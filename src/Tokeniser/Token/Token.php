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

class Token
{
    const T_CONTENT      = 1;
    const T_DELIMITER    = 2;
    const T_NEW_LINE     = 4;
    const T_QUOTE        = 8;
    const T_NULL         = 16;
    const T_ESCAPE       = 32;
    const T_DOUBLE_QUOTE = 128;
    const T_BOM          = 256;
    const T_ANY          = 511;
}
