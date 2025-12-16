<?php
session_start();
require_once 'koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_query($mysqli, "SELECT * FROM users WHERE username='$username'");
$user = mysqli_fetch_assoc($query);

if ($user) {
    if (password_verify($password, $user['password'])) {
        $_SESSION['login'] = true;
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Password salah";
    }
} else {
    echo "Username tidak ditemukan";
}
