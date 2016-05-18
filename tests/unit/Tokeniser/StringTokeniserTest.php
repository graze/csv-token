<?php

namespace Graze\CsvToken\Test\Unit;

use Graze\CsvToken\Csv\CsvConfiguration;
use Graze\CsvToken\Test\TestCase;
use Graze\CsvToken\Tokeniser\StreamTokeniser;
use Graze\CsvToken\Tokeniser\StringTokeniser;
use Graze\CsvToken\Tokeniser\TokeniserInterface;

class StringTokeniserTest extends TestCase
{
    public function testInstanceOf()
    {
        $tokeniser = new StringTokeniser(new CsvConfiguration(), '"test","data"');

        static::assertInstanceOf(StreamTokeniser::class, $tokeniser);
        static::assertInstanceOf(TokeniserInterface::class, $tokeniser);
    }
}
