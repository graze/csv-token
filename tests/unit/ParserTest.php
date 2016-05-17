<?php

namespace Graze\CsvToken\Test\Unit;

use Graze\CsvToken\Parser;
use Graze\CsvToken\Test\TestCase;
use Graze\CsvToken\ValueParser\ValueParserInterface;
use Iterator;
use Mockery as m;
use Mockery\MockInterface;

class ParserTest extends TestCase
{
    public function testConstructorValueParsers()
    {
        /** @var ValueParserInterface|MockInterface $valueParser */
        $valueParser = m::mock(ValueParserInterface::class);

        $parser = new Parser([$valueParser]);
        static::assertTrue($parser->hasValueParser($valueParser));
    }

    public function testAddingAParser()
    {
        /** @var ValueParserInterface|MockInterface $valueParser */
        $valueParser = m::mock(ValueParserInterface::class);

        $parser = new Parser();
        static::assertFalse($parser->hasValueParser($valueParser));

        $parser->addValueParser($valueParser);
        static::assertTrue($parser->hasValueParser($valueParser));
    }

    public function testRemovingAParser()
    {
        /** @var ValueParserInterface|MockInterface $valueParser */
        $valueParser = m::mock(ValueParserInterface::class);

        $parser = new Parser([$valueParser]);

        static::assertTrue($parser->hasValueParser($valueParser));
        $parser->removeValueParser($valueParser);
        static::assertFalse($parser->hasValueParser($valueParser));
    }

    public function testParserIteratesSoNothingShouldHappenIfThereIsNoRequestForData()
    {
        $parser = new Parser();

        $tokens = m::mock(Iterator::class);

        $output = $parser->parse($tokens);

        static::assertInstanceOf(Iterator::class, $output);
    }
}
