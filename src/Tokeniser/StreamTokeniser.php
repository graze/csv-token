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

use Graze\CsvToken\Buffer\StreamBuffer;
use Graze\CsvToken\Csv\Bom;
use Graze\CsvToken\Csv\CsvConfigurationInterface;
use Graze\CsvToken\Tokeniser\Token\Token;
use Graze\CsvToken\Tokeniser\Token\TokenStore;
use Iterator;

class StreamTokeniser implements TokeniserInterface
{
    use StateBuilder;

    const BUFFER_SIZE = 128;

    /** @var int */
    private $minLength;
    /** @var resource */
    private $stream;
    /** @var State */
    private $state;
    /** @var TokenStore */
    private $tokenStore;
    /** @var StreamBuffer */
    private $buffer;

    /**
     * Tokeniser constructor.
     *
     * @param CsvConfigurationInterface $config
     * @param resource                  $stream
     */
    public function __construct(CsvConfigurationInterface $config, $stream)
    {
        $this->tokenStore = new TokenStore($config);
        $this->state = $this->buildStates($this->tokenStore);
        $types = $this->tokenStore->getTokens();
        $this->minLength = count($types) > 0 ? strlen(array_keys($types)[0]) * 2 : 1;
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
        $this->buffer = new StreamBuffer($this->stream, static::BUFFER_SIZE, $this->minLength);
        $this->buffer->read();

        /** @var Token $last */
        $last = null;

        while (!$this->buffer->isEof()) {
            foreach ($this->state->match($this->buffer) as $token) {
                if ($token[0] == Token::T_BOM) {
                    $this->changeEncoding($token[1]);
                }

                $this->state = $this->state->getNextState($token[0]);

                // merge tokens together to condense T_CONTENT tokens
                if ($token[0] == Token::T_CONTENT) {
                    if (!is_null($last)) {
                        $last[1] .= $token[1];
                        $last[3] = strlen($last[1]);
                    } else {
                        $last = $token;
                    }
                } else {
                    if (!is_null($last)) {
                        yield $last;
                        $last = null;
                    }
                    yield $token;
                }

                $this->buffer->move($token[3]);
                $this->buffer->read();
            }
        }

        if (!is_null($last)) {
            yield $last;
        }

        fclose($this->stream);
    }

    /**
     * @param string $content
     */
    private function changeEncoding($content)
    {
        $this->tokenStore->setEncoding(Bom::getEncoding($content));
        $types = $this->tokenStore->getTokens();
        $this->minLength = count($types) > 0 ? strlen(array_keys($types)[0]) * 2 : 1;
        $this->buffer->setMinBufferSize($this->minLength);
    }
}
