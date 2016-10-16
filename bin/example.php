<?php

use Maidmaid\Phperiod\Phperiod;

require __DIR__.'/../vendor/autoload.php';

function example(IntlDateFormatter $formatter = null)
{
    print_r(array(
        Phperiod::period('2016-10-15', null, [], $formatter),
        Phperiod::period('2016-10-15 12:00', null, [], $formatter),
        Phperiod::period('2016-10-15 12:00', '2016-10-15 13:00', [], $formatter),
        Phperiod::period('2016-10-15', '2016-10-17', [], $formatter),
        Phperiod::period('2016-10-15 12:00', '2016-10-17', [], $formatter),
        Phperiod::period('2016-10-15 12:00', '2016-10-17 13:00', [], $formatter),
        Phperiod::period('2016-10-15', '2016-10-29', ['Mon', 'Thu', 'Sat'], $formatter),
        Phperiod::period('2016-10-15 12:00', '2016-10-29 00:00', ['Mon', 'Thu', 'Sat'], $formatter),
        Phperiod::period('2016-10-15 12:00', '2016-10-29 13:00', ['Mon', 'Thu', 'Sat'], $formatter),
    ));
}

example();
example(new \IntlDateFormatter('fr', \IntlDateFormatter::FULL, \IntlDateFormatter::SHORT));
example(new \IntlDateFormatter('en', \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT));
