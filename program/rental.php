<?php

namespace Testbike;

require_once('database.php');
require_once('customer.php');
require_once('tariff.php');
require_once('tariff-alpha.php');
require_once('tariff-beta.php');
require_once('tariff-gamma.php');

use Testbike\Database;
use Testbike\Customer;
use Testbike\Tariff;
use Testbike\TariffAlpha;
use Testbike\TariffBeta;
use Testbike\TariffGamma;


/**
 * class for rentals in the system
 */
class Rental
{
    /**
     * @var \customer $customer the customer who did the rental
     */
    private $customer;
    
    /**
     * @var int $startTime UNIX timestamp, when the rental started
     */
    private $startTime;
    
    /**
     * @var int $endTime UNIX timestamp, when the rental ended
     */
    private $endTime;
    
    /**
     * [constructor]
     * @param \customer customer
     * @param int $startTime
     * @param int $endTime
     */
    public function __construct(Customer $customer, int $startTime, int $endTime)
    {
        $this->customer = $customer;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }
    
    /**
     * [accessor] returns a rental by its ID
     * 
     * @param int $rentalId
     * @return Rental
     * @throws \Exception in case the rental was not found
     */
    public static function getById(int $rentalId): Rental
    {
        $rows = Database::query(
            'SELECT customer_id, start_time, end_time FROM rentals WHERE (id = :id);',
            [
                'id' => $rentalId,
            ]
        );
        if (count($rows) === 1) {
            $dataRaw = $rows[0];
            $customerId = $dataRaw['customer_id'];
            $customer = new \Testbike\Customer($customerId);
            return (
                new Rental(
                    $customer,
                    $dataRaw['start_time'],
                    $dataRaw['end_time']
                )
            );
        } else {
            throw (new \Exception(sprintf('no rental found for ID %d', $rentalId)));
        }
    }
    
    /**
     * [accessor] returns the IDs of all rentals in the system
     * 
     * @return array
     */
    public static function getIds(): array
    {
        return array_map(
            function ($dataset) {return $dataset['id'];},
            Database::query('SELECT id FROM rentals;')
        );
    }
    
    /**
     * [accessor] [getter] the customer of the rental
     * 
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }
    
    /**
     * [accessor] [getter] the start time of the rental
     * 
     * @return int
     */
    public function getStartTime(): int
    {
        return $this->startTime;
    }
    
    /**
     * [accessor] [getter] the end time of the rental
     * 
     * @return int
     */
    public function getEndTime(): int
    {
        return $this->endTime;
    }
    
    /**
     * [accessor] duration of the rental in seconds
     * 
     * @return int
     */
    public function duration(): int
    {
        return ($this->endTime - $this->startTime);
    }
    
    /**
     * [accessor] shall return the price of the rental according to a given tariff
     * 
     * @param string $tariffName
     * @return float
     */
    public function calculatePrice(string $tariffName): float
    {
        switch (strtolower($tariffName)) {
            case 'alpha':
                $tariff = new TariffAlpha();
                break;
            case 'beta':
                $tariff = new TariffBeta();
                break;
            case 'gamma':
                $tariff = new TariffGamma();
                break;
            default:
                throw new \Exception("Unknown tariff: $tariffName");
        }
        return $tariff->calculatePrice($this);
    }
}

