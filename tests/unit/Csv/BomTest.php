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

namespace Graze\CsvToken\Test\Unit\Csv;

use Graze\CsvToken\Csv\Bom;
use Graze\CsvToken\Test\TestCase;
use InvalidArgumentException;

class BomTest extends TestCase
{
    /**
     * @dataProvider getEncodingData
     *
     * @param string $bom
     * @param string $expectedEncoding
     */
    public function testGetEncoding($bom, $expectedEncoding)
    {
        static::assertEquals($expectedEncoding, Bom::getEncoding($bom));
    }

    /**
     * @return array
     */
    public function getEncodingData()
    {
        return [
            [Bom::BOM_UTF8, 'UTF-8'],
            [Bom::BOM_UTF16_BE, 'UTF-16BE'],
            [Bom::BOM_UTF16_LE, 'UTF-16LE'],
            [Bom::BOM_UTF32_BE, 'UTF-32BE'],
            [Bom::BOM_UTF32_LE, 'UTF-32LE'],
        ];
    }

    public function testGetEncodingWillThrowAnExceptionForUnknownBom()
    {
        static::expectException(InvalidArgumentException::class);
        Bom::getEncoding('random');
    }
}
