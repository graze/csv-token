<?php

namespace Graze\CsvToken\Tokeniser;

use Graze\CsvToken\Csv\CsvConfigurationInterface;
use Iterator;
use Psr\Http\Message\StreamInterface;

class StreamTokeniser implements TokeniserInterface
{
    use TypeBuilder;
    use StateBuilder;

    /** @var int */
    private $maxTypeLength;
    /** @var StreamInterface */
    private $stream;
    /** @var State */
    private $state;

    /**
     * Tokeniser constructor.
     *
     * @param CsvConfigurationInterface $config
     * @param StreamInterface           $stream
     */
    public function __construct(CsvConfigurationInterface $config, StreamInterface $stream)
    {
        $types = $this->getTypes($config);
        $this->state = $this->buildStates($types);
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
        $this->stream->rewind();
        $position = $this->stream->tell();
        $buffer = $this->stream->read($this->maxTypeLength);

        /** @var Token $last */
        $last = null;

        while (strlen($buffer) > 0) {
            $token = $this->state->match($position, $buffer);
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
            $buffer = substr($buffer, $len) . $this->stream->read($len);
        }

        if (!is_null($last)) {
            yield $last;
        }

        $this->stream->close();
    }
}
