<?php

namespace Graze\CsvToken\Tokeniser;

use Iterator;

interface TokeniserInterface
{
    /**
     * @return Iterator
     */
    public function getTokens();
}
