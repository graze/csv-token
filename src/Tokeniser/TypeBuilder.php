<?php

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
