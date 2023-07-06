<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateTimeHelper
{

    /**
     * @param $unixTimestamp
     * @return array
     */
    public static function unixToDateTime($unixTimestamp): array
    {
        $carbonDate = Carbon::createFromTimestamp($unixTimestamp);
        $date = ['date' => $carbonDate->toDateString(), 'time' => $carbonDate->toTimeString()];

        return $date;
    }

    /**
     * @param $dateTime
     * @return string
     */
    public static function getDateFromTimestamp($dateTime): string
    {
        $carbonDate = Carbon::parse($dateTime);
        return $carbonDate->toDateString();
    }

}

