<?php

use Maidmaid\Phperiod\Phperiod;

require __DIR__.'/../vendor/autoload.php';

function example(IntlDateFormatter $formatter = null)
{
    print_r(array(
        Phperiod::period(new DateTime('2016-10-15'), null, array(), $formatter),
        Phperiod::period(new DateTime('2016-10-15 12:00'), null, array(), $formatter),
        Phperiod::period(new DateTime('2016-10-15 12:00'), new DateTime('2016-10-15 13:00'), array(), $formatter),
        Phperiod::period(new DateTime('2016-10-15'), new DateTime('2016-10-17'), array(), $formatter),
        Phperiod::period(new DateTime('2016-10-15 12:00'), new DateTime('2016-10-17'), array(), $formatter),
        Phperiod::period(new DateTime('2016-10-15 12:00'), new DateTime('2016-10-17 13:00'), array(), $formatter),
        Phperiod::period(new DateTime('2016-10-15'), new DateTime('2016-10-29'), array('Mon', 'Thu', 'Sat'), $formatter),
        Phperiod::period(new DateTime('2016-10-15 12:00'), new DateTime('2016-10-29 00:00'), array('Mon', 'Thu', 'Sat'), $formatter),
        Phperiod::period(new DateTime('2016-10-15 12:00'), new DateTime('2016-10-29 13:00'), array('Mon', 'Thu', 'Sat'), $formatter),
    ));
}

example();
example(new \IntlDateFormatter('fr', \IntlDateFormatter::FULL, \IntlDateFormatter::SHORT));
example(new \IntlDateFormatter('en', \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT));
