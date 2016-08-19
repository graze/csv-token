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

namespace Graze\CsvToken\Test\Unit;

use Graze\CsvToken\Csv\CsvConfiguration;
use Graze\CsvToken\Csv\CsvConfigurationInterface;
use Graze\CsvToken\Reader;
use Graze\CsvToken\Test\TestCase;
use Mockery as m;

class ReaderTest extends TestCase
{
    /**
     * @dataProvider parseData
     *
     * @param CsvConfigurationInterface $config
     * @param string                    $csv
     * @param array                     $expected
     */
    public function testParse(CsvConfigurationInterface $config, $csv, array $expected)
    {
        $reader = new Reader($config, $this->getStream($csv));

        $results = iterator_to_array($reader->read());

        static::assertEquals($expected, $results);
    }

    /**
     * @return array
     */
    public function parseData()
    {
        return [
            [
                new CsvConfiguration(),
                '"some",\\N,"new' . "\n" . 'line",with\\' . "\n" . 'escaped,"in\\' . "\n" . 'quotes","\\\\"',
                [
                    ['some', null, "new\nline", "with\nescaped", "in\nquotes", '\\'],
                ],
            ],
        ];
    }
}
