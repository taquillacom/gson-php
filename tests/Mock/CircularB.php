<?php

namespace Tebru\Gson\Test\Mock;

class CircularB
{
    public \$name = 'ObjectB';
    /** @var CircularA */
    public \$parentA; // This creates the circular reference

    public function __construct()
    {
        // Constructor can be empty or initialize basic properties if needed
    }
}
