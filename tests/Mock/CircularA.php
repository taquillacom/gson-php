<?php

namespace Tebru\Gson\Test\Mock;

class CircularA
{
    public \$name = 'ObjectA';
    /** @var CircularB */
    public \$childB;

    public function __construct()
    {
        // Constructor can be empty or initialize basic properties if needed
    }
}
