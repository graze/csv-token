<?php
/**
 * This file is part of graze/csv-token
 *
 * Copyright (c) 2016 Nature Delivered Ltd. <https://www.graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license https://github.com/graze/csv-token/blob/master/LICENSE.md
 * @link    https://github.com/graze/csv-token
 */

namespace Graze\CsvToken\Test\Unit\ValueParser;

use Graze\CsvToken\Test\TestCase;
use Graze\CsvToken\ValueParser\BoolValueParser;

class BoolValueParserTest extends TestCase
{
    /** @var BoolValueParser */
    private $parser;

    public function setUp()
    {
        $this->parser = new BoolValueParser();
    }

    /**
     * @dataProvider getCanParseData
     *
     * @param string $string
     * @param bool   $expected
     */
    public function testCanParse($string, $expected)
    {
        static::assertEquals($expected, $this->parser->canParseValue($string));
    }

    /**
     * @return array
     */
    public function getCanParseData()
    {
        return [
            ['true', true],
            ['false', true],
            ['yes', false],
            ['no', false],
            ['1', false],
            ['0', false],
            ['bool', false],
        ];
    }

    /**
     * @dataProvider getParseData
     *
     * @param string $string
     * @param bool   $expected
     */
    public function testParse($string, $expected)
    {
        static::assertEquals($expected, $this->parser->parseValue($string));
    }

    /**
     * @return array
     */
    public function getParseData()
    {
        return [
            ['true', true],
            ['false', false],
        ];
    }
}
