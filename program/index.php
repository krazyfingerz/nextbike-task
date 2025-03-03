<?php
// index.php

require_once 'rental.php';
require_once 'customer.php';

use Testbike\Rental;

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Retrieve all rental IDs from the database.
$rentalIds = Rental::getIds();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rental Prices</title>
    <style>
        table {
            border-collapse: collapse;
            width: 90%;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        caption {
            font-size: 1.2em;
            margin: 10px;
        }
    </style>
</head>
<body>
    <table>
        <caption>Rental Prices by Tariff</caption>
        <thead>
            <tr>
                <th>Rental ID</th>
                <th>Customer</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Duration (min)</th>
                <th>Tariff Alpha<br>(EUR)</th>
                <th>Tariff Beta<br>(PLN)</th>
                <th>Tariff Gamma<br>(EUR)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rentalIds as $id): 
                try {
                    $rental = Rental::getById($id);
                    $customer = $rental->getCustomer();
                    
                    // Calculate prices for each tariff.
                    $priceAlpha = $rental->calculatePrice('alpha');
                    $priceBeta  = $rental->calculatePrice('beta');
                    $priceGamma = $rental->calculatePrice('gamma');
                    
                    // Format the start and end times.
                    $startTime = date("Y-m-d H:i:s", $rental->getStartTime());
                    $endTime   = date("Y-m-d H:i:s", $rental->getEndTime());
                    
                    // Convert duration from seconds to minutes.
                    $duration  = round($rental->duration() / 60);
                } catch (Exception $e) {
                    // If an error occurs (e.g., rental not found), skip this rental.
                    continue;
                }
            ?>
            <tr>
                <td><?php echo htmlspecialchars($id); ?></td>
                <td><?php echo htmlspecialchars($customer->getName()); ?></td>
                <td><?php echo htmlspecialchars($startTime); ?></td>
                <td><?php echo htmlspecialchars($endTime); ?></td>
                <td><?php echo htmlspecialchars($duration); ?></td>
                <td><?php echo htmlspecialchars($priceAlpha); ?></td>
                <td><?php echo htmlspecialchars($priceBeta); ?></td>
                <td><?php echo htmlspecialchars($priceGamma); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
