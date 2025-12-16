<?php
// koneksi_dw.php
$servername = "localhost";
$username = "root";
$password = "";
$database = "dw_adventureworks";

$mysqli_dw = new mysqli($servername, $username, $password, $database);

if ($mysqli_dw->connect_error) {
    die("Connection failed: " . $mysqli_dw->connect_error);
}
?>
