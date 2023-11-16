<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Test\Mock\Unit\Internal\AccessorStrategy\SetByClosureTest;

use AllowDynamicProperties;

/**
 * Class SetByClosureTestMock
 *
 * @author Nate Brunette <n@tebru.net>
 */
#[AllowDynamicProperties]
class SetByClosureTestMock
{
    private $foo = 'bar';
}
