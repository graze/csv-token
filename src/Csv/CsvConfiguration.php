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

namespace Graze\CsvToken\Csv;

use InvalidArgumentException;

class CsvConfiguration implements CsvConfigurationInterface
{
    const DEFAULT_DELIMITER    = ',';
    const DEFAULT_NULL         = '\\N';
    const DEFAULT_QUOTE        = '"';
    const DEFAULT_ESCAPE       = '\\';
    const DEFAULT_DOUBLE_QUOTE = false;
    const DEFAULT_ENCODING     = 'UTF-8';

    const OPTION_DELIMITER    = 'delimiter';
    const OPTION_NULL         = 'null';
    const OPTION_NEW_LINES    = 'newLines';
    const OPTION_QUOTE        = 'quote';
    const OPTION_ESCAPE       = 'escape';
    const OPTION_DOUBLE_QUOTE = 'doubleQuote';
    const OPTION_BOMS         = 'boms';
    const OPTION_ENCODING     = 'encoding';

    /** @var string */
    private $delimiter;
    /** @var string */
    private $quote;
    /** @var string */
    private $escape;
    /** @var bool */
    private $doubleQuotes;
    /** @var string[] */
    private $newLines;
    /** @var string */
    private $null;
    /** @var string[] */
    private $boms;
    /** @var string */
    private $encoding;

    /**
     * CsvConfiguration constructor.
     *
     * @param array $options List of options that can be configured:
     *                       <p> `delimiter`    string (Default: `','`)
     *                       <p> `quote`        string (Default: `'"'`)
     *                       <p> `escape`       string (Default: `'\\'`)
     *                       <p> `doubleQuotes` string (Default: `false`)
     *                       <p> `newLines`     string[] (Default: `["\n","\r","\r\n"]`)
     *                       <p> `null`         string (Default: `'\\N'`)
     *                       <p> `boms`         string[] (Default:
     *                       `[Bom::BOM_UTF8,Bom::BOM_UTF16_BE,Bom::BOM_UTF16_LE,Bom::BOM_UTF32_BE,Bom::BOM_UTF32_LE]`)
     *                       <p> `encoding`     string (Default: `'UTF-8'`)
     */
    public function __construct(array $options = [])
    {
        $this->delimiter = $this->getOption($options, static::OPTION_DELIMITER, static::DEFAULT_DELIMITER);
        $this->quote = $this->getOption($options, static::OPTION_QUOTE, static::DEFAULT_QUOTE);
        $this->escape = $this->getOption($options, static::OPTION_ESCAPE, static::DEFAULT_ESCAPE);
        $this->doubleQuotes = $this->getOption($options, static::OPTION_DOUBLE_QUOTE, static::DEFAULT_DOUBLE_QUOTE);
        $this->null = $this->getOption($options, static::OPTION_NULL, static::DEFAULT_NULL);
        $this->encoding = $this->getOption($options, static::OPTION_ENCODING, static::DEFAULT_ENCODING);
        $this->newLines = (array) $this->getOption(
            $options,
            static::OPTION_NEW_LINES,
            ["\n", "\r", "\r\n"],
            'is_array'
        );
        $this->boms = (array) $this->getOption(
            $options,
            static::OPTION_BOMS,
            [
                Bom::BOM_UTF8,
                Bom::BOM_UTF16_BE,
                Bom::BOM_UTF16_LE,
                Bom::BOM_UTF32_BE,
                Bom::BOM_UTF32_LE,
            ],
            'is_array'
        );
    }

    /**
     * @param array    $options
     * @param string   $name
     * @param mixed    $default
     * @param callable $type
     *
     * @return mixed
     */
    private function getOption(array $options, $name, $default = null, callable $type = null)
    {
        if (array_key_exists($name, $options)) {
            $result = $options[$name];
        } else {
            $result = $default;
        }
        if ($type) {
            if (!call_user_func($type, $result)) {
                throw new InvalidArgumentException(
                    "The value: " . print_r($result, true) . " for option: {$name} is invalid"
                );
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * @return string
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * @return string
     */
    public function getEscape()
    {
        return $this->escape;
    }

    /**
     * @return string[]
     */
    public function getNewLines()
    {
        return $this->newLines;
    }

    /**
     * @return bool
     */
    public function useDoubleQuotes()
    {
        return $this->doubleQuotes;
    }

    /**
     * @return string
     */
    public function getNullValue()
    {
        return $this->null;
    }

    /**
     * @return string[]
     */
    public function getBoms()
    {
        return $this->boms;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }
}
