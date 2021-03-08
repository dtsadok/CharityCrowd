<?php

namespace App\Service;

class MonthYearService
{
    public function getMonthNumberFromMonthName($month) : string
    {
        $date = \DateTimeImmutable::createFromFormat('d F', "1 $month");
        if (!$date) { $date = new \DateTimeImmutable(); }

        return $date->format('n');
    }
}
