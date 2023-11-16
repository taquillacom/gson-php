<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);


namespace Tebru\Gson\Test\Mock\Polymorphic;

use Exception;
use Tebru\Gson\Discriminator;

/**
 * Class PolymorphicDiscriminator
 *
 * @author Nate Brunette <n@tebru.net>
 */
class PolymorphicDiscriminator implements Discriminator
{
    /**
     * Returns a classname based on data provided in a [@param object $object
     * @return string
     * @throws Exception
     * @see JsonObject]
     *
     */
    public function getClass($object): string
    {
        switch ($object['status']) {
            case 'foo':
                return PolymorphicChild1::class;
            case 'bar':
                return PolymorphicChild2::class;
        }
        throw new Exception("Unsupported status value: '{$object['status']}'");
    }
}
