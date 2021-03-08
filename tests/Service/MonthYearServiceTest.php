<?php

namespace App\Tests\Service;

use App\Entity\Nomination;
use App\Service\MonthYearService;
use PHPUnit\Framework\TestCase;

class MonthYearServiceTest extends TestCase
{
    public function testGetMonthNumberFromMonthName()
    {
        $monthYearService = new MonthYearService();
        $monthNumber = $monthYearService->getMonthNumberFromMonthName("January");
        $this->assertEquals("1", $monthNumber);

        $monthNumber = $monthYearService->getMonthNumberFromMonthName("June");
        $this->assertEquals("6", $monthNumber);

        $monthNumber = $monthYearService->getMonthNumberFromMonthName("December");
        $this->assertEquals("12", $monthNumber);
    }
}
