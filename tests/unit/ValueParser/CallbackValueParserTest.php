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

use Akamon\MockeryCallableMock\MockeryCallableMock;
use Graze\CsvToken\Test\TestCase;
use Graze\CsvToken\ValueParser\CallbackValueParser;
use Mockery as m;

class CallbackValueParserTest extends TestCase
{
    /** @var MockeryCallableMock */
    private $canParse;
    /** @var MockeryCallableMock */
    private $parse;
    /** @var CallbackValueParser */
    private $parser;

    public function setUp()
    {
        $this->canParse = new MockeryCallableMock();
        $this->parse = new MockeryCallableMock();
        $this->parser = new CallbackValueParser($this->canParse, $this->parse);
    }

    public function testCanParseCallback()
    {
        $this->canParse->shouldBeCalled()
                       ->with('string')
                       ->andReturn(true, false);

        static::assertTrue($this->parser->canParseValue('string'));
        static::assertFalse($this->parser->canParseValue('string'));
    }

    public function testParseCallback()
    {
        $this->parse->shouldBeCalled()
                    ->with('text')
                    ->andReturn('first', 'second');

        static::assertEquals('first', $this->parser->parseValue('text'));
        static::assertEquals('second', $this->parser->parseValue('text'));
    }
}
