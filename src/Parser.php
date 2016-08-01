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

namespace Graze\CsvToken;

use ArrayIterator;
use Graze\CsvToken\Tokeniser\Token\Token;
use Graze\CsvToken\ValueParser\Value;
use Graze\CsvToken\ValueParser\ValueParserInterface;
use Iterator;
use RuntimeException;

class Parser implements ParserInterface
{
    /** @var ValueParserInterface[] */
    private $valueParsers = [];

    /**
     * Parser constructor.
     *
     * @param ValueParserInterface[] $valueParsers
     */
    public function __construct(array $valueParsers = [])
    {
        array_map([$this, 'addValueParser'], $valueParsers);
    }

    /**
     * @param Iterator $tokens
     *
     * @return Iterator Iterator of csv line Iterators
     */
    public function parse(Iterator $tokens)
    {
        $value = new Value($this->valueParsers);
        $row = [];

        foreach ($tokens as $token) {
            switch ($token[0]) {
                case Token::T_QUOTE:
                    $value->setInQuotes(!$value->isInQuotes());
                    break;
                case Token::T_CONTENT:
                    $value->addContent($token[1]);
                    break;
                case Token::T_DOUBLE_QUOTE:
                    $value->addContent(substr($token[1], 0, $token[3] / 2));
                    break;
                case Token::T_NULL:
                    if ($value->isEmpty() && !$value->isInQuotes() && !$value->wasQuoted()) {
                        $value->addContent($token[1]);
                        $value->setIsNull();
                    } else {
                        $value->addContent($token[1]);
                    }
                    break;
                case Token::T_DELIMITER:
                    $row[] = $value->getValue();
                    $value->reset();
                    break;
                case Token::T_NEW_LINE:
                    $row[] = $value->getValue();
                    $value->reset();
                    yield $row;
                    $row = [];
                    break;
                default:
                    break;
            }
        }

        if (!$value->isEmpty()) {
            if ($value->isInQuotes()) {
                throw new RuntimeException("Unmatched quote at the end of the csv data");
            }
            $row[] = $value->getValue();
        }

        if (count($row) > 0) {
            yield $row;
        }
    }

    /**
     * @param ValueParserInterface $valueParser
     */
    public function addValueParser(ValueParserInterface $valueParser)
    {
        $this->valueParsers[] = $valueParser;
    }

    /**
     * @param ValueParserInterface $valueParser
     */
    public function removeValueParser(ValueParserInterface $valueParser)
    {
        $index = array_search($valueParser, $this->valueParsers, true);
        if ($index !== false) {
            unset($this->valueParsers[$index]);
        }
    }

    /**
     * @param ValueParserInterface $valueParser
     *
     * @return bool
     */
    public function hasValueParser(ValueParserInterface $valueParser)
    {
        return in_array($valueParser, $this->valueParsers);
    }
}
