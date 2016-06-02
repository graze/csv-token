<?php

namespace Graze\CsvToken\Tokeniser\Token;

use Graze\CsvToken\Csv\Bom;
use Graze\CsvToken\Csv\CsvConfigurationInterface;

class TokenStore implements TokenStoreInterface
{
    /** @var array[] */
    private $maskStore = [];
    /** @var int[] */
    private $tokens = [];
    /** @var string|null */
    private $lastEncoding = null;

    /**
     * TokenStore constructor.
     *
     * @param CsvConfigurationInterface $config
     */
    public function __construct(CsvConfigurationInterface $config)
    {
        $this->tokens = $this->buildTokens($config);
        $this->setEncoding($config->getEncoding());
        $this->sort();
    }

    /**
     * @param int $mask
     *
     * @return array
     */
    public function getTokens($mask = Token::T_ANY)
    {
        if (!array_key_exists($mask, $this->maskStore)) {
            $this->maskStore[$mask] = array_filter($this->tokens, function ($type) use ($mask) {
                return $type & $mask;
            });
        }

        return $this->maskStore[$mask];
    }

    /**
     * @param CsvConfigurationInterface $config
     *
     * @return int[]
     */
    private function buildTokens(CsvConfigurationInterface $config)
    {
        $tokens = [
            $config->getDelimiter() => Token::T_DELIMITER,
        ];

        if ($config->getQuote() != '') {
            $tokens[$config->getQuote()] = Token::T_QUOTE;
            if ($config->useDoubleQuotes()) {
                $tokens[str_repeat($config->getQuote(), 2)] = Token::T_DOUBLE_QUOTE;
            }
        }
        if ($config->getEscape() != '') {
            $tokens[$config->getEscape()] = Token::T_ESCAPE;
        }

        $newLines = $config->getNewLine();
        if (!is_array($newLines)) {
            $newLines = [$newLines];
        }
        foreach ($newLines as $newLine) {
            $tokens[$newLine] = Token::T_NEW_LINE;
        }
        if (!is_null($config->getNullValue())) {
            $tokens[$config->getNullValue()] = Token::T_NULL;
        }

        if (is_null($config->getBom())) {
            $boms = [Bom::BOM_UTF8, Bom::BOM_UTF16_BE, Bom::BOM_UTF16_LE, Bom::BOM_UTF32_BE, Bom::BOM_UTF32_LE];
        } else {
            $boms = [$config->getBom()];
        }
        foreach ($boms as $bom) {
            $tokens[$bom] = Token::T_BOM;
        }

        return $tokens;
    }

    /**
     * @param string $encoding
     */
    public function setEncoding($encoding)
    {
        if ($encoding != $this->lastEncoding) {
            if ($this->lastEncoding) {
                $changeEncoding = function ($string) use ($encoding) {
                    return mb_convert_encoding($string, $encoding, $this->lastEncoding);
                };
            } else {
                $changeEncoding = function ($string) use ($encoding) {
                    return mb_convert_encoding($string, $encoding);
                };
            }
            $tokens = [];
            foreach ($this->tokens as $string => $token) {
                if ($token != Token::T_BOM) {
                    $string = $changeEncoding($string);
                }
                $tokens[$string] = $token;
            }
            $this->tokens = $tokens;
            $this->sort();

            $this->lastEncoding = $encoding;
            $this->maskStore = [];
        }
    }

    /**
     * Sort the tokens into reverse key length order
     */
    private function sort()
    {
        // sort by reverse key length
        uksort($this->tokens, function ($first, $second) {
            return strlen($second) - strlen($first);
        });
    }
}
