<?php

$db_host = 'localhost';
$db_name = 'booknest';
$db_user = 'root';
$db_pass = '';


$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}