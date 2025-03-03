<?php

namespace Testbike;


/**
 * interface for calculating the price of a rental
 */
interface Tariff
{
    /**
     * @param Rental $rental
     * @return float
     */
    public function calculatePrice(Rental $rental): float;
}

