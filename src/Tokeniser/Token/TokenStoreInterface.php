<?php

namespace Graze\CsvToken\Tokeniser\Token;

interface TokenStoreInterface
{
    /**
     * Get all the tokens and search strings matching the provided mask
     *
     * @param int $mask
     *
     * @return int[]
     */
    public function getTokens($mask = Token::T_ANY);
}
