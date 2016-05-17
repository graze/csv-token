<?php

namespace Graze\CsvToken\ValueParser;

use RuntimeException;

class Value
{
    /** @var bool */
    private $inQuotes = false;
    /** @var bool */
    private $wasQuoted = false;
    /** @var ValueParserInterface[] */
    private $valueParsers = [];
    /** @var string */
    private $content = '';
    /** @var bool */
    private $isNull = false;
    /** @var bool */
    private $hasContent = false;

    /**
     * Value constructor.
     *
     * @param array $valueParsers
     */
    public function __construct(array $valueParsers = [])
    {
        $this->valueParsers = $valueParsers;
        $this->reset();
    }

    /**
     * @param bool $quoted
     *
     * @return static
     */
    public function setInQuotes($quoted)
    {
        $this->inQuotes = $quoted;
        if ($quoted) {
            $this->wasQuoted = true;
        }
        return $this;
    }

    /**
     * @param string $content
     *
     * @return static
     */
    public function addContent($content)
    {
        if (!$this->inQuotes && $this->wasQuoted) {
            throw new RuntimeException(
                "Invalid CSV: Attempting to add a string to a field that was in quotes: " . $this->content . $content
            );
        }
        $this->content .= $content;
        $this->isNull = false;
        $this->hasContent = true;
        return $this;
    }

    /**
     * Indicated that this value is null
     */
    public function setIsNull()
    {
        $this->isNull = true;
        $this->hasContent = true;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        if ($this->wasQuoted) {
            return $this->content;
        } elseif ($this->isNull) {
            return null;
        } else {
            foreach ($this->valueParsers as $valueParser) {
                if ($valueParser->canParseValue($this->content)) {
                    return $valueParser->parseValue($this->content);
                }
            }
        }

        return $this->content;
    }

    /**
     * Reset the state
     */
    public function reset()
    {
        $this->inQuotes = false;
        $this->wasQuoted = false;
        $this->content = '';
        $this->isNull = false;
        $this->hasContent = false;
    }

    /**
     * @return bool
     */
    public function isInQuotes()
    {
        return $this->inQuotes;
    }

    /**
     * @return bool
     */
    public function wasQuoted()
    {
        return $this->wasQuoted;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return !$this->hasContent;
    }
}
