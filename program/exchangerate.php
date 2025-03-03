<?php

namespace Testbike;

require_once('database.php');

use Testbike\Database;


/**
 * class for converting currencies
 */
class Exchangerate
{
    /**
     * @var array
     */
    private static $factors = [];
    
    /**
     * loads the exchange rates from the database into the memory
     */
    public static function setup(): void
    {
        $rows = Database::query(
            'SELECT currency, factor FROM exchangerates'
        );
        self::$factors['EUR'] = 1;
        foreach ($rows as $row) {
            $currency = $row['currency'];
            $factor = $row['factor'];
            self::$factors[$currency] = $factor;
        }
    }
    
    /**
     * returns the factor for a money amount in EUR to be expressed in the target currency; e.g. if "1 EUR = 4.29 PLN",
     *     then "getCurrencyFactor('PLN') = 4.29"
     * @param string $currency
     * @return float
     */
    public static function getCurrencyFactor(string $currency): float
    {
        if (array_key_exists($currency, self::$factors)) {
            return self::$factors[$currency];
        } else {
            throw (new \Exception(sprintf('no entry for currency "%s"', $currency)));
        }
    }
}

