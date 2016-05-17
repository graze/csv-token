<?php

namespace Graze\CsvToken\Csv;

interface CsvConfigurationInterface
{
    /**
     * @return string
     */
    public function getDelimiter();

    /**
     * @return string
     */
    public function getQuote();

    /**
     * @return string
     */
    public function getEscape();

    /**
     * @return string|array
     */
    public function getNewLine();

    /**
     * @return bool
     */
    public function useDoubleQuotes();

    /**
     * @return string
     */
    public function getNullValue();
}
