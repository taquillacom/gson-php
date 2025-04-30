<?php

namespace Tebru\Gson\Test\Mock;

readonly class ReadOnlyObject {
    public function __construct(
        public string $foo
    ) {}
}