<?php

$con = mysqli_connect('localhost', 'root', '', 'sales_inventory_system','3307');

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "";

?>