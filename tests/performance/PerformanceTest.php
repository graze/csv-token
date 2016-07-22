<?php

namespace Graze\CsvToken\Test\Performance;

use Graze\CsvToken\Csv\CsvConfiguration;
use Graze\CsvToken\Parser;
use Graze\CsvToken\Test\TestCase;
use Graze\CsvToken\Tokeniser\StreamTokeniser;

class PerformanceTest extends TestCase
{
    /**
     * @dataProvider getFixtures
     *
     * @param string $path
     */
    public function testPerformance($path)
    {
        $iterations = array_pad([], 5, -1);

        foreach ($iterations as &$iteration) {
            $start = microtime(true);
            $parser = new Parser();
            $tokeniser = new StreamTokeniser(new CsvConfiguration(), fopen($path, 'r'));
            foreach ($parser->parse($tokeniser->getTokens()) as $row) {
                echo '';
            }
            $iteration = microtime(true) - $start;
        }

        $average = array_sum($iterations) / count($iterations);

        echo sprintf("Path: %s - iterations: %d - average: %.2f ms", $path, count($iterations), $average * 1000);
    }

    /**
     * @return array
     */
    public function getFixtures()
    {
        return [
            [__DIR__ . '/fixture.csv'],
        ];
    }
}
