<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

declare(strict_types=1);

namespace Tebru\Gson\TypeAdapter;

use Tebru\AnnotationReader\AnnotationCollection;
use Tebru\Gson\Annotation\ExclusionCheck;
use Tebru\Gson\Annotation\JsonAdapter;
use Tebru\Gson\Context\ReaderContext;
use Tebru\Gson\Context\WriterContext;
use Tebru\Gson\Internal\Data\Property;
use Tebru\Gson\Internal\Data\PropertyCollection;
use Tebru\Gson\Internal\DefaultClassMetadata;
use Tebru\Gson\Internal\DefaultDeserializationExclusionData;
use Tebru\Gson\Internal\DefaultSerializationExclusionData;
use Tebru\Gson\Internal\Excluder;
use Tebru\Gson\Internal\ObjectConstructor;
use Tebru\Gson\Internal\ObjectConstructor\CreateFromInstance;
use Tebru\Gson\Internal\ObjectConstructorAware;
use Tebru\Gson\Internal\ObjectConstructorAwareTrait;
use Tebru\Gson\Internal\TypeAdapterProvider;
use Tebru\Gson\TypeAdapter;
use TypeError;

/**
 * Class ReadOnlyReflectionTypeAdapter
 *
 * Uses reflected class properties to write object and constructor to read object.
 *
 */
class ReadOnlyReflectionTypeAdapter extends ReflectionTypeAdapter {
    /**
     * Read the next value, convert it to its type and return it
     *
     * @param array $value
     * @param ReaderContext $context
     * @return object
     */
    public function read($value, ReaderContext $context)
    {
        if ($this->skipDeserialize || $value === null) {
            return null;
        }

        $propertyValues = [];
        $enableScalarAdapters = $context->enableScalarAdapters();
        foreach ($value as $name => $item) {
            $property = $this->propertyCache[$name] ?? ($this->propertyCache[$name] = ($this->properties->elements[$name] ?? null));

            if ($property === null || $property->skipDeserialize) {
                continue;
            }

            $checkProperty = $this->hasPropertyDeserializationStrategies
                             && (!$this->requireExclusionCheck || ($this->requireExclusionCheck && $property->annotations->get(ExclusionCheck::class) !== null));
            if ($checkProperty && $this->excluder->excludePropertyByDeserializationStrategy($property)) {
                continue;
            }

            if (!$enableScalarAdapters && $property->isScalar) {
                $propertyValues[$name] = $item;
                continue;
            }

            $adapter = $this->adapters[$name] ?? $this->getAdapter($property);

            $propertyValues[$property->getName()] = $adapter->read($item, $context);
        }


        $object = $this->objectConstructor->construct($propertyValues);
        $classExclusionCheck = $this->hasClassDeserializationStrategies
            && (!$this->requireExclusionCheck || ($this->requireExclusionCheck && $this->classAnnotations->get(ExclusionCheck::class) !== null));
        $propertyExclusionCheck = $this->hasPropertyDeserializationStrategies
            && (!$this->requireExclusionCheck || ($this->requireExclusionCheck && $this->hasPropertyExclusionCheck));
        $exclusionData = $classExclusionCheck || $propertyExclusionCheck
            ? new DefaultDeserializationExclusionData(clone $object, $context)
            : null;

        if ($classExclusionCheck && $exclusionData) {
            $this->excluder->applyClassDeserializationExclusionData($exclusionData);

            if ($this->excluder->excludeClassByDeserializationStrategy($this->classMetadata)) {
                return null;
            }
        }

        if ($propertyExclusionCheck && $exclusionData) {
            $this->excluder->applyPropertyDeserializationExclusionData($exclusionData);
        }

        if ($this->classVirtualProperty !== null) {
            $value = array_shift($value);
        }

        return $object;
    }

    /**
     * Write the value to the writer for the type
     *
     * @param object $value
     * @param WriterContext $context
     * @return array|null
     */
    public function write($value, WriterContext $context): ?array
    {
        if ($this->skipSerialize || $value === null) {
            return null;
        }

        $classExclusionCheck = $this->hasClassSerializationStrategies
            && (!$this->requireExclusionCheck || ($this->requireExclusionCheck && $this->classAnnotations->get(ExclusionCheck::class) !== null));
        $propertyExclusionCheck = $this->hasPropertySerializationStrategies
            && (!$this->requireExclusionCheck || ($this->requireExclusionCheck && $this->hasPropertyExclusionCheck));
        $exclusionData = $classExclusionCheck || $propertyExclusionCheck
            ? new DefaultSerializationExclusionData($value, $context)
            : null;

        if ($classExclusionCheck && $exclusionData) {
            $this->excluder->applyClassSerializationExclusionData($exclusionData);

            if ($this->excluder->excludeClassBySerializationStrategy($this->classMetadata)) {
                return null;
            }
        }

        if ($propertyExclusionCheck && $exclusionData) {
            $this->excluder->applyPropertySerializationExclusionData($exclusionData);
        }

        $enableScalarAdapters = $context->enableScalarAdapters();
        $serializeNull = $context->serializeNull();
        $result = [];

        /** @var Property $property */
        foreach ($this->properties as $property) {
            if ($property->skipSerialize) {
                continue;
            }

            $serializedName = $property->serializedName;
            $checkProperty = $this->hasPropertySerializationStrategies
                && (!$this->requireExclusionCheck || ($this->requireExclusionCheck && $property->annotations->get(ExclusionCheck::class) !== null));
            if ($checkProperty && $this->excluder->excludePropertyBySerializationStrategy($property)) {
                continue;
            }

            if (!$enableScalarAdapters && $property->isScalar) {
                $propertyValue = $property->getterStrategy->get($value);
                if ($serializeNull || $propertyValue !== null) {
                    $result[$serializedName] = $propertyValue;
                }
                continue;
            }

            $adapter = $this->adapters[$serializedName] ?? $this->getAdapter($property);
            $propertyValue = $adapter->write($property->getterStrategy->get($value), $context);
            if ($serializeNull || $propertyValue !== null) {
                $result[$serializedName] = $propertyValue;
            }
        }

        if ($this->classVirtualProperty !== null) {
            $result = [$this->classVirtualProperty => $result];
        }

        return $result;
    }

    /**
     * Get the next type adapter
     *
     * @param Property $property
     * @return TypeAdapter
     */
    protected function getAdapter(Property $property): TypeAdapter
    {
        /** @var JsonAdapter $jsonAdapterAnnotation */
        $jsonAdapterAnnotation = $property->annotations->get(JsonAdapter::class);
        $adapter = null === $jsonAdapterAnnotation
            ? $this->typeAdapterProvider->getAdapter($property->type)
            : $this->typeAdapterProvider->getAdapterFromAnnotation($property->type, $jsonAdapterAnnotation);
        $this->adapters[$property->serializedName] = $adapter;

        return $adapter;
    }
}
