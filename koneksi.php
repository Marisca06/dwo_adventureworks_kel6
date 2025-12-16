<?php
$servername = "localhost";
$username = "root";
$password = ""; // sesuaikan password mysql kamu
$database = "dw_adventureworks";

// koneksi
$mysqli = new mysqli($servername, $username, $password, $database);

// cek koneksi
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>
