<?php

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase 
{
    public function testMultipleTwoNumbers()
    {
        $result = 1 + 6; 
        $this->assertEquals(7, $result);
    }
}