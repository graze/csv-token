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

namespace Graze\CsvToken\Test\Integration;

use Graze\CsvToken\Csv\CsvConfiguration;
use Graze\CsvToken\Csv\CsvConfigurationInterface;
use Graze\CsvToken\Parser;
use Graze\CsvToken\Test\TestCase;
use Graze\CsvToken\Tokeniser\StringTokeniser;
use Graze\CsvToken\ValueParser\BoolValueParser;
use Graze\CsvToken\ValueParser\NumberValueParser;
use RuntimeException;

class ParserTest extends TestCase
{
    /**
     * @dataProvider parseData
     *
     * @param CsvConfigurationInterface $config
     * @param string                    $csv
     * @param array                     $valueParsers
     * @param array                     $expected
     */
    public function testParse(CsvConfigurationInterface $config, $csv, array $valueParsers, array $expected)
    {
        $tokeniser = new StringTokeniser($config, $csv);
        $parser = new Parser($valueParsers);

        $results = iterator_to_array($parser->parse($tokeniser->getTokens()));

        static::assertEquals($expected, $results);
    }

    /**
     * @return array
     */
    public function parseData()
    {
        return [
            [
                new CsvConfiguration(),
                '"some",\\N,"new' . "\n" . 'line",with\\' . "\n" . 'escaped,"in\\' . "\n" . 'quotes","\\\\"',
                [],
                [
                    ['some', null, "new\nline", "with\nescaped", "in\nquotes", '\\'],
                ],
            ],
            [
                new CsvConfiguration([
                    CsvConfiguration::OPTION_DOUBLE_QUOTE => true,
                ]),
                '"end""","""start","""both""","",""""',
                [],
                [['end"', '"start', '"both"', '', '"']],
            ],
            [
                new CsvConfiguration([
                    CsvConfiguration::OPTION_DELIMITER    => '|',
                    CsvConfiguration::OPTION_QUOTE        => "'",
                    CsvConfiguration::OPTION_ESCAPE       => '\\',
                    CsvConfiguration::OPTION_DOUBLE_QUOTE => true,
                    CsvConfiguration::OPTION_NEW_LINES    => ['---'],
                    CsvConfiguration::OPTION_NULL         => '\\N',
                ]),
                "'some'|text|'\\'here'|\\N|'with''quotes'---'another'|'line'",
                [],
                [
                    ['some', 'text', "'here", null, "with'quotes"],
                    ['another', 'line'],
                ],
            ],
            [
                new CsvConfiguration([
                    CsvConfiguration::OPTION_NULL => 'null',
                ]),
                '"text",1.2,false,true,12,2.3e-34,-2341,null,pants',
                [
                    new BoolValueParser(),
                    new NumberValueParser(),
                ],
                [
                    ['text', 1.2, false, true, 12, 2.3e-34, -2341, null, 'pants'],
                ],
            ],
            [
                new CsvConfiguration(),
                '',
                [],
                [],
            ],
            [
                new CsvConfiguration(),
                'text\\Nthing,\\Nstart,end\\N,\\N,"\\N"',
                [],
                [
                    ['text\\Nthing', '\\Nstart', 'end\\N', null, 'N'],
                ],
            ],
            [
                new CsvConfiguration(),
                "한국말\n조선말,한국말",
                [],
                [
                    ['한국말'],
                    ['조선말', '한국말'],
                ],
            ],
            [
                new CsvConfiguration(),
                '"1","2","3"' . "\n",
                [],
                [
                    ['1', '2', '3'],
                ],
            ],
            [ // no quote and double quote should do nothing
                new CsvConfiguration([
                    CsvConfiguration::OPTION_QUOTE        => '',
                    CsvConfiguration::OPTION_DOUBLE_QUOTE => true,
                ]),
                'text,things"here,and\,here',
                [],
                [
                    ['text', 'things"here', 'and,here'],
                ],
            ],
            [
                new CsvConfiguration([
                    CsvConfiguration::OPTION_ESCAPE => '',
                ]),
                '"text","here","and\,here"',
                [],
                [
                    ['text', 'here', 'and\,here'],
                ],
            ],
            [
                new CsvConfiguration([
                    CsvConfiguration::OPTION_DELIMITER    => '|',
                    CsvConfiguration::OPTION_ESCAPE       => '~',
                    CsvConfiguration::OPTION_QUOTE        => '`',
                    CsvConfiguration::OPTION_NULL         => 'null',
                    CsvConfiguration::OPTION_DOUBLE_QUOTE => true,
                ]),
                '`string`|`other,thing`|some stuff|escaped ~\\n|``` all the `` quotes `|null',
                [],
                [['string', 'other,thing', 'some stuff', 'escaped \n', '` all the ` quotes ', null]],
            ],
        ];
    }

    /**
     * @dataProvider parseExceptionsData
     *
     * @param string $csv
     * @param string $exception
     */
    public function testParseExceptions($csv, $exception)
    {
        $tokeniser = new StringTokeniser(new CsvConfiguration(), $csv);
        $parser = new Parser();

        static::expectException($exception);

        iterator_to_array($parser->parse($tokeniser->getTokens()));
    }

    /**
     * @return array
     */
    public function parseExceptionsData()
    {
        return [
            ['"string"stuff,things', RuntimeException::class], // extra text after a closing quote
            ['"string', RuntimeException::class], // no closing quote
        ];
    }
}
