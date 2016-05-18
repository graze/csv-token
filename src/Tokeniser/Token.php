<?php

namespace Graze\CsvToken\Tokeniser;

class Token
{
    const T_CONTENT      = 1;
    const T_DELIMITER    = 2;
    const T_NEW_LINE     = 3;
    const T_QUOTE        = 4;
    const T_NULL         = 5;
    const T_ESCAPE       = 6;
    const T_DOUBLE_QUOTE = 7;

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
