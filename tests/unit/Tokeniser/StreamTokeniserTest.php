<?php

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

        $actual = iterator_to_array($tokeniser->getTokens());

        $tokensOnly = array_map(function (Token $token) {
            return [$token->getType(), $token->getPosition(), $token->getLength(), $token->getContent()];
        }, $actual);

        static::assertEquals($tokens, $tokensOnly);
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
                    [Token::T_QUOTE, 0, 1, '"'],
                    [Token::T_CONTENT, 1, 4, 'some'],
                    [Token::T_QUOTE, 5, 1, '"'],
                    [Token::T_DELIMITER, 6, 1, ','],
                    [Token::T_QUOTE, 7, 1, '"'],
                    [Token::T_CONTENT, 8, 4, 'test'],
                    [Token::T_QUOTE, 12, 1, '"'],
                    [Token::T_DELIMITER, 13, 1, ','],
                    [Token::T_QUOTE, 14, 1, '"'],
                    [Token::T_QUOTE, 15, 1, '"'],
                    [Token::T_DELIMITER, 16, 1, ','],
                    [Token::T_QUOTE, 17, 1, '"'],
                    [Token::T_CONTENT, 18, 4, 'data'],
                    [Token::T_QUOTE, 22, 1, '"'],
                ],
            ],
            [
                new CsvConfiguration(),
                'some',
                [
                    [Token::T_CONTENT, 0, 4, 'some'],
                ],
            ],
            [
                new CsvConfiguration(),
                '"some",test,"with \" escape"',
                [
                    [Token::T_QUOTE, 0, 1, '"'],
                    [Token::T_CONTENT, 1, 4, 'some'],
                    [Token::T_QUOTE, 5, 1, '"'],
                    [Token::T_DELIMITER, 6, 1, ','],
                    [Token::T_CONTENT, 7, 4, 'test'],
                    [Token::T_DELIMITER, 11, 1, ','],
                    [Token::T_QUOTE, 12, 1, '"'],
                    [Token::T_CONTENT, 13, 5, 'with '],
                    [Token::T_ESCAPE, 18, 1, '\\'],
                    [Token::T_QUOTE, 19, 1, '"'],
                    [Token::T_CONTENT, 20, 7, ' escape'],
                    [Token::T_QUOTE, 27, 1, '"'],
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
                    [Token::T_QUOTE, 0, 1, "'"],
                    [Token::T_CONTENT, 1, 4, 'some'],
                    [Token::T_QUOTE, 5, 1, "'"],
                    [Token::T_DELIMITER, 6, 1, '|'],
                    [Token::T_CONTENT, 7, 4, 'text'],
                    [Token::T_DELIMITER, 11, 1, '|'],
                    [Token::T_QUOTE, 12, 1, "'"],
                    [Token::T_ESCAPE, 13, 1, '\\'],
                    [Token::T_QUOTE, 14, 1, "'"],
                    [Token::T_CONTENT, 15, 4, "here"],
                    [Token::T_QUOTE, 19, 1, "'"],
                    [Token::T_DELIMITER, 20, 1, '|'],
                    [Token::T_NULL, 21, 2, '\\N'],
                    [Token::T_DELIMITER, 23, 1, '|'],
                    [Token::T_QUOTE, 24, 1, "'"],
                    [Token::T_CONTENT, 25, 4, 'with'],
                    [Token::T_DOUBLE_QUOTE, 29, 2, "''"],
                    [Token::T_CONTENT, 31, 6, 'quotes'],
                    [Token::T_QUOTE, 37, 1, "'"],
                    [Token::T_NEW_LINE, 38, 3, '---'],
                    [Token::T_QUOTE, 41, 1, "'"],
                    [Token::T_CONTENT, 42, 7, 'another'],
                    [Token::T_QUOTE, 49, 1, "'"],
                    [Token::T_DELIMITER, 50, 1, '|'],
                    [Token::T_QUOTE, 51, 1, "'"],
                    [Token::T_CONTENT, 52, 4, 'line'],
                    [Token::T_QUOTE, 56, 1, "'"],
                ],
            ],
            [
                new CsvConfiguration(),
                '"some","new' . "\n" . 'line",with\\' . "\n" . 'escaped,"in\\' . "\n" . 'quotes"',
                [
                    [Token::T_QUOTE, 0, 1, '"'],
                    [Token::T_CONTENT, 1, 4, 'some'],
                    [Token::T_QUOTE, 5, 1, '"'],
                    [Token::T_DELIMITER, 6, 1, ','],
                    [Token::T_QUOTE, 7, 1, '"'],
                    [Token::T_CONTENT, 8, 3, 'new'],
                    [Token::T_NEW_LINE, 11, 1, "\n"],
                    [Token::T_CONTENT, 12, 4, 'line'],
                    [Token::T_QUOTE, 16, 1, '"'],
                    [Token::T_DELIMITER, 17, 1, ','],
                    [Token::T_CONTENT, 18, 4, 'with'],
                    [Token::T_ESCAPE, 22, 1, '\\'],
                    [Token::T_NEW_LINE, 23, 1, "\n"],
                    [Token::T_CONTENT, 24, 7, 'escaped'],
                    [Token::T_DELIMITER, 31, 1, ','],
                    [Token::T_QUOTE, 32, 1, '"'],
                    [Token::T_CONTENT, 33, 2, 'in'],
                    [Token::T_ESCAPE, 35, 1, '\\'],
                    [Token::T_NEW_LINE, 36, 1, "\n"],
                    [Token::T_CONTENT, 37, 6, 'quotes'],
                    [Token::T_QUOTE, 43, 1, '"'],
                ],
            ],
            [
                new CsvConfiguration(),
                '',
                [],
            ],
        ];
    }
}
