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

namespace Graze\CsvToken\Test\Unit\Format;

use Graze\CsvToken\Csv\CsvConfiguration;
use Graze\CsvToken\Csv\CsvConfigurationInterface;
use Graze\CsvToken\Test\TestCase;

class CsvConfigurationTest extends TestCase
{
    public function testImplementsInterface()
    {
        $definition = new CsvConfiguration();

        static::assertInstanceOf(CsvConfigurationInterface::class, $definition);
    }

    public function testDefaultsAreAssignedWhenNoOptionsSupplied()
    {
        $definition = new CsvConfiguration();

        static::assertEquals(',', $definition->getDelimiter(), "Default Delimiter should be ','");
        static::assertEquals('"', $definition->getQuote(), "Default quote character should be \"");
        static::assertEquals('\\N', $definition->getNullValue(), "Null character should be '\\N'");
        static::assertEquals("\n", $definition->getNewLine(), "Line terminator should be '\\n'");
        static::assertEquals('\\', $definition->getEscape(), "Default escape character should be '\\'");
        static::assertEquals(false, $definition->useDoubleQuotes(), "Double quote should be off by default");
        static::assertEquals(null, $definition->getBom(), "By default BOM should be set to null");
        static::assertEquals('UTF-8', $definition->getEncoding(), "Default encoding should be 'UTF-8'");
    }

    public function testAssigningOptionsModifiesTheDefinition()
    {
        $definition = new CsvConfiguration([
            'delimiter'   => "\t",
            'quote'       => '',
            'null'        => '',
            'newLine'     => "----",
            'escape'      => '"',
            'doubleQuote' => true,
        ]);

        static::assertEquals("\t", $definition->getDelimiter(), "Delimiter should be set to '\\t' (tab)");
        static::assertEquals('', $definition->getQuote(), "Quote character should be blank");
        static::assertEquals('', $definition->getNullValue(), "Null character should be '' (blank)'");
        static::assertEquals("----", $definition->getNewLine(), "Line terminator should be '----'");
        static::assertEquals('"', $definition->getEscape(), 'Escape Character should be "');
        static::assertEquals(true, $definition->useDoubleQuotes(), 'double quote should be on');
    }
}
