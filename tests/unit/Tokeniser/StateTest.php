<?php

namespace Graze\CsvToken\Test\Unit\Tokeniser;

use Graze\CsvToken\Test\TestCase;
use Graze\CsvToken\Tokeniser\State;
use Graze\CsvToken\Tokeniser\Token;
use RuntimeException;

class StateTest extends TestCase
{
    public function testCallGetNextStateWithAnInvalidTokenWillThrowAnException()
    {
        $state = new State([]);
        $state->addStateTarget(Token::T_CONTENT, $state);

        static::assertSame($state, $state->getNextState(Token::T_CONTENT));

        static::expectException(RuntimeException::class);
        $state->getNextState(Token::T_ESCAPE);
    }
}
