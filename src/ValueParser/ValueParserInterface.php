<?php

namespace Graze\CsvToken\ValueParser;

interface ValueParserInterface
{
    /**
     * @param string $string
     *
     * @return mixed
     */
    public function parseValue($string);

    /**
     * Determine if the provided string can be parsed. If this returns true no other value parsers will be run
     *
     * @param string $string
     *
     * @return bool
     */
    public function canParseValue($string);
}
