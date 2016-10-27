Phperiod
========

[![Build Status](https://travis-ci.org/maidmaid/phperiod.svg?branch=master)](https://travis-ci.org/maidmaid/phperiod)   

Showing translated and ranged DateTime in PHP. 

Require PHP >= 5.6 with Intl extension.

## Installation

Use [Composer](http://getcomposer.org/) to install Phperiod in your project:

```shell
composer require "maidmaid/phperiod"
```


## Usage

Same day:

```php
echo Phperiod::period('2016-10-15');
// Saturday, October 15, 2016

echo Phperiod::period('2016-10-15 12:00');
// Saturday, October 15, 2016 at 12:00 PM

echo Phperiod::period('2016-10-15 12:00', '2016-10-15 13:00');
// Saturday, October 15, 2016 from 12:00 PM to 1:00 PM
```

Ranged dates:

```php
echo Phperiod::period('2016-10-15', '2016-10-17');
// from Saturday, October 15, 2016 to Monday, October 17, 2016

echo Phperiod::period('2016-10-15 12:00', '2016-10-17');
// from Saturday, October 15, 2016 to Monday, October 17, 2016 at 12:00 PM

echo Phperiod::period('2016-10-15 12:00', '2016-10-17 13:00');
// from Saturday, October 15, 2016 to Monday, October 17, 2016 from 12:00 PM to 1:00 PM
```

Ranged dates with days of week: 

```php
echo Phperiod::period('2016-10-15', '2016-10-29', ['Mon', 'Thu', 'Sat']);
// Monday, Thursday and Saturday, from Saturday, October 15, 2016 to Saturday, October 29, 2016

echo Phperiod::period('2016-10-15 12:00', '2016-10-29', ['Mon', 'Thu', 'Sat']);
// Monday, Thursday and Saturday at 12:00 PM, from Saturday, October 15, 2016 to Saturday, October 29, 2016

echo Phperiod::period('2016-10-15 12:00', '2016-10-29 13:00', ['Mon', 'Thu', 'Sat']);
// Monday, Thursday and Saturday from 12:00 PM to 1:00 PM, from Saturday, October 15, 2016 to Saturday, October 29, 2016
```

With custom format:

```php
$formatter = new \IntlDateFormatter('en', \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT);
echo Phperiod::period('2016-10-15 12:00', '2016-10-29 13:00', ['Mon', 'Thu', 'Sat'], $formatter);
// Monday, Thursday and Saturday from 12:00 PM to 1:00 PM, from 10/15/16 to 10/29/16
```

Translated dates:

```php
$fr = new \IntlDateFormatter('fr');
echo Phperiod::period('2016-10-15 12:00', '2016-10-29 13:00', ['Mon', 'Thu', 'Sat'], $fr);
// lundi, jeudi et samedi de 12:00 à 13:00, du samedi 15 octobre 2016 au samedi 29 octobre 2016

// if special keywords don't have translation, they are remplaced by generic
// symbols ('from' and 'to' remplaced by '→'), like with Zulu locale
$zu = new \IntlDateFormatter('zu', \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT);
echo Phperiod::period('2016-10-15 12:00', '2016-10-17 13:00', [], $fr);
// 10/15/16 → 10/17/16 12:00 Ntambama → 1:00 Ntambama
```

## License

Phperiod is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
