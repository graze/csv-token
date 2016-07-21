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

use Graze\CsvToken\Csv\Bom;
use Graze\CsvToken\Csv\CsvConfigurationInterface;
use Graze\CsvToken\Tokeniser\Token\Token;
use Graze\CsvToken\Tokeniser\Token\TokenStore;
use Iterator;
use Psr\Http\Message\StreamInterface;

class StreamTokeniser implements TokeniserInterface
{
    use StateBuilder;

    /** @var int */
    private $maxTypeLength;
    /** @var resource */
    private $stream;
    /** @var State */
    private $state;
    /** @var TokenStore */
    private $tokenStore;

    /**
     * Tokeniser constructor.
     *
     * @param CsvConfigurationInterface $config
     * @param resource $stream
     */
    public function __construct(CsvConfigurationInterface $config, $stream)
    {
        $this->tokenStore = new TokenStore($config);
        $this->state = $this->buildStates($this->tokenStore);
        $types = $this->tokenStore->getTokens();
        $this->maxTypeLength = count($types) > 0 ? strlen(array_keys($types)[0]) : 1;
        $this->stream = $stream;
    }

    /**
     * Loop through the stream, pulling maximum type length each time, find the largest type that matches and create a
     * token, then move on length characters
     *
     * @return Iterator
     */
    public function getTokens()
    {
        fseek($this->stream, 0);
        $position = ftell($this->stream);
        $buffer = fread($this->stream, $this->maxTypeLength);

        /** @var Token $last */
        $last = null;

        while (strlen($buffer) > 0) {
            $token = $this->state->match($position, $buffer);

            if ($token->getType() == Token::T_BOM) {
                $this->changeEncoding($token);
            }

            $this->state = $this->state->getNextState($token->getType());

            $len = $token->getLength();

            // merge tokens together to condense T_CONTENT tokens
            if ($token->getType() == Token::T_CONTENT) {
                $last = (!is_null($last)) ? $last->addContent($token->getContent()) : $token;
            } else {
                if (!is_null($last)) {
                    yield $last;
                    $last = null;
                }
                yield $token;
            }

            $position += $len;
            $buffer = substr($buffer, $len) . fread($this->stream, $len);
        }

        if (!is_null($last)) {
            yield $last;
        }

        fclose($this->stream);
    }

    /**
     * @param Token $token
     */
    private function changeEncoding(Token $token)
    {
        $this->tokenStore->setEncoding(Bom::getEncoding($token->getContent()));
    }
}
