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
        parent::__construct($config, $stream);
    }
}
