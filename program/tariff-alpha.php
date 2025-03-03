<?php

namespace Testbike;

require_once('tariff.php');
require_once('rental.php');

use Testbike\Tariff;
use Testbike\Rental;


class TariffAlpha implements Tariff
{
    /**
     * [implementation]
     */
    public function calculatePrice(Rental $rental): float
    {
        $duration = $rental->duration(); // in seconds
        $secondsPerDay = 86400; // 24 hours
        $fullDays = floor($duration / $secondsPerDay);
        $remainingSeconds = $duration % $secondsPerDay;

        // Each full day costs 9 EUR.
        $price = $fullDays * 9;

        // For the remaining time, charge 1 EUR per 30 minutes (or part thereof),
        // but do not exceed 9 EUR for the partial day.
        $halfHours = ceil($remainingSeconds / (30 * 60));
        $remainingCost = min($halfHours * 1, 9);

        return $price + $remainingCost;
    }
}

