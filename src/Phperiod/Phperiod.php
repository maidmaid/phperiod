<?php

namespace Maidmaid\Phperiod;

use DateTime;
use IntlDateFormatter;
use Maidmaid\Phperiod\Exception\FormatException;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;

class Phperiod
{
    /** @var \Symfony\Component\Translation\Translator */
    protected static $translator;

    /** @var IntlDateFormatter */
    protected static $formatter;

    /**
     * Format period.
     *
     * @param DateTime|string|null   $start
     * @param DateTime|string|null   $end
     * @param array                  $daysOfWeek
     * @param IntlDateFormatter|null $formatter
     *
     * @return string
     *
     * @throws FormatException
     */
    public static function period($start = null, $end = null, $daysOfWeek = array(), IntlDateFormatter $formatter = null)
    {
        $start = $start instanceof DateTime
            ? $start
            : new DateTime(is_string($start) ? $start : 'now')
        ;
        $end = $end instanceof DateTime
            ? $end
            : (is_string($end)
                ? new DateTime($end)
                : clone $start)
        ;
        static::$formatter = $formatter ?: new IntlDateFormatter(null, IntlDateFormatter::FULL, IntlDateFormatter::SHORT);

        static::setLocale(static::$formatter->getLocale());

        $o = array(
            '=d' => static::isSameDay($start, $end),
            '=h' => static::isSameHour($start, $end),
            's=00:00' => static::isMidnight($start),
            'e=00:00' => static::isMidnight($end),
            'dow' => 2 <= count($daysOfWeek),
        );

        switch (true) {
            // Example: Saturday, October 15, 2016
            case $o['=d'] && $o['=h'] && $o['s=00:00'] && $o['e=00:00'] && !$o['dow']:
                return static::formatDate($start);

            // Example: Saturday, October 15, 2016 at 12:00 PM
            case $o['=d'] && $o['=h'] && !$o['s=00:00'] && !$o['e=00:00'] && !$o['dow']:
                return static::formatDateTime($start);

            // Example: Saturday, October 15, 2016 from 12:00 AM to 1:00 PM
            case $o['=d'] && !$o['=h'] /* && $o['s=00:00'] && $o['e=00:00'] */ && !$o['dow']:
                return static::formatDateRangedTime($start, $end);

            // Example: from Saturday, October 15, 2016 to Monday, October 17, 2016
            case !$o['=d'] && $o['=h'] && $o['s=00:00'] && $o['e=00:00'] && !$o['dow']:
                return static::formatRangedDate($start, $end);

            // Example: from Saturday, October 15, 2016 to Monday, October 17, 2016 at 12:00 PM
            case !$o['=d'] && !$o['=h'] && !$o['s=00:00'] && $o['e=00:00'] && !$o['dow']:
                return static::formatRangedDateTime($start, $end);

            // Example: from Saturday, October 15, 2016 to Monday, October 17, 2016 from 12:00 PM to 1:00 PM
            case !$o['=d'] && !$o['=h'] && !$o['s=00:00'] && !$o['e=00:00'] && !$o['dow']:
                return static::formatRangedDateRangedTime($start, $end);

            // Example: Monday, Thursday and Saturday, from Saturday, October 15, 2016 to Saturday, October 29, 2016'
            case !$o['=d'] && $o['=h'] && $o['s=00:00'] && $o['e=00:00'] && $o['dow']:
                return static::formatRangedDateWithDaysOfWeek($start, $end, $daysOfWeek);

            // Example: Monday, Thursday and Saturday at 12:00, from Saturday, October 15, 2016 to Saturday, October 29, 2016'
            case !$o['=d'] && !$o['=h'] && !$o['s=00:00'] && $o['e=00:00'] && $o['dow']:
                return static::formatRangedDateTimeWithDaysOfWeek($start, $end, $daysOfWeek);

            // Example: Monday, Thursday and Saturday from 12:00 to 13:00, from Saturday, October 15, 2016 to Saturday, October 29, 2016'
            case !$o['=d'] && !$o['=h'] /* && $o['s=00:00'] && $o['e=00:00'] */ && $o['dow']:
                return static::formatRangedDateRangedTimeWithDaysOfWeek($start, $end, $daysOfWeek);

            default:
                throw new FormatException('Impossible to format this period');
        }
    }

    protected static function translator()
    {
        if (null === static::$translator) {
            static::$translator = new Translator('en');
            static::$translator->addLoader('array', new ArrayLoader());
            static::setLocale('en');
        }

        return static::$translator;
    }

