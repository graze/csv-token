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

namespace Graze\CsvToken\Test\Unit\Tokeniser;

use Graze\CsvToken\Test\TestCase;
use Graze\CsvToken\Tokeniser\State;
use Graze\CsvToken\Tokeniser\Token\Token;
use Graze\CsvToken\Tokeniser\Token\TokenStoreInterface;
use Mockery as m;
use RuntimeException;

class StateTest extends TestCase
{
    public function testCallGetNextStateWithAnInvalidTokenWillThrowAnException()
    {
        $tokenStore = m::mock(TokenStoreInterface::class);
        $state = new State($tokenStore);

        $tokenStore->shouldReceive('getTokens')
                   ->andReturn([]);

        $state->addStateTarget(Token::T_CONTENT, $state);

        static::assertSame($state, $state->getNextState(Token::T_CONTENT));

        static::expectException(RuntimeException::class);
        $state->getNextState(Token::T_ESCAPE);
    }
}
