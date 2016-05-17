<?php

namespace Graze\CsvToken\Test\Unit\ValueParser;

use Graze\CsvToken\Test\TestCase;
use Graze\CsvToken\ValueParser\Value;
use RuntimeException;

class ValueTest extends TestCase
{
    public function testAddContentWhenQuotesHaveBeenClosedWillThrowAnException()
    {
        $value = new Value();

        $value->setInQuotes(true);
        $value->addContent('some stuff');
        $value->setInQuotes(false);

        static::assertEquals('some stuff', $value->getValue());

        static::expectException(RuntimeException::class);
        $value->addContent('more content');
    }
}
