<?php

$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT') ?: 3306;

$con = mysqli_connect($host, $user, $pass, $db, $port);

if (!$con) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// $host = "localhost";
// $user = "root";
// $pass = "";
// $db   = "sistem_bkk";
// $port = 3306;

// $con = mysqli_connect($host, $user, $pass, $db, $port);

// if (!$con) {
//     die("Koneksi gagal: " . mysqli_connect_error());
// }
