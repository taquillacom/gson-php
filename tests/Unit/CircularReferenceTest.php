<?php

namespace Tebru\Gson\Test\Unit;

use Tebru\Gson\Gson;
use Tebru\Gson\Test\Mock\CircularA;
use Tebru\Gson\Test\Mock\CircularB;
use Tebru\Gson\Test\Mock\SelfReference;
use Tebru\Gson\Test\Unit\TypeAdapter\TypeAdapterTestCase;

class CircularReferenceTest extends TypeAdapterTestCase
{
    private \$gson;

    protected function setUp(): void
    {
        parent::setUp();
        \$this->gson = Gson::builder()->build();
    }

    public function testCircularReferenceAB(): void
    {
        \$objA = new CircularA();
        \$objB = new CircularB();

        \$objA->childB = \$objB;
        \$objB->parentA = \$objA; // Circular reference

        \$json = \$this->gson->toJson(\$objA);
        \$expected = '{"name":"ObjectA","childB":{"name":"ObjectB","parentA":null}}';
        \$this->assertJsonStringEqualsJsonString(\$expected, \$json);
    }
    
    public function testCircularReferenceABWithNullSerialization(): void
    {
        \$this->gson = Gson::builder()->serializeNulls()->build();
        \$objA = new CircularA();
        \$objB = new CircularB();

        \$objA->childB = \$objB;
        \$objB->parentA = \$objA; // Circular reference

        \$json = \$this->gson->toJson(\$objA);
        \$expected = '{"name":"ObjectA","childB":{"name":"ObjectB","parentA":null}}';
        \$this->assertJsonStringEqualsJsonString(\$expected, \$json);
    }

    public function testSelfReference(): void
    {
        \$obj = new SelfReference();
        \$obj->me = \$obj; // Self reference

        \$json = \$this->gson->toJson(\$obj);
        \$expected = '{"name":"Self","me":null}';
        \$this->assertJsonStringEqualsJsonString(\$expected, \$json);
    }
    
    public function testSelfReferenceWithNullSerialization(): void
    {
        \$this->gson = Gson::builder()->serializeNulls()->build();
        \$obj = new SelfReference();
        \$obj->me = \$obj; // Self reference

        \$json = \$this->gson->toJson(\$obj);
        \$expected = '{"name":"Self","me":null}';
        \$this->assertJsonStringEqualsJsonString(\$expected, \$json);
    }

    public function testObjectAppearingMultipleTimesWithoutCircularReference(): void
    {
        \$objA = new CircularA(); // Reusing CircularA for convenience
        \$objA->name = "SharedObject";
        
        \$container = new \stdClass();
        \$container->prop1 = \$objA;
        \$container->prop2 = \$objA;

        \$json = \$this->gson->toJson(\$container);
        // Expect the object to be fully serialized in both places
        \$expected = '{"prop1":{"name":"SharedObject","childB":null},"prop2":{"name":"SharedObject","childB":null}}';
        \$this->assertJsonStringEqualsJsonString(\$expected, \$json);
    }
}
