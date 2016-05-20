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

class CsvConfiguration implements CsvConfigurationInterface
{
    const DEFAULT_DELIMITER    = ',';
    const DEFAULT_NULL         = '\\N';
    const DEFAULT_NEW_LINE     = PHP_EOL;
    const DEFAULT_QUOTE        = '"';
    const DEFAULT_ESCAPE       = '\\';
    const DEFAULT_DOUBLE_QUOTE = false;

    const OPTION_DELIMITER    = 'delimiter';
    const OPTION_NULL         = 'null';
    const OPTION_NEW_LINE     = 'newLine';
    const OPTION_QUOTE        = 'quote';
    const OPTION_ESCAPE       = 'escape';
    const OPTION_DOUBLE_QUOTE = 'doubleQuote';

    /** @var string */
    private $delimiter;
    /** @var string */
    private $quote;
    /** @var string */
    private $escape;
    /** @var bool */
    private $doubleQuotes;
    /** @var string|array */
    private $newLine;
    /** @var string */
    private $null;

    /**
     * CsvConfiguration constructor.
     *
     * @param array $options List of options that can be configured:
     *                       <p> `delimiter`    string (Default: `','`)
     *                       <p> `quote`        string (Default: `'"'`)
     *                       <p> `escape`       string (Default: `'\\'`)
     *                       <p> `doubleQuotes` string (Default: `false`)
     *                       <p> `newLine`      string|array (Default: `PHP_EOL`)
     *                       <p> `null`         string (Default: `'\\N'`)
     */
    public function __construct(array $options = [])
    {
        $this->delimiter = $this->getOption($options, static::OPTION_DELIMITER, static::DEFAULT_DELIMITER);
        $this->quote = $this->getOption($options, static::OPTION_QUOTE, static::DEFAULT_QUOTE);
        $this->escape = $this->getOption($options, static::OPTION_ESCAPE, static::DEFAULT_ESCAPE);
        $this->doubleQuotes = $this->getOption($options, static::OPTION_DOUBLE_QUOTE, static::DEFAULT_DOUBLE_QUOTE);
        $this->newLine = $this->getOption($options, static::OPTION_NEW_LINE, static::DEFAULT_NEW_LINE);
        $this->null = $this->getOption($options, static::OPTION_NULL, static::DEFAULT_NULL);
    }

    /**
     * @param array  $options
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    private function getOption(array $options, $name, $default = null)
    {
        if (array_key_exists($name, $options)) {
            return $options[$name];
        } else {
            return $default;
        }
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
     * @return string|array
     */
    public function getNewLine()
    {
        return $this->newLine;
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
}
