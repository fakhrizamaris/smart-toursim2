<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'db_mhs';
$connection = mysqli_connect($host, $username, $password, $database);
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

?>