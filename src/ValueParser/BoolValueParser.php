<?php

namespace Graze\CsvToken\ValueParser;

class BoolValueParser implements ValueParserInterface
{
    /**
     * @param string $string
     *
     * @return mixed
     */
    public function parseValue($string)
    {
        return ($string === 'true');
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    public function canParseValue($string)
    {
        return $string === 'true' || $string === 'false';
    }
}
