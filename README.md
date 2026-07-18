# 🐪💼 PHP Camel Caser

[![Tests](https://github.com/Jord-JD/php-camel-caser/actions/workflows/tests.yml/badge.svg)](https://github.com/Jord-JD/php-camel-caser/actions/workflows/tests.yml)
[![Packagist](https://img.shields.io/packagist/dt/jord-jd/php-camel-caser.svg)](https://packagist.org/packages/jord-jd/php-camel-caser/stats)

This package lets you use built-in PHP functions in camel case.

## Installation


PHP Camel Caser can be easily installed using Composer. Just run the following command from the root of your project.

```
composer require jord-jd/php-camel-caser
```

If you have never used the Composer dependency manager before, head to the [Composer website](https://getcomposer.org/) for more information on how to get started.

## Usage

After installing PHP Camel Caser, the new functions are available straight away.

Some example usage is shown below.

```php
require_once __DIR__.'/vendor/autoload.php';

strReplace('c', 'b', 'cat');                // bat
strWordCount("Hello world!");               // 2
inArray('Picard', ['Picard', 'Janeway']);   // true

// and so on...
```
