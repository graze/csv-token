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

namespace Graze\CsvToken\ValueParser;

class BoolValueParser implements ValueParserInterface
{
    /**
     * @param string $string
     *
     * @return mixed
     */
    public function parseValue($string)
    {
        return ($string === 'true');
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    public function canParseValue($string)
    {
        return $string === 'true' || $string === 'false';
    }
}
