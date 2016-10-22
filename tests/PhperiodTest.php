<?php

namespace Maidmaid\Phperiod\Test;

use Maidmaid\Phperiod\Phperiod;

class PhperiodTest extends \PHPUnit_Framework_TestCase
{
    protected $supportedLocales;
    protected $unsupportedLocales;

    protected function setUp()
    {
        date_default_timezone_set('Europe/Zurich');

        // Supported locales
        $files = scandir(__DIR__.'/../src/Phperiod/Lang');
        $this->supportedLocales = array_filter(array_map(function ($file) {
            preg_match('/^([[:alpha:]]{2}).php/', $file, $matches);

            return isset($matches[1]) ? $matches[1] : null;
        }, $files));

        // Unsupported locales
        $supportedLocales = implode('|', $this->supportedLocales);
        $this->unsupportedLocales = $l = array_filter(\ResourceBundle::getLocales(''), function ($locale) use ($supportedLocales) {
            preg_match('/'.$supportedLocales.'/', $locale, $matches);

            return $matches ? null : $locale;
        });
    }

    /**
     * @dataProvider providePeriod
     */
    public function testPeriod($expected, $start, $end, $daysOfWeek, $formatter)
    {
        $this->assertEquals($expected, Phperiod::period($start, $end, $daysOfWeek, $formatter));
    }

