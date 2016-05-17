<?php

namespace Graze\CsvToken\Tokeniser;

use Graze\CsvToken\Csv\CsvConfigurationInterface;

trait TypeBuilder
{
    /**
     * @param CsvConfigurationInterface $config
     *
     * @return int[]
     */
    protected function getTypes(CsvConfigurationInterface $config)
    {
        $types[$config->getDelimiter()] = Token::T_DELIMITER;
        $types[$config->getQuote()] = Token::T_QUOTE;
        $types[$config->getEscape()] = Token::T_ESCAPE;
        if ($config->useDoubleQuotes()) {
            $types[$config->getQuote() . $config->getQuote()] = Token::T_DOUBLE_QUOTE;
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

        return $types;
    }
}
