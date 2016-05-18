<?php

namespace Graze\CsvToken;

use ArrayIterator;
use Graze\CsvToken\Tokeniser\Token;
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
        $row = new ArrayIterator();

        $tokens->rewind();
        /** @var Token $token */
        $token = $tokens->current();
        while (!is_null($token)) {
            switch ($token->getType()) {
                case Token::T_QUOTE:
                    $value->setInQuotes(!$value->isInQuotes());
                    break;
                case Token::T_CONTENT:
                    $value->addContent($token->getContent());
                    break;
                case Token::T_DOUBLE_QUOTE:
                    $value->addContent(substr($token->getContent(), 0, $token->getLength() / 2));
                    break;
                case Token::T_NULL:
                    if ($value->isEmpty() && !$value->isInQuotes() && !$value->wasQuoted()) {
                        $value->addContent($token->getContent());
                        $value->setIsNull();
                    } else {
                        $value->addContent($token->getContent());
                    }
                    break;
                case Token::T_DELIMITER:
                    $row->append($value->getValue());
                    $value->reset();
                    break;
                case Token::T_NEW_LINE:
                    $row->append($value->getValue());
                    $value->reset();
                    yield $row;
                    $row = new ArrayIterator();
                    break;
                default:
                    break;
            }

            $tokens->next();
            $token = $tokens->current();
        }

        if (!$value->isEmpty()) {
            if ($value->isInQuotes()) {
                throw new RuntimeException("Unmatched quote at the end of the csv data");
            }
            $row->append($value->getValue());
        }

        if ($row->count() > 0) {
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
