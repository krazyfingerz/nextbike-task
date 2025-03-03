<?php

namespace Testbike;

require_once 'database.php';

class Customer
{
    private $id;
    private $name;
    private $domain;
    private $language;
    private $currency;

    /**
     * Constructor.
     * If an ID is provided, fetches the record from the database.
     * Otherwise, initializes from the given data row.
     *
     * @param int|null $id   The customer ID.
     * @param array    $data An associative array with keys id, name, domain, language, currency.
     */
    public function __construct($id = null, array $data = [])
    {
        if ($id) {
            $sql = 'SELECT id, name, domain, language, currency FROM customers WHERE id=' . (int)$id;
            $rows = Database::query($sql);
            if (!empty($rows)) {
                $row = $rows[0];
                $this->id       = $row['id'];
                $this->name     = $row['name'];
                $this->domain   = $row['domain'];
                $this->language = $row['language'];
                $this->currency = $row['currency'];
            }
        } else {
            // Initialize from provided data
            $this->id       = $data['id']       ?? null;
            $this->name     = $data['name']     ?? '';
            $this->domain   = $data['domain']   ?? '';
            $this->language = $data['language'] ?? '';
            $this->currency = $data['currency'] ?? '';
        }
    }

    /**
     * Returns an array of all Customer objects from the database.
     *
     * @return array An array of Customer instances.
     */
    public static function getAll(): array
    {
        $rows = Database::query('SELECT id, name, domain, language, currency FROM customers;');
        $customers = [];
        foreach ($rows as $row) {
            $customers[] = new self(null, $row);
        }
        return $customers;
    }

    // Getter methods
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getDomain(): string
    {
        return $this->domain;
    }
    
    public function getLanguage(): string
    {
        return $this->language;
    }
    
    public function getCurrency(): string
    {
        return $this->currency;
    }
}
?>

<?php
// Outside the namespace, we use the Customer class to display the table.
use Testbike\Customer;

// Retrieve all customers.
$customers = Customer::getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Table</title>
    <style>
        table {
            border-collapse: collapse;
            width: 60%;
            margin: 20px auto;
            text-align: center;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Domain</th>
                <th>ID</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $customer): ?>
                <tr>
                    <td><?php echo htmlspecialchars($customer->getName()); ?></td>
                    <td><?php echo htmlspecialchars($customer->getDomain()); ?></td>
                    <td><?php echo htmlspecialchars($customer->getId()); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>