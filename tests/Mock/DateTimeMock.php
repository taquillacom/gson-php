<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Test\Mock;

use DateTime;

/**
 * Class DateTimeMock
 *
 * @author Nate Brunette <n@tebru.net>
 */
class DateTimeMock extends DateTime
{
    public static function create(DateTime $dateTime)
    {
        return new static($dateTime->format(DateTime::ATOM), $dateTime->getTimezone());
    }

    public static function createFromFormat ($format, $datetime, $timezone = null)
    {
        $dateTime = parent::createFromFormat($format, $datetime, $timezone);

        return self::create($dateTime);
    }
}
