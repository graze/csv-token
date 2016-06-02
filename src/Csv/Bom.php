<?php

namespace Graze\CsvToken\Csv;

use InvalidArgumentException;

class Bom
{
    /** UTF-8 BOM sequence */
    const BOM_UTF8 = "\xEF\xBB\xBF";
    /** UTF-16 BE BOM sequence */
    const BOM_UTF16_BE = "\xFE\xFF";
    /** UTF-16 LE BOM sequence */
    const BOM_UTF16_LE = "\xFF\xFE";
    /** UTF-32 BE BOM sequence */
    const BOM_UTF32_BE = "\x00\x00\xFE\xFF";
    /** UTF-32 LE BOM sequence */
    const BOM_UTF32_LE = "\x00\x00\xFF\xFE";

    /** @var string[] */
    static protected $encodingMap = [
        self::BOM_UTF8     => 'UTF-8',
        self::BOM_UTF16_BE => 'UTF-16BE',
        self::BOM_UTF16_LE => 'UTF-16LE',
        self::BOM_UTF32_BE => 'UTF-32BE',
        self::BOM_UTF32_LE => 'UTF-32LE',
    ];

    /**
     * @param string $bom
     *
     * @return string
     */
    public static function getEncoding($bom)
    {
        if (array_key_exists($bom, static::$encodingMap)) {
            return static::$encodingMap[$bom];
        } else {
            throw new InvalidArgumentException("Could not determine encoding from Byte Order Mark: " . $bom);
        }
    }
}
