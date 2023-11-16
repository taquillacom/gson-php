<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Test\Mock;

use AllowDynamicProperties;

/**
 * Class GsonObjectMock
 *
 * @author Nate Brunette <n@tebru.net>
 */
#[AllowDynamicProperties]
class GsonObjectMock implements GsonObjectMockable
{
    private $foo;

    public function __construct($foo)
    {
        $this->foo = $foo;
    }

    public function getFoo()
    {
        return $this->foo;
    }

    public function setFoo($foo)
    {
        $this->foo = $foo;
    }
}
