<?php

use PHPUnit\Framework\TestCase;
use Winnipass\YocoJackValidator;

class YocoJackTest extends TestCase 
{
    public function testAllGamesAreWonByTheExpectedPlayer()
    {
        $jsonFilePath = __DIR__.'../../tests.json';
        $result = (new YocoJackValidator($jsonFilePath))->validate();

        $this->assertTrue($result);
    }
}