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

namespace Graze\CsvToken\Tokeniser;

use Graze\CsvToken\Tokeniser\Token\Token;
use Graze\CsvToken\Tokeniser\Token\TokenStoreInterface;
use RuntimeException;

class State
{
    const S_ANY             = 0;
    const S_IN_QUOTE        = 1;
    const S_IN_ESCAPE       = 2;
    const S_IN_QUOTE_ESCAPE = 4;
    const S_INITIAL         = 5;

    const S_INITIAL_TOKENS         = Token::T_ANY & ~Token::T_DOUBLE_QUOTE;
    const S_ANY_TOKENS             = Token::T_ANY & ~Token::T_DOUBLE_QUOTE & ~Token::T_BOM;
    const S_IN_QUOTE_TOKENS        = Token::T_CONTENT | Token::T_QUOTE | Token::T_DOUBLE_QUOTE | Token::T_ESCAPE;
    const S_IN_ESCAPE_TOKENS       = Token::T_CONTENT;
    const S_IN_QUOTE_ESCAPE_TOKENS = Token::T_CONTENT;

    /** @var State[] */
    private $states;
    /** @var TokenStoreInterface */
    private $tokenStore;
    /** @var int */
    private $tokenMask;
    /** @var int[] */
    private $tokens;
    /** @var string[] */
    private $keys;
    /** @var int[] */
    private $keyLengths;

    /**
     * TokenStoreInterface is passed in here, as the tokens can be modified by the store
     *
     * @param TokenStoreInterface $tokens
     * @param int                 $tokenMask
     */
    public function __construct(TokenStoreInterface $tokens, $tokenMask = Token::T_ANY)
    {
        $this->tokenStore = $tokens;
        $this->tokenMask = $tokenMask;
        $this->parseTokens();
    }

    private function parseTokens()
    {
        $this->tokens = $this->tokenStore->getTokens($this->tokenMask);
        $this->keys = array_keys($this->tokens);
        $this->keyLengths = array_unique(array_map('strlen', $this->keys));
        arsort($this->keyLengths);
    }

    /**
     * @param int $token
     *
     * @return State|null
     */
    public function getNextState($token)
    {
        foreach ($this->states as $mask => $state) {
            if ($mask & $token) {
                return $state;
            }
        }

        throw new RuntimeException("The supplied token: {$token} has no target state");
    }

    /**
     * @param int   $tokenMask
     * @param State $target
     */
    public function addStateTarget($tokenMask, State $target)
    {
        $this->states[$tokenMask] = $target;
    }

    /**
     * @param int    $position
     * @param string $buffer
     *
     * @return Token
     */
    public function match($position, &$buffer)
    {
        if ($this->tokenStore->hasChanged($this->tokenMask)) {
            $this->parseTokens();
        }

        foreach ($this->keyLengths as $len) {
            $buf = substr($buffer, 0, $len);
            if (isset($this->tokens[$buf])) {
                return [$this->tokens[$buf], $buf, $position];
            }
        }

        return [Token::T_CONTENT, $buffer[0], $position];
    }
}
