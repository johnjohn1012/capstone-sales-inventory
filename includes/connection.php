<<<<<<< HEAD
<?php 
	$con = mysqli_connect('localhost','root','','sales_inventory_system');
	if (!$con) {
		echo "Database Not Connected";
	}
	else {
		echo"";
	}
 ?>
=======
<?php

$con = mysqli_connect('localhost', 'root', '', 'sales_inventory_system','3307');

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "";

?>
>>>>>>> c51e4fd03bd979e2ba3b0907f4dcc76478822920
