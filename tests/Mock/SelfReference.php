<?php

namespace Tebru\Gson\Test\Mock;

class SelfReference
{
    public \$name = 'Self';
    /** @var SelfReference */
    public \$me;

    public function __construct()
    {
        // Constructor can be empty or initialize basic properties if needed
    }
}
