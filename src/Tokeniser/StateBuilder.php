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

trait StateBuilder
{
    /**
     * @param array $types
     *
     * @return State The default starting state
     */
    public function buildStates(array $types)
    {
        $getTypes = function ($tokenMask) use ($types) {
            return array_filter($types, function ($type) use ($tokenMask) {
                return $type & $tokenMask;
            });
        };

        $any = new State($getTypes(State::S_ANY_TOKENS));
        $inQuote = new State($getTypes(State::S_IN_QUOTE_TOKENS));
        $inEscape = new State($getTypes(State::S_IN_ESCAPE_TOKENS));
        $inQuoteEscape = new State($getTypes(State::S_IN_QUOTE_ESCAPE_TOKENS));

        // generate state mapping
        $any->addStateTarget(Token::T_ANY & ~Token::T_QUOTE & ~Token::T_ESCAPE, $any);
        $any->addStateTarget(Token::T_QUOTE, $inQuote);
        $any->addStateTarget(Token::T_ESCAPE, $inEscape);

        $inQuote->addStateTarget(Token::T_CONTENT | Token::T_DOUBLE_QUOTE, $inQuote);
        $inQuote->addStateTarget(Token::T_QUOTE, $any);
        $inQuote->addStateTarget(Token::T_ESCAPE, $inQuoteEscape);

        $inEscape->addStateTarget(Token::T_CONTENT, $any);

        $inQuoteEscape->addStateTarget(Token::T_CONTENT, $inQuote);

        return $any;
    }
}
