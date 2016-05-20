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

namespace Graze\CsvToken\Tokeniser;

use Graze\CsvToken\Csv\CsvConfigurationInterface;

trait TypeBuilder
{
    /**
     * @param CsvConfigurationInterface $config
     *
     * @return int[] Sorted in order of precedence
     */
    protected function getTypes(CsvConfigurationInterface $config)
    {
        $types = [
            $config->getDelimiter() => Token::T_DELIMITER,
            $config->getQuote()     => Token::T_QUOTE,
            $config->getEscape()    => Token::T_ESCAPE,
        ];

        if ($config->useDoubleQuotes()) {
            $types[str_repeat($config->getQuote(), 2)] = Token::T_DOUBLE_QUOTE;
        }
        $newLines = $config->getNewLine();
        if (!is_array($newLines)) {
            $newLines = [$newLines];
        }
        foreach ($newLines as $newLine) {
            $types[$newLine] = Token::T_NEW_LINE;
        }
        if (!is_null($config->getNullValue())) {
            $types[$config->getNullValue()] = Token::T_NULL;
        }

        // sort by reverse key length
        uksort($types, function ($first, $second) {
            return strlen($second) - strlen($first);
        });

        return $types;
    }
}
