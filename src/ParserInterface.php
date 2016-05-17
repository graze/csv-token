<?php

namespace Graze\CsvToken;

use Iterator;

interface ParserInterface
{
    /**
     * @param Iterator $tokens
     *
     * @return \array[] Array of array of csv items
     */
    public function parse(Iterator $tokens);
}