    public function providePeriod()
    {
        $short = new \IntlDateFormatter('en', \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT);
        $medium = new \IntlDateFormatter('en', \IntlDateFormatter::MEDIUM, \IntlDateFormatter::MEDIUM);
        $long = new \IntlDateFormatter('en', \IntlDateFormatter::LONG, \IntlDateFormatter::LONG);
        $full = new \IntlDateFormatter('en', \IntlDateFormatter::FULL, \IntlDateFormatter::FULL);
        $gmt0 = new \IntlDateFormatter('en', \IntlDateFormatter::FULL, \IntlDateFormatter::FULL, 'GMT+0');
        $trad = new \IntlDateFormatter('en', \IntlDateFormatter::FULL, \IntlDateFormatter::FULL, null, \IntlDateFormatter::TRADITIONAL);
        $en = new \IntlDateFormatter('en', \IntlDateFormatter::FULL, \IntlDateFormatter::SHORT);
        $fr = new \IntlDateFormatter('fr', \IntlDateFormatter::FULL, \IntlDateFormatter::SHORT);
        $de = new \IntlDateFormatter('de', \IntlDateFormatter::FULL, \IntlDateFormatter::SHORT);

        return array(
            // different formatter
            array('from 10/15/16 to 10/17/16 from 12:00 PM to 1:00 PM', '2016-10-15 12:00', '2016-10-17 13:00', array(), $short),
            array('from Oct 15, 2016 to Oct 17, 2016 from 12:00:00 PM to 1:00:00 PM', '2016-10-15 12:00', '2016-10-17 13:00', array(), $medium),
            //array('from October 15, 2016 to October 17, 2016 from 12:00:00 PM GMT+2 to 1:00:00 PM GMT+2', '2016-10-15 12:00', '2016-10-17 13:00', array(), $long),
            array('from Saturday, October 15, 2016 to Monday, October 17, 2016 from 12:00:00 PM Central European Summer Time to 1:00:00 PM Central European Summer Time', '2016-10-15 12:00', '2016-10-17 13:00', array(), $full),
            array('from Saturday, October 15, 2016 to Monday, October 17, 2016 from 10:00:00 AM GMT to 11:00:00 AM GMT', '2016-10-15 12:00', '2016-10-17 13:00', array(), $gmt0),
            array('from Saturday, October 15, 2016 to Monday, October 17, 2016 from 12:00:00 PM Central European Summer Time to 1:00:00 PM Central European Summer Time', '2016-10-15 12:00', '2016-10-17 13:00', array(), $trad),

            // EN
            array('Saturday, October 15, 2016', '2016-10-15 00:00', null, array(), $en),
            array('Saturday, October 15, 2016', '2016-10-15 00:00', '2016-10-15 00:00', array(), $en),
            array('Saturday, October 15, 2016 at 12:00 PM', '2016-10-15 12:00', null, array(), $en),
            array('Saturday, October 15, 2016 at 12:00 PM', '2016-10-15 12:00', '2016-10-15 12:00', array(), $en),
            array('Saturday, October 15, 2016 at 12:00 PM', '2016-10-15 12:00', '2016-10-15 00:00', array(), $en),
            array('Saturday, October 15, 2016 from 12:00 AM to 1:00 PM', '2016-10-15 00:00', '2016-10-15 13:00', array(), $en),
            array('Saturday, October 15, 2016 from 12:00 PM to 1:00 PM', '2016-10-15 12:00', '2016-10-15 13:00', array(), $en),
            array('from Saturday, October 15, 2016 to Monday, October 17, 2016', '2016-10-15 00:00', '2016-10-17 00:00', array(), $en),
            array('from Saturday, October 15, 2016 to Monday, October 17, 2016 at 12:00 PM', '2016-10-15 12:00', '2016-10-17 12:00', array(), $en),
            array('from Saturday, October 15, 2016 to Monday, October 17, 2016 at 12:00 PM', '2016-10-15 12:00', '2016-10-17 00:00', array(), $en),
            array('from Saturday, October 15, 2016 to Monday, October 17, 2016 from 12:00 PM to 1:00 PM', '2016-10-15 12:00', '2016-10-17 13:00', array(), $en),
            array('Monday, Thursday and Saturday, from Saturday, October 15, 2016 to Saturday, October 29, 2016', '2016-10-15 00:00', '2016-10-29 00:00', array('Mon', 'Thu', 'Sat'), $en),
            array('Monday, Thursday and Saturday at 12:00 PM, from Saturday, October 15, 2016 to Saturday, October 29, 2016', '2016-10-15 12:00', '2016-10-29 00:00', array('Mon', 'Thu', 'Sat'), $en),
            array('Monday, Thursday and Saturday from 12:00 PM to 1:00 PM, from Saturday, October 15, 2016 to Saturday, October 29, 2016', '2016-10-15 12:00', '2016-10-29 13:00', array('Mon', 'Thu', 'Sat'), $en),

            // FR
            array('samedi 15 octobre 2016', '2016-10-15 00:00', null, array(), $fr),
            array('samedi 15 octobre 2016', '2016-10-15 00:00', '2016-10-15 00:00', array(), $fr),
            array('samedi 15 octobre 2016 à 12:00', '2016-10-15 12:00', null, array(), $fr),
            array('samedi 15 octobre 2016 à 12:00', '2016-10-15 12:00', '2016-10-15 12:00', array(), $fr),
            array('samedi 15 octobre 2016 à 12:00', '2016-10-15 12:00', '2016-10-15 00:00', array(), $fr),
            array('samedi 15 octobre 2016 de 00:00 à 13:00', '2016-10-15 00:00', '2016-10-15 13:00', array(), $fr),
            array('samedi 15 octobre 2016 de 12:00 à 13:00', '2016-10-15 12:00', '2016-10-15 13:00', array(), $fr),
            array('du samedi 15 octobre 2016 au lundi 17 octobre 2016', '2016-10-15 00:00', '2016-10-17 00:00', array(), $fr),
            array('du samedi 15 octobre 2016 au lundi 17 octobre 2016 à 12:00', '2016-10-15 12:00', '2016-10-17 12:00', array(), $fr),
            array('du samedi 15 octobre 2016 au lundi 17 octobre 2016 à 12:00', '2016-10-15 12:00', '2016-10-17 00:00', array(), $fr),
            array('du samedi 15 octobre 2016 au lundi 17 octobre 2016 de 12:00 à 13:00', '2016-10-15 12:00', '2016-10-17 13:00', array(), $fr),
            array('lundi, jeudi et samedi, du samedi 15 octobre 2016 au samedi 29 octobre 2016', '2016-10-15 00:00', '2016-10-29 00:00', array('Mon', 'Thu', 'Sat'), $fr),
            array('lundi, jeudi et samedi à 12:00, du samedi 15 octobre 2016 au samedi 29 octobre 2016', '2016-10-15 12:00', '2016-10-29 00:00', array('Mon', 'Thu', 'Sat'), $fr),
            array('lundi, jeudi et samedi de 12:00 à 13:00, du samedi 15 octobre 2016 au samedi 29 octobre 2016', '2016-10-15 12:00', '2016-10-29 13:00', array('Mon', 'Thu', 'Sat'), $fr),

            // DE
            array('Samstag, 15. Oktober 2016', '2016-10-15 00:00', null, array(), $de),
            array('Samstag, 15. Oktober 2016', '2016-10-15 00:00', '2016-10-15 00:00', array(), $de),
            array('Samstag, 15. Oktober 2016 um 12:00', '2016-10-15 12:00', null, array(), $de),
            array('Samstag, 15. Oktober 2016 um 12:00', '2016-10-15 12:00', '2016-10-15 12:00', array(), $de),
            array('Samstag, 15. Oktober 2016 um 12:00', '2016-10-15 12:00', '2016-10-15 00:00', array(), $de),
            array('Samstag, 15. Oktober 2016 von 00:00 bis 13:00', '2016-10-15 00:00', '2016-10-15 13:00', array(), $de),
            array('Samstag, 15. Oktober 2016 von 12:00 bis 13:00', '2016-10-15 12:00', '2016-10-15 13:00', array(), $de),
            array('von Samstag, 15. Oktober 2016 bis Montag, 17. Oktober 2016', '2016-10-15 00:00', '2016-10-17 00:00', array(), $de),
            array('von Samstag, 15. Oktober 2016 bis Montag, 17. Oktober 2016 um 12:00', '2016-10-15 12:00', '2016-10-17 12:00', array(), $de),
            array('von Samstag, 15. Oktober 2016 bis Montag, 17. Oktober 2016 um 12:00', '2016-10-15 12:00', '2016-10-17 00:00', array(), $de),
            array('von Samstag, 15. Oktober 2016 bis Montag, 17. Oktober 2016 von 12:00 bis 13:00', '2016-10-15 12:00', '2016-10-17 13:00', array(), $de),
            array('Montag, Donnerstag und Samstag, von Samstag, 15. Oktober 2016 bis Samstag, 29. Oktober 2016', '2016-10-15 00:00', '2016-10-29 00:00', array('Mon', 'Thu', 'Sat'), $de),
            array('Montag, Donnerstag und Samstag um 12:00, von Samstag, 15. Oktober 2016 bis Samstag, 29. Oktober 2016', '2016-10-15 12:00', '2016-10-29 00:00', array('Mon', 'Thu', 'Sat'), $de),
            array('Montag, Donnerstag und Samstag von 12:00 bis 13:00, von Samstag, 15. Oktober 2016 bis Samstag, 29. Oktober 2016', '2016-10-15 12:00', '2016-10-29 13:00', array('Mon', 'Thu', 'Sat'), $de),
        );
    }

