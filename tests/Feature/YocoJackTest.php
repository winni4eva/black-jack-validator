<?php

use PHPUnit\Framework\TestCase;
use Winnipass\YocoJackValidator;

class YocoJackTest extends TestCase 
{
    public function testAllGamesAreWonByTheExpectedPlayer()
    {
        $result = (new YocoJackValidator)->validate();

        $this->assertTrue($result);
    }
}