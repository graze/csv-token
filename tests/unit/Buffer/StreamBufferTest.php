<?php

namespace Graze\CsvToken\Test\Unit\Buffer;

use Graze\CsvToken\Buffer\StreamBuffer;
use Graze\CsvToken\Test\TestCase;

class StreamBufferTest extends TestCase
{
    /**
     * @param string $string
     *
     * @return resource
     */
    private function makeStream($string)
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $string);
        rewind($stream);
        return $stream;
    }

    public function testBasicProperties()
    {
        $stream = $this->makeStream('some text here and things');
        $buffer = new StreamBuffer($stream);

        static::assertEquals(0, $buffer->getPosition());
        static::assertEquals('', $buffer->getContents());
        static::assertEquals(0, $buffer->getLength());
        static::assertTrue($buffer->isEof());
        static::assertFalse($buffer->isSourceEof());

        $buffer->read();

        static::assertEquals(0, $buffer->getPosition());
        static::assertEquals('some text here and things', $buffer->getContents());
        static::assertEquals(25, $buffer->getLength());
        static::assertFalse($buffer->isEof());
        static::assertFalse($buffer->isSourceEof());

        $buffer->read();

        static::assertTrue($buffer->isSourceEof());
    }

    public function testReadSize()
    {
        $stream = $this->makeStream('first second third fourth fifth');
        $buffer = new StreamBuffer($stream, 6);

        $buffer->read();

        static::assertEquals('first ', $buffer->getContents());
    }

    public function testMinSize()
    {
        $stream = $this->makeStream('first second third fourth');
        $buffer = new StreamBuffer($stream, 10, 2);

        $buffer->read();
        static::assertEquals('first seco', $buffer->getContents());
        $buffer->read();
        static::assertEquals('first seco', $buffer->getContents());

        static::assertEquals(2, $buffer->getMinBufferSize());
        static::assertSame($buffer, $buffer->setMinBufferSize(-1));
        static::assertEquals(-1, $buffer->getMinBufferSize());
        $buffer->read();
        static::assertEquals('first second third f', $buffer->getContents());
    }

    public function testMove()
    {
        $stream = $this->makeStream('first second third fourth');
        $buffer = new StreamBuffer($stream, 10, 2);

        $buffer->read();
        static::assertEquals('first seco', $buffer->getContents());
        $buffer->move(9);
        static::assertEquals('o', $buffer->getContents());
        $buffer->read();
        static::assertEquals('ond third f', $buffer->getContents());
    }
}