    protected static function setLocale($locale)
    {
        if (file_exists($filename = __DIR__.'/Lang/'.$locale.'.php')) {
            static::translator()->setLocale($locale);
            $resource = require $filename;
            static::$translator->addResource('array', $resource['*'], $locale);
            static::$translator->addResource('array', $resource['time'], $locale, 'time');
            static::$translator->addResource('array', $resource['date'], $locale, 'date');
        }
    }

    protected static function isMidnight(DateTime $date)
    {
        return '00:00' === $date->format('H:i');
    }

    protected static function isSameDay(DateTime $date1, DateTime $date2)
    {
        return $date1->format('Ymd') === $date2->format('Ymd');
    }

    protected static function isSameHour(DateTime $date1, DateTime $date2)
    {
        return $date1->format('H:i') === $date2->format('H:i');
    }

    protected static function format($date, $locale, $datetype, $timetype, $timezone = null, $calendar = null, $pattern = null)
    {
        $timezone = $timezone ?: static::$formatter->getTimeZone();
        $calendar = $calendar ?: static::$formatter->getCalendar();

        return IntlDateFormatter::create($locale, $datetype, $timetype, $timezone, $calendar, $pattern)->format($date);
    }

    protected static function formatDate($date)
    {
        return static::format($date, static::$formatter->getLocale(), static::$formatter->getDateType(), IntlDateFormatter::NONE);
    }

    protected static function formatTime($time)
    {
        return static::format($time, static::$formatter->getLocale(), IntlDateFormatter::NONE, static::$formatter->getTimeType());
    }

    protected static function formatDayOfWeek($dayOfWeek)
    {
        return static::format(new DateTime($dayOfWeek), static::translator()->getLocale(), null, null, null, null, 'EEEE');
    }

    protected static function formatRangedDate($start, $end)
    {
        return sprintf(
            '%s %s %s %s',
            static::translator()->trans('from', array(), 'date'),
            static::formatDate($start),
            static::translator()->trans('to', array(), 'date'),
            static::formatDate($end)
        );
    }

    protected static function formatRangedTime($start, $end)
    {
        return sprintf(
            '%s %s %s %s',
            static::translator()->trans('from', array(), 'time'),
            static::formatTime($start),
            static::translator()->trans('to', array(), 'time'),
            static::formatTime($end)
        );
    }

    protected static function formatDaysOfWeek($daysOfWeek)
    {
        $daysOfWeek = array_map(function ($dayOfWeek) {
            return static::formatDayOfWeek($dayOfWeek);
        }, $daysOfWeek);

        $last = end($daysOfWeek);
        array_pop($daysOfWeek);

        return sprintf(
            '%s %s %s',
            implode(', ', $daysOfWeek),
            static::translator()->trans('and'),
            $last
        );
    }

    protected static function formatDateTime($date, $time = null)
    {
        $time = $time ?: $date;

        return sprintf(
            '%s %s %s',
            static::formatDate($date),
            static::translator()->trans('at', array(), 'time'),
            static::formatTime($time)
        );
    }

    protected static function formatDateRangedTime($start, $end)
    {
        return sprintf(
            '%s %s',
            static::formatDate($end),
            static::formatRangedTime($start, $end)
        );
    }

    protected static function formatRangedDateTime($start, $end)
    {
        return sprintf(
            '%s %s %s %s',
            static::translator()->trans('from', array(), 'date'),
            static::formatDate($start),
            static::translator()->trans('to', array(), 'date'),
            static::formatDateTime($end, $start)
        );
    }

    protected static function formatRangedDateRangedTime($start, $end)
    {
        return sprintf(
            '%s %s %s %s',
            static::translator()->trans('from', array(), 'date'),
            static::formatDate($start),
            static::translator()->trans('to', array(), 'date'),
            static::formatDateRangedTime($start, $end)
        );
    }

    protected static function formatRangedDateWithDaysOfWeek($start, $end, $daysOfWeek)
    {
        return sprintf(
            '%s, %s',
            static::formatDaysOfWeek($daysOfWeek),
            static::formatRangedDate($start, $end)
        );
    }

    protected static function formatRangedDateTimeWithDaysOfWeek($start, $end, $daysOfWeek)
    {
        return sprintf(
            '%s %s %s, %s',
            static::formatDaysOfWeek($daysOfWeek),
            static::translator()->trans('at', array(), 'time'),
            static::formatTime($start),
            static::formatRangedDate($start, $end)
        );
    }

    protected static function formatRangedDateRangedTimeWithDaysOfWeek($start, $end, $daysOfWeek)
    {
        return sprintf(
            '%s %s, %s',
            static::formatDaysOfWeek($daysOfWeek),
            static::formatRangedTime($start, $end),
            static::formatRangedDate($start, $end)
        );
    }
}
