<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace Tebru\Gson\Context;

/**
 * Class WriterContext
 *
 * Runtime context that can be used during reading
 *
 * @author Nate Brunette <n@tebru.net>
 */
class WriterContext extends Context
{
    /**
     * If nulls should be serialized
     *
     * @var bool
     */
    private $serializeNull = false;

    /**
     * @var array
     */
    private $visiting = [];

    /**
     * If we should serialize null
     *
     * @return bool
     */
    public function serializeNull(): bool
    {
        return $this->serializeNull;
    }

    /**
     * Set if nulls should be serialized
     *
     * @param bool $serializeNull
     * @return Context
     */
    public function setSerializeNull(bool $serializeNull): Context
    {
        $this->serializeNull = $serializeNull;

        return $this;
    }

    /**
     * Check if we're currently visiting an object
     *
     * @param object $object
     * @return bool
     */
    public function isVisiting(object $object): bool
    {
        $hash = spl_object_hash($object);
        return isset($this->visiting[$hash]);
    }

    /**
     * Add an object to the visiting array
     *
     * @param object $object
     */
    public function pushVisiting(object $object): void
    {
        $hash = spl_object_hash($object);
        $this->visiting[$hash] = true;
    }

    /**
     * Remove an object from the visiting array
     *
     * @param object $object
     */
    public function popVisiting(object $object): void
    {
        $hash = spl_object_hash($object);
        unset($this->visiting[$hash]);
    }
}
