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

interface BufferInterface
{
    /**
     * @return string
     */
    public function getContents();

    /**
     * @return int
     */
    public function getLength();

    /**
     * Returns if we are at the end of the source or not
     *
     * @return bool
     */
    public function isSourceEof();

    /**
     * Returns the position in the source that the beginning of the buffer is at
     *
     * @return int
     */
    public function getPosition();

    /**
     * Returns true if the buffer is empty
     *
     * @return bool
     */
    public function isEof();

    /**
     * Read ahead from the source. Returns false if there is nothing that can be read
     *
     * @return bool
     */
    public function read();

    /**
     * Remove the first $length characters from the buffer
     *
     * @param int $length
     *
     * @return bool
     */
    public function move($length);

    /**
     * @return int
     */
    public function getMinBufferSize();

    /**
     * @param int $size
     *
     * @return $this
     */
    public function setMinBufferSize($size);
}
