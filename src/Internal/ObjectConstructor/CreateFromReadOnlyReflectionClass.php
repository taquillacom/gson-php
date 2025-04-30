<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

declare(strict_types=1);

namespace Tebru\Gson\Internal\ObjectConstructor;

use InvalidArgumentException;
use ReflectionClass;
use Tebru\Gson\Annotation\ExclusionCheck;
use Tebru\Gson\Internal\Data\PropertyCollection;
use Tebru\Gson\Internal\ObjectConstructor;
use Tebru\Gson\Internal\ObjectConstructorAware;

/**
 * Class CreateFromReadOnlyReflectionClass
 *
 * Instantiate a new class using reflection.  This is necessary if the class constructor
 * has required arguments, but an [@see InstanceCreator] is not registered.
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class CreateFromReadOnlyReflectionClass implements ObjectConstructor
{
    private string $className;
    private ?array $parameters = null;

    /**
     * Constructor
     *
     * @param string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * Returns the instantiated object
     *
     * @return object
     */
    public function construct($values = [])
    {
        if ($this->parameters === null) {
            $reflectionClass = new ReflectionClass($this->className);
            $constructor     = $reflectionClass->getConstructor();
            $this->parameters      = $constructor->getParameters();
        }

        $constructorArgs = [];
        foreach ($this->parameters as $parameter) {
            $paramName = $parameter->getName();

            if (array_key_exists($paramName, $values)) {
                $constructorArgs[] = $values[$paramName];
            } else if ($parameter->isDefaultValueAvailable()) {
                $constructorArgs[] = $parameter->getDefaultValue();
            } else {
                // Si no hay valor y no hay valor por defecto, lanzar excepción
                throw new InvalidArgumentException("Falta el parámetro requerido '$paramName' en los datos JSON");
            }
        }

        return new $this->className(...$constructorArgs);
    }
}
