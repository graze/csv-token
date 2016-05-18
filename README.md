# csv-token

[![Latest Version on Packagist](https://img.shields.io/packagist/v/graze/csv-token.svg?style=flat-square)](https://packagist.org/packages/graze/csv-token)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/graze/csv-token/master.svg?style=flat-square)](https://travis-ci.org/graze/csv-token)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/graze/csv-token.svg?style=flat-square)](https://scrutinizer-ci.com/g/graze/csv-token/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/graze/csv-token.svg?style=flat-square)](https://scrutinizer-ci.com/g/graze/csv-token)
[![Total Downloads](https://img.shields.io/packagist/dt/graze/csv-token.svg?style=flat-square)](https://packagist.org/packages/graze/csv-token)

Tokenised Csv Reader that handles some of the strange configurations databases and application use.

- Parses tokens and csv from streams and outputs using a Lazy Iterator

| Csv Feature                | Example                  | Array                         |
|----------------------------|--------------------------|-------------------------------|
| Delimiter                  | `thing|other`            | `['thing','other']`           |
| Quote Enclosure            | `"quote, here",not here` | `['quote, here', 'not here']` |
| Escaping                   | `"\"text","new\\nline"`  | `['"text',"new\\\nline"]`     |
| Double Quotes and Escaping | `"""text","\, text"`     | `['"text',', text']`          |
| Null value parsing         | `"text",\\N,"text"`      | `['text',null,'text']`        |
| Boolean value parsing      | `"text",false,true`      | `['text',false,true]`         |
| Numeric value parsing      | `"text",1,-2.3,3.1e-24`  | `['text',1,-2.3,3.1e-24]`     |

## Install

Via Composer

``` bash
$ composer require graze/csv-token
```

## Usage

``` php
$csvDefiniton = new CsvDefinition();
$parser = new Parser();
$tokeniser = new StreamTokeniser($csvDefinition, $stream);
$csvIterator = $parser->parser($tokens->getTokens());
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ make test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email security@graze.com instead of using the issue tracker.

## Credits

- [Harry Bragg](https://github.com/h-bragg)
- [All Contributors](../../contributors)
- Original Idea: [jfsimon/php-csv](https://github.com/jfsimon/php-csv)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
