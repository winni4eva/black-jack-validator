<?php

use PHPUnit\Framework\TestCase;

class ExampleUnitTest extends TestCase 
{
    public function testMultipleTwoNumbers()
    {
        $result = 1 + 6; 
        $this->assertEquals(7, $result);
    }
}