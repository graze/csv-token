<?php

namespace Graze\CsvToken\ValueParser;

class CallbackValueParser implements ValueParserInterface
{
    /** @var callable */
    private $canParse;
    /** @var callable */
    private $parse;

    /**
     * CallbackValueParser constructor.
     *
     * @param callable $canParse
     * @param callable $parse
     */
    public function __construct(callable $canParse, callable $parse)
    {
        $this->canParse = $canParse;
        $this->parse = $parse;
    }

    /**
     * @param string $string
     *
     * @return mixed
     */
    public function parseValue($string)
    {
        return call_user_func($this->parse, $string);
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    public function canParseValue($string)
    {
        return call_user_func($this->canParse, $string);
    }
}
