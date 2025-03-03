<?php

namespace Testbike;

require_once('tariff.php');
require_once('rental.php');

use Testbike\Tariff;
use Testbike\Rental;


class TariffBeta implements Tariff
{
    /**
     * [implementation]
     */
    public function calculatePrice(Rental $rental): float
    {
        // Convert duration to minutes, rounding up.
        $durationMinutes = ceil($rental->duration() / 60);

        if ($durationMinutes <= 20) {
            return 0;
        } elseif ($durationMinutes <= 60) {
            return 2;
        } elseif ($durationMinutes <= 120) {
            return 6;
        } elseif ($durationMinutes <= 720) { // up to 12 hours (720 minutes)
            $extraHours = ceil(($durationMinutes - 120) / 60);
            return 6 + ($extraHours * 4);
        } else {
            // Calculate the price for the first 12 hours:
            // Note: 720 - 120 = 600 minutes → exactly 10 extra hours.
            $basePrice = 6 + (10 * 4); // 6 + 40 = 46 PLN
            return $basePrice + 200;
        }
    }
}

