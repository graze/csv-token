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

namespace Graze\CsvToken\Test\Unit;

use Graze\CsvToken\Csv\CsvConfiguration;
use Graze\CsvToken\Csv\CsvConfigurationInterface;
use Graze\CsvToken\Test\TestCase;
use Graze\CsvToken\Tokeniser\StreamTokeniser;
use Graze\CsvToken\Tokeniser\Token;
use GuzzleHttp\Psr7\Stream;

class StreamTokeniserTest extends TestCase
{
    /**
     * @dataProvider tokeniserTestData
     *
     * @param CsvConfigurationInterface $config
     * @param string                    $csv
     * @param array                     $tokens
     */
    public function testTokeniser(CsvConfigurationInterface $config, $csv, array $tokens)
    {
        $tokeniser = new StreamTokeniser($config, $this->getStream($csv));

        /** @var Token[] $actual */
        $actual = iterator_to_array($tokeniser->getTokens());

        $tokensOnly = array_map(function (Token $token) {
            return [$token->getType(), $token->getContent()];
        }, $actual);

        static::assertEquals($tokens, $tokensOnly);

        $count = count($actual);
        for ($i = 1; $i < $count; $i++) {
            static::assertEquals(
                $actual[$i]->getPosition(),
                $actual[$i - 1]->getPosition() + $actual[$i - 1]->getLength(),
                "There should be no missing gaps in the data"
            );
        }
    }

