<?php

namespace Graze\CsvToken\Test\Unit\ValueParser;

use Graze\CsvToken\Test\TestCase;
use Graze\CsvToken\ValueParser\NumberValueParser;

class NumberValueParserTest extends TestCase
{
    /** @var NumberValueParser */
    private $parser;

    public function setUp()
    {
        $this->parser = new NumberValueParser();
    }

    /**
     * @dataProvider canParseData
     *
     * @param string $string
     * @param bool   $expected
     */
    public function testCanParseValue($string, $expected)
    {
        static::assertEquals($expected, $this->parser->canParseValue($string));
    }

    /**
     * @return array
     */
    public function canParseData()
    {
        return [
            ['1', true],
            ['1.2', true],
            ['-1', true],
            ['string', false],
            ['null', false],
            ['1.2e-12', true],
            ['4763894692379379723479327842', true],
            ['0.000000000001231312', true],
            ['some string', false],
            ['string12345', false],
            ['12345string', false],
            ['123,123,123.01', false],
        ];
    }

    /**
     * @dataProvider parseData
     *
     * @param string $string
     * @param mixed  $expected
     */
    public function testParseValue($string, $expected)
    {
        static::assertEquals($expected, $this->parser->parseValue($string));
    }

    /**
     * @return array
     */
    public function parseData()
    {
        return [
            ['1', true],
            ['1.2', 1.2],
            ['-1', -1],
            ['1.2e-12', 1.2e-12],
            ['4763894692379379723479327842', 4763894692379379723479327842],
            ['0.000000000001231312', 0.000000000001231312],
            ['123,123,123.01', 123123123.01],
        ];
    }
}
