Phperiod
========

[![Build Status](https://travis-ci.org/maidmaid/phperiod.svg?branch=master)](https://travis-ci.org/maidmaid/phperiod)

Showing translated and ranged DateTime in PHP.

Usage
-----

Same day:

```php
echo Phperiod::period(new DateTime('2016-10-15'));
// Saturday, October 15, 2016

echo Phperiod::period(new DateTime('2016-10-15 12:00'));
// Saturday, October 15, 2016 at 12:00 PM

echo Phperiod::period(new DateTime('2016-10-15 12:00'), new DateTime('2016-10-15 13:00'));
// Saturday, October 15, 2016 from 12:00 PM to 1:00 PM
```

Ranged dates:

```php
echo Phperiod::period(new DateTime('2016-10-15'), new DateTime('2016-10-17'));
// from Saturday, October 15, 2016 to Monday, October 17, 2016

echo Phperiod::period(new DateTime('2016-10-15 12:00'), new DateTime('2016-10-17'));
// from Saturday, October 15, 2016 to Monday, October 17, 2016 at 12:00 PM

echo Phperiod::period(new DateTime('2016-10-15 12:00'), new DateTime('2016-10-17 13:00'));
// from Saturday, October 15, 2016 to Monday, October 17, 2016 from 12:00 PM to 1:00 PM
```

Ranged dates with days of week: 

```php
echo Phperiod::period(new DateTime('2016-10-15'), new DateTime('2016-10-29'), array('Mon', 'Thu', 'Sat'));
// Monday, Thursday and Saturday, from Saturday, October 15, 2016 to Saturday, October 29, 2016

echo Phperiod::period(new DateTime('2016-10-15 12:00'), new DateTime('2016-10-29'), array('Mon', 'Thu', 'Sat'));
// Monday, Thursday and Saturday at 12:00 PM, from Saturday, October 15, 2016 to Saturday, October 29, 2016

echo Phperiod::period(new DateTime('2016-10-15 12:00'), new DateTime('2016-10-29 13:00'), array('Mon', 'Thu', 'Sat'));
// Monday, Thursday and Saturday from 12:00 PM to 1:00 PM, from Saturday, October 15, 2016 to Saturday, October 29, 2016
```

Translated dates:

```php
echo Phperiod::period(
    new DateTime('2016-10-15 12:00'),
    new DateTime('2016-10-29 13:00'),
    array('Mon', 'Thu', 'Sat'),
    new \IntlDateFormatter('fr')
);
// lundi, jeudi et samedi de 12:00 à 13:00, du samedi 15 octobre 2016 au samedi 29 octobre 2016
```

With custom format:

```php
echo Phperiod::period(
    new DateTime('2016-10-15 12:00'),
    new DateTime('2016-10-29 13:00'),
    array('Mon', 'Thu', 'Sat'),
    new \IntlDateFormatter('en', \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT)
);
// Monday, Thursday and Saturday from 12:00 PM to 1:00 PM, from 10/15/16 to 10/29/16
```

License
-------

Phperiod is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
