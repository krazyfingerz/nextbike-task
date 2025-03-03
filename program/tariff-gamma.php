<?php

namespace Testbike;

require_once('tariff.php');
require_once('rental.php');

use Testbike\Tariff;
use Testbike\Rental;


class TariffGamma implements Tariff
{
    /**
     * [implementation]
     */
    public function calculatePrice(Rental $rental): float
    {
        $startTimestamp = $rental->getStartTime();
        $endTimestamp   = $rental->getEndTime();
        $totalPrice     = 0.0;

        // Create DateTime objects for the rental period.
        $startDateTime = new \DateTime();
        $startDateTime->setTimestamp($startTimestamp);
        $endDateTime = new \DateTime();
        $endDateTime->setTimestamp($endTimestamp);

        // Iterate over each calendar day touched by the rental.
        $currentDate = (clone $startDateTime)->setTime(0, 0, 0);
        $endDate = (clone $endDateTime)->setTime(0, 0, 0);

        while ($currentDate <= $endDate) {
            $dayStart = (clone $currentDate);
            $dayEnd   = (clone $currentDate)->modify('+1 day');

            // Determine the overlap of the rental with this calendar day.
            $intervalStart = max($startTimestamp, $dayStart->getTimestamp());
            $intervalEnd   = min($endTimestamp, $dayEnd->getTimestamp());
            if ($intervalEnd <= $intervalStart) {
                $currentDate->modify('+1 day');
                continue;
            }

            // Calculate daytime cost (09:00 to 18:00).
            $daytimeStart = (clone $currentDate)->setTime(9, 0, 0);
            $daytimeEnd   = (clone $currentDate)->setTime(18, 0, 0);
            $daytimeOverlap = max(0, min($intervalEnd, $daytimeEnd->getTimestamp()) - max($intervalStart, $daytimeStart->getTimestamp()));
            $daytimeHours = $daytimeOverlap / 3600.0;
            // Charge 2 EUR per hour with a maximum of 16 EUR per day.
            $dayCost = min(2 * $daytimeHours, 16);

            // Calculate nighttime cost (all time in the day outside 09:00-18:00).
            $totalInterval = $intervalEnd - $intervalStart;
            $nighttimeSeconds = $totalInterval - $daytimeOverlap;
            $nighttimeHours = $nighttimeSeconds / 3600.0;
            // Nighttime rate: base 2 EUR/h plus an extra 2 EUR/h, i.e. 4 EUR/h.
            $nightCost = 4 * $nighttimeHours;

            $totalPrice += $dayCost + $nightCost;
            $currentDate->modify('+1 day');
        }

        return $totalPrice;
    }
}

