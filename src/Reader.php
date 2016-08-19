<?php

namespace Graze\CsvToken;

use Graze\CsvToken\Csv\CsvConfigurationInterface;
use Graze\CsvToken\Tokeniser\StreamTokeniser;
use Iterator;

class Reader
{
    /** @var resource */
    private $stream;
    /** @var CsvConfigurationInterface */
    private $config;

    /**
     * Reader constructor.
     *
     * @param CsvConfigurationInterface $config
     * @param resource                  $stream
     */
    public function __construct(CsvConfigurationInterface $config, $stream)
    {
        $this->stream = $stream;
        $this->config = $config;
    }

    /**
     * @return Iterator Iterator of csv line arrays
     */
    public function read()
    {
        $tokens = new StreamTokeniser($this->config, $this->stream);
        $parser = new Parser();
        return $parser->parse($tokens->getTokens());
    }
}
