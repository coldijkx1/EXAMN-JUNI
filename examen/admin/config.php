<?php
$DATABASE_HOST = 'localhost';
$DATABASE_USER = '84222';
$DATABASE_PASS = 'Hamster2003';
$DATABASE_NAME = 'Examen';
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
?>