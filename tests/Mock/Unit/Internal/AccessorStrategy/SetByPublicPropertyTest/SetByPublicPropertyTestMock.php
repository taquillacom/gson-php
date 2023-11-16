<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Test\Mock\Unit\Internal\AccessorStrategy\SetByPublicPropertyTest;

use AllowDynamicProperties;

/**
 * Class SetByPublicPropertyTestMock
 *
 * @author Nate Brunette <n@tebru.net>
 */
#[AllowDynamicProperties]
class SetByPublicPropertyTestMock
{
    public $foo;
}