    /**
     * @dataProvider provideInvalidPeriod
     * @expectedException \Maidmaid\Phperiod\Exception\FormatException
     */
    public function testInvalidPeriod($start, $end, $daysOfWeek)
    {
        Phperiod::period($start, $end, $daysOfWeek);
    }

    public function provideInvalidPeriod()
    {
        return array(
            array('2016-10-15 00:00', null, array('Mon', 'Thu')),
        );
    }

    /**
     * @dataProvider provideGenericPeriod
     */
    public function testGenericPeriod($pattern, $start, $end, $daysOfWeek)
    {
        foreach ($this->unsupportedLocales as $locale) {
            $formatter = new \IntlDateFormatter($locale, \IntlDateFormatter::FULL, \IntlDateFormatter::SHORT);
            $this->assertRegExp($pattern, Phperiod::period($start, $end, $daysOfWeek, $formatter));
        }
    }

    public function provideGenericPeriod()
    {
        return array(
            array('/.+/', '2016-10-15 00:00', null, array()),
            array('/.+/', '2016-10-15 00:00', '2016-10-15 00:00', array()),
            array('/.+/', '2016-10-15 12:00', null, array()),
            array('/.+/', '2016-10-15 12:00', '2016-10-15 12:00', array()),
            array('/.+/', '2016-10-15 12:00', '2016-10-15 00:00', array()),
            array('/.+→.+/', '2016-10-15 00:00', '2016-10-15 13:00', array()),
            array('/.+→.+/', '2016-10-15 12:00', '2016-10-15 13:00', array()),
            array('/.+→.+/', '2016-10-15 00:00', '2016-10-17 00:00', array()),
            array('/.+→.+/', '2016-10-15 12:00', '2016-10-17 12:00', array()),
            array('/.+→.+/', '2016-10-15 12:00', '2016-10-17 00:00', array()),
            array('/.+→.+→.+/', '2016-10-15 12:00', '2016-10-17 13:00', array()),
            array('/.+\+.+→.+/', '2016-10-15 00:00', '2016-10-29 00:00', array('Mon', 'Thu', 'Sat')),
            array('/.+\+.+→.+/', '2016-10-15 12:00', '2016-10-29 00:00', array('Mon', 'Thu', 'Sat')),
            array('/.+\+.+→.+→.+/', '2016-10-15 12:00', '2016-10-29 13:00', array('Mon', 'Thu', 'Sat')),
        );
    }
}
