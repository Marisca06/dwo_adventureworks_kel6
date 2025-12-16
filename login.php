<?php
require 'koneksi.php';

session_start();

$usernameErr = $passwordErr = "";
$username = $password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST['username'])) {
        $usernameErr = "Username belum diisi";
    } else {
        $username = trim($_POST['username']);
    }

    if (empty($_POST['password'])) {
        $passwordErr = "Password belum diisi";
    } else {
        $password = trim($_POST['password']);
    }

    if ($username && $password) {

        $sql = mysqli_query($mysqli, "
            SELECT * FROM users 
            WHERE username = '$username' 
            AND password = '$password'
        ");

        if (mysqli_num_rows($sql) == 1) {
            $data = mysqli_fetch_assoc($sql);

            $_SESSION['login'] = true;
            $_SESSION['username'] = $data['username'];
            $_SESSION['nama'] = $data['nama'];

            header("Location: dashboard.php");
            exit;
        } else {
            $passwordErr = "Username atau Password salah";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - AdventureWorks</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
body{
    background: linear-gradient(135deg,#4e73df,#224abe);
    height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    font-family:'Segoe UI',sans-serif;
}
.login-card{
    background:#fff;
    width:420px;
    padding:40px;
    border-radius:16px;
    box-shadow:0 15px 40px rgba(0,0,0,.2);
}
.login-title{
    font-size:26px;
    font-weight:700;
    color:#4e73df;
    text-align:center;
}
.login-sub{
    text-align:center;
    color:#6c757d;
    margin-bottom:25px;
}
.form-control{
    height:48px;
    border-radius:10px;
}
.btn-login{
    background:#4e73df;
    color:white;
    border-radius:10px;
    font-weight:600;
}
.btn-login:hover{
    background:#224abe;
}
.error{
    font-size:12px;
    color:#dc3545;
}
</style>
</head>

<body>

<div class="login-card">
    <div class="login-title">Selamat Datang</div>
    <div class="login-sub">Masuk untuk melanjutkan ke Dashboard AdventureWorks</div>

    <form method="post">
        <div class="mb-3">
            <input type="text" name="username" class="form-control" placeholder="Username" value="<?= $username ?>">
            <div class="error"><?= $usernameErr ?></div>
        </div>
        <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password">
            <div class="error"><?= $passwordErr ?></div>
        </div>

        <button class="btn btn-login w-100 py-2">
            LOGIN
        </button>
    </form>
</div>

</body>
</html>
