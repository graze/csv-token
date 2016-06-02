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

namespace Graze\CsvToken\Tokeniser\Token;

class Token
{
    const T_CONTENT      = 1;
    const T_DELIMITER    = 2;
    const T_NEW_LINE     = 4;
    const T_QUOTE        = 8;
    const T_NULL         = 16;
    const T_ESCAPE       = 32;
    const T_DOUBLE_QUOTE = 128;
    const T_BOM          = 256;
    const T_ANY          = 511;

    /** @var int */
    private $type;
    /** @var string */
    private $content;
    /** @var int */
    private $position;
    /** @var int */
    private $length;

    /**
     * Token constructor.
     *
     * @param int    $type
     * @param string $content
     * @param int    $position
     */
    public function __construct($type, $content, $position)
    {
        $this->type = $type;
        $this->content = $content;
        $this->length = strlen($content);
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Append some content onto this content
     *
     * @param string $content
     *
     * @return static
     */
    public function addContent($content)
    {
        $this->content .= $content;
        $this->length = strlen($this->content);
        return $this;
    }
}