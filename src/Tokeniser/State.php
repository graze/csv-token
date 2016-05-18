<?php

namespace Graze\CsvToken\Tokeniser;

class State
{
    const S_ANY             = 0;
    const S_IN_QUOTE        = 1;
    const S_IN_ESCAPE       = 2;
    const S_IN_QUOTE_ESCAPE = 4;

    const S_ANY_TOKENS             = Token::T_ANY & ~Token::T_DOUBLE_QUOTE;
    const S_IN_QUOTE_TOKENS        = Token::T_CONTENT | Token::T_QUOTE | Token::T_DOUBLE_QUOTE | Token::T_ESCAPE;
    const S_IN_ESCAPE_TOKENS       = Token::T_CONTENT;
    const S_IN_QUOTE_ESCAPE_TOKENS = Token::T_CONTENT;

    /** @var array */
    private $types;
    /** @var State[] */
    private $states;

    /**
     * State constructor.
     *
     * @param array $types
     */
    public function __construct(array $types)
    {
        $this->types = $types;
    }

    /**
     * @return int[]
     */
    public function getTypes()
    {
        return $this->types;
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

        return null;
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
    public function match($position, $buffer)
    {
        foreach ($this->types as $search => $tokenType) {
            if (substr($buffer, 0, strlen($search)) == $search) {
                return new Token($tokenType, $search, $position);
            }
        }

        return new Token(Token::T_CONTENT, $buffer[0], $position);
    }
}
