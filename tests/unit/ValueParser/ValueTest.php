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

namespace Graze\CsvToken\Test\Unit\ValueParser;

use Graze\CsvToken\Test\TestCase;
use Graze\CsvToken\ValueParser\Value;
use RuntimeException;

class ValueTest extends TestCase
{
    public function testAddContentWhenQuotesHaveBeenClosedWillThrowAnException()
    {
        $value = new Value();

        $value->setInQuotes(true);
        $value->addContent('some stuff');
        $value->setInQuotes(false);

        static::assertEquals('some stuff', $value->getValue());

        static::expectException(RuntimeException::class);
        $value->addContent('more content');
    }
}
