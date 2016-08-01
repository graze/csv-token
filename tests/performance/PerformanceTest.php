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
     * @param int    $iterations
     */
    public function testPerformance($path, $iterations)
    {
        $runs = array_pad([], $iterations, -1);

        foreach ($runs as &$iteration) {
            $start = microtime(true);
            $parser = new Parser();
            $tokeniser = new StreamTokeniser(new CsvConfiguration(), fopen($path, 'r'));
            foreach ($parser->parse($tokeniser->getTokens()) as $row) {
                echo '';
            }
            $iteration = microtime(true) - $start;
        }

        $average = array_sum($runs) / count($runs);

        printf("\nPath: %s - iterations: %d - average: %.2f ms\n", $path, count($runs), $average * 1000);
    }

    /**
     * @return array
     */
    public function getFixtures()
    {
        return [
            [__DIR__ . '/fixture.csv', 20],
            [__DIR__ . '/big.csv', 5],
        ];
    }
}
