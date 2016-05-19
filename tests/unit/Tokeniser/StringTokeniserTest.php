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

namespace Graze\CsvToken\Test\Unit;

use Graze\CsvToken\Csv\CsvConfiguration;
use Graze\CsvToken\Test\TestCase;
use Graze\CsvToken\Tokeniser\StreamTokeniser;
use Graze\CsvToken\Tokeniser\StringTokeniser;
use Graze\CsvToken\Tokeniser\TokeniserInterface;

class StringTokeniserTest extends TestCase
{
    public function testInstanceOf()
    {
        $tokeniser = new StringTokeniser(new CsvConfiguration(), '"test","data"');

        static::assertInstanceOf(StreamTokeniser::class, $tokeniser);
        static::assertInstanceOf(TokeniserInterface::class, $tokeniser);
    }
}
