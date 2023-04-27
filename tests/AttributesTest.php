<?php

use Intimation\Catalyst\Attributes;
use PHPUnit\Framework\TestCase;

class AttributesTest extends TestCase
{
    public function test_that_the_correct_classes_are_generated()
    {
        $this->assertEquals(
            'class="class-one class-two class-three"',
            Attributes::class([
                'class-one',
                'class-two class-three' => true,
                'class-four' => false,
            ])
        );

        $this->assertEquals(
            'class="class-one class-four"',
            Attributes::class([
                'class-one',
                'class-two class-three' => false,
                'class-four' => true,
            ])
        );
    }
}
