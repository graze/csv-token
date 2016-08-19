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

namespace Graze\CsvToken\Test;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $string
     *
     * @return resource
     */
    protected function getStream($string)
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $string);
        rewind($stream);
        return $stream;
    }
}
