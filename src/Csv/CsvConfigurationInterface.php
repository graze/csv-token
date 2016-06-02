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

    /**
     * @return string|null
     */
    public function getBom();

    /**
     * @return string
     */
    public function getEncoding();
}
