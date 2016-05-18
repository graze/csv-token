<?php

namespace Graze\CsvToken\Tokeniser;

use Graze\CsvToken\Csv\CsvConfigurationInterface;
use Iterator;
use Psr\Http\Message\StreamInterface;

class StreamTokeniser implements TokeniserInterface
{
    use TypeBuilder;

    /** @var int[] */
    private $types;
    /** @var int */
    private $maxTypeLength;
    /** @var StreamInterface */
    private $stream;

    /**
     * Tokeniser constructor.
     *
     * @param CsvConfigurationInterface $config
     * @param StreamInterface           $stream
     */
    public function __construct(CsvConfigurationInterface $config, StreamInterface $stream)
    {
        $this->types = $this->getTypes($config);

        // sort by reverse key length
        uksort($this->types, function ($first, $second) {
            return strlen($second) - strlen($first);
        });
        $this->maxTypeLength = count($this->types) > 0 ? strlen(array_keys($this->types)[0]) : 1;
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
        $this->stream->rewind();
        $position = $this->stream->tell();
        $buffer = $this->stream->read($this->maxTypeLength);

        /** @var Token $last */
        $last = null;

        while (strlen($buffer) > 0) {
            $token = $this->match($position, $buffer);
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
            $buffer = substr($buffer, $len) . $this->stream->read($len);
        }

        if (!is_null($last)) {
            yield $last;
        }

        $this->stream->close();
    }

    /**
     * @param int    $position
     * @param string $buffer
     *
     * @return Token
     */
    private function match($position, $buffer)
    {
        foreach ($this->types as $search => $tokenType) {
            if (substr($buffer, 0, strlen($search)) == $search) {
                return new Token($tokenType, $search, $position);
            }
        }

        return new Token(Token::T_CONTENT, $buffer[0], $position);
    }
}
