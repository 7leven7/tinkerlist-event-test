<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateTimeHelper
{

    public static function unixToDateTime($unixTimestamp)
    {
        $carbonDate = Carbon::createFromTimestamp($unixTimestamp);
        $date = ['date' => $carbonDate->toDateString(), 'time' => $carbonDate->toTimeString()];

        return $date;
    }

    public static function getDateFromTimestamp($dateTime)
    {
        $carbonDate = Carbon::parse($dateTime);
        $date = $carbonDate->toDateString();

        return $date;
    }

}

