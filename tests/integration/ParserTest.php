<?php

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

        $results = array_map('iterator_to_array', $results);

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
                    CsvConfiguration::OPTION_NEW_LINE     => '---',
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