    /**
     * @param string $string
     *
     * @return Stream
     */
    private function getStream($string)
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $string);
        rewind($stream);
        return new Stream($stream);
    }

    /**
     * @return array
     */
    public function tokeniserTestData()
    {
        return [
            [
                new CsvConfiguration(),
                '"some","test","","data"',
                [
                    [Token::T_QUOTE, '"'],
                    [Token::T_CONTENT, 'some'],
                    [Token::T_QUOTE, '"'],
                    [Token::T_DELIMITER, ','],
                    [Token::T_QUOTE, '"'],
                    [Token::T_CONTENT, 'test'],
                    [Token::T_QUOTE, '"'],
                    [Token::T_DELIMITER, ','],
                    [Token::T_QUOTE, '"'],
                    [Token::T_QUOTE, '"'],
                    [Token::T_DELIMITER, ','],
                    [Token::T_QUOTE, '"'],
                    [Token::T_CONTENT, 'data'],
                    [Token::T_QUOTE, '"'],
                ],
            ],
            [
                new CsvConfiguration(),
                'some',
                [
                    [Token::T_CONTENT, 'some'],
                ],
            ],
            [
                new CsvConfiguration([
                    CsvConfiguration::OPTION_DOUBLE_QUOTE => true,
                ]),
                '"end""","""start","""both""","","""",""""""""',
                [
                    [Token::T_QUOTE, '"'],
                    [Token::T_CONTENT, 'end'],
                    [Token::T_DOUBLE_QUOTE, '""'],
                    [Token::T_QUOTE, '"'],
                    [Token::T_DELIMITER, ','],
                    [Token::T_QUOTE, '"'],
                    [Token::T_DOUBLE_QUOTE, '""'],
                    [Token::T_CONTENT, 'start'],
                    [Token::T_QUOTE, '"'],
                    [Token::T_DELIMITER, ','],
                    [Token::T_QUOTE, '"'],
                    [Token::T_DOUBLE_QUOTE, '""'],
                    [Token::T_CONTENT, 'both'],
                    [Token::T_DOUBLE_QUOTE, '""'],
                    [Token::T_QUOTE, '"'],
                    [Token::T_DELIMITER, ','],
                    [Token::T_QUOTE, '"'],
                    [Token::T_QUOTE, '"'],
                    [Token::T_DELIMITER, ','],
                    [Token::T_QUOTE, '"'],
                    [Token::T_DOUBLE_QUOTE, '""'],
                    [Token::T_QUOTE, '"'],
                    [Token::T_DELIMITER, ','],
                    [Token::T_QUOTE, '"'],
                    [Token::T_DOUBLE_QUOTE, '""'],
                    [Token::T_DOUBLE_QUOTE, '""'],
                    [Token::T_DOUBLE_QUOTE, '""'],
                    [Token::T_QUOTE, '"'],
                ],
            ],
            [
                new CsvConfiguration(),
                '"some",test,"with \" escape","\\\\"',
                [
                    [Token::T_QUOTE, '"'],
                    [Token::T_CONTENT, 'some'],
                    [Token::T_QUOTE, '"'],
                    [Token::T_DELIMITER, ','],
                    [Token::T_CONTENT, 'test'],
                    [Token::T_DELIMITER, ','],
                    [Token::T_QUOTE, '"'],
                    [Token::T_CONTENT, 'with '],
                    [Token::T_ESCAPE, '\\'],
                    [Token::T_CONTENT, '" escape'],
                    [Token::T_QUOTE, '"'],
                    [Token::T_DELIMITER, ','],
                    [Token::T_QUOTE, '"'],
                    [Token::T_ESCAPE, '\\'],
                    [Token::T_CONTENT, '\\'],
                    [Token::T_QUOTE, '"'],
                ],
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
                [
                    [Token::T_QUOTE, "'"],
                    [Token::T_CONTENT, 'some'],
                    [Token::T_QUOTE, "'"],
                    [Token::T_DELIMITER, '|'],
                    [Token::T_CONTENT, 'text'],
                    [Token::T_DELIMITER, '|'],
                    [Token::T_QUOTE, "'"],
                    [Token::T_ESCAPE, '\\'],
                    [Token::T_CONTENT, "'here"],
                    [Token::T_QUOTE, "'"],
                    [Token::T_DELIMITER, '|'],
                    [Token::T_NULL, '\\N'],
                    [Token::T_DELIMITER, '|'],
                    [Token::T_QUOTE, "'"],
                    [Token::T_CONTENT, 'with'],
                    [Token::T_DOUBLE_QUOTE, "''"],
                    [Token::T_CONTENT, 'quotes'],
                    [Token::T_QUOTE, "'"],
                    [Token::T_NEW_LINE, '---'],
                    [Token::T_QUOTE, "'"],
                    [Token::T_CONTENT, 'another'],
                    [Token::T_QUOTE, "'"],
                    [Token::T_DELIMITER, '|'],
                    [Token::T_QUOTE, "'"],
                    [Token::T_CONTENT, 'line'],
                    [Token::T_QUOTE, "'"],
                ],
            ],
            [
                new CsvConfiguration(),
                '"some","new' . "\n" . 'line",with\\' . "\n" . 'escaped,"in\\' . "\n" . 'quotes"',
                [
                    [Token::T_QUOTE, '"'],
                    [Token::T_CONTENT, 'some'],
                    [Token::T_QUOTE, '"'],
                    [Token::T_DELIMITER, ','],
                    [Token::T_QUOTE, '"'],
                    [Token::T_CONTENT, 'new' . "\n" . 'line'],
                    [Token::T_QUOTE, '"'],
                    [Token::T_DELIMITER, ','],
                    [Token::T_CONTENT, 'with'],
                    [Token::T_ESCAPE, '\\'],
                    [Token::T_CONTENT, "\n" . 'escaped'],
                    [Token::T_DELIMITER, ','],
                    [Token::T_QUOTE, '"'],
                    [Token::T_CONTENT, 'in'],
                    [Token::T_ESCAPE, '\\'],
                    [Token::T_CONTENT, "\n" . 'quotes'],
                    [Token::T_QUOTE, '"'],
                ],
            ],
            [
                new CsvConfiguration(),
                '',
                [],
            ],
            [
                new CsvConfiguration(),
                "한국말\n조선말,한국말",
                [
                    [Token::T_CONTENT, '한국말'],
                    [Token::T_NEW_LINE, "\n"],
                    [Token::T_CONTENT, '조선말'],
                    [Token::T_DELIMITER, ','],
                    [Token::T_CONTENT, '한국말'],
                ],
            ],
            [
                new CsvConfiguration([]),
                'text\\Nthing,\\Nstart,end\\N,\\N,"\\N"',
                [
                    [Token::T_CONTENT, 'text'],
                    [Token::T_NULL, '\\N'],
                    [Token::T_CONTENT, 'thing'],
                    [Token::T_DELIMITER, ','],
                    [Token::T_NULL, '\N'],
                    [Token::T_CONTENT, 'start'],
                    [Token::T_DELIMITER, ','],
                    [Token::T_CONTENT, 'end'],
                    [Token::T_NULL, '\\N'],
                    [Token::T_DELIMITER, ','],
                    [Token::T_NULL, '\\N'],
                    [Token::T_DELIMITER, ','],
                    [Token::T_QUOTE, '"'],
                    [Token::T_ESCAPE, '\\'],
                    [Token::T_CONTENT, 'N'],
                    [Token::T_QUOTE, '"'],
                ],
            ],
        ];
    }
}
