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

use Graze\CsvToken\Parser;
use Graze\CsvToken\Test\TestCase;
use Graze\CsvToken\ValueParser\ValueParserInterface;
use Iterator;
use Mockery as m;

class ParserTest extends TestCase
{
    public function testConstructorValueParsers()
    {
        /** @var ValueParserInterface $valueParser */
        $valueParser = m::mock(ValueParserInterface::class);

        $parser = new Parser([$valueParser]);
        static::assertTrue($parser->hasValueParser($valueParser));
    }

    public function testAddingAParser()
    {
        /** @var ValueParserInterface $valueParser */
        $valueParser = m::mock(ValueParserInterface::class);

        $parser = new Parser();
        static::assertFalse($parser->hasValueParser($valueParser));

        $parser->addValueParser($valueParser);
        static::assertTrue($parser->hasValueParser($valueParser));
    }

    public function testRemovingAParser()
    {
        /** @var ValueParserInterface $valueParser */
        $valueParser = m::mock(ValueParserInterface::class);

        $parser = new Parser([$valueParser]);

        static::assertTrue($parser->hasValueParser($valueParser));
        $parser->removeValueParser($valueParser);
        static::assertFalse($parser->hasValueParser($valueParser));
    }

    public function testParserIteratesSoNothingShouldHappenIfThereIsNoRequestForData()
    {
        $parser = new Parser();

        /** @var Iterator $tokens */
        $tokens = m::mock(Iterator::class);

        $output = $parser->parse($tokens);

        static::assertInstanceOf(Iterator::class, $output);
    }
}
