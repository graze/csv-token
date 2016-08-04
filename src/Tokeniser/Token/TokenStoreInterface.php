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

    /**
     * Determine if a mask set of tokens has changed
     *
     * @param int $mask
     *
     * @return bool
     */
    public function hasChanged($mask = Token::T_ANY);
}
