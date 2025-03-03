<?php

require_once('exchangerate.php');
require_once('customer.php');

use Testbike\Exchangerate;

Exchangerate::setup();

// TODO
$customer = new \customer(1);

