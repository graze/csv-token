<?php

namespace Graze\CsvToken\Tokeniser;

use Graze\CsvToken\Csv\CsvConfigurationInterface;
use GuzzleHttp\Psr7\Stream;

class StringTokeniser extends StreamTokeniser
{
    /**
     * StringTokeniser constructor.
     *
     * @param CsvConfigurationInterface $config
     * @param string                    $csv
     */
    public function __construct(CsvConfigurationInterface $config, $csv)
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $csv);
        rewind($stream);
        parent::__construct($config, new Stream($stream));
    }
}
