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

namespace Graze\CsvToken\Buffer;

class StreamBuffer implements BufferInterface
{
    const READ_LENGTH = 128;

    /** @var string */
    protected $contents;
    /** @var int */
    protected $length;
    /** @var int */
    protected $position;
    /** @var bool */
    protected $eof = false;
    /** @var int */
    protected $minSize;

    /** @var resource */
    private $stream;
    /** @var int */
    private $readLength;

    /**
     * Create a buffer around a stream
     *
     * @param resource $stream
     * @param int      $readLength
     * @param int      $minSize
     */
    public function __construct($stream, $readLength = self::READ_LENGTH, $minSize = -1)
    {
        $this->stream = $stream;
        $this->readLength = $readLength;
        $this->position = ftell($stream);
        $this->minSize = $minSize;
        $this->length = 0;
        $this->eof = false;
    }

    /**
     * @return string
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return bool
     */
    public function isEof()
    {
        return $this->length === 0;
    }

    /**
     * @return bool
     */
    public function read()
    {
        if ($this->canRead()) {
            $next = fread($this->stream, $this->readLength);
            if (strlen($next) > 0) {
                $this->contents .= $next;
                $this->length += strlen($next);
                return true;
            } else {
                $this->eof = true;
            }
        }
        return false;
    }

    /**
     * Remove the first $length characters from the buffer
     *
     * @param int $length
     *
     * @return bool
     */
    public function move($length)
    {
        $this->contents = substr($this->contents, $length);
        $newLen = max(0, $this->length - $length);
        $this->position += $this->length - $newLen;
        $this->length = $newLen;
        return true;
    }

    /**
     * Determine if we can read more from the stream
     *
     * @return bool
     */
    private function canRead()
    {
        return ((!$this->eof)
            && ($this->minSize < 0 || $this->length <= $this->minSize));
    }

    /**
     * @return bool
     */
    public function isSourceEof()
    {
        return $this->eof;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return int
     */
    public function getMinBufferSize()
    {
        return $this->minSize;
    }

    /**
     * @param int $size
     *
     * @return $this
     */
    public function setMinBufferSize($size)
    {
        $this->minSize = $size;
        return $this;
    }
}
