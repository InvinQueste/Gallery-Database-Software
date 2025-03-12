<?php

include 'connect.php';
include 'navbar.php';

if (isset($_POST['signIn'])) {
    $email = $_POST['username'];
    $password = $_POST['password'];
    $loginType = $_POST['loginType'];}

    $sql = "SELECT * FROM Customer WHERE Username='$email' and Password='$password'";
    $result = $conn->query($sql);
    if ($loginType === 'customer') {
        if ($result->num_rows > 0) {
            session_start();
            $row = $result->fetch_assoc();
            $_SESSION['username'] = $row['Username'];
            $_SESSION['id'] = $row['CustomerID'];
            header("Location: homepage.php");
            exit();
        } else {
        }
    }
    elseif ($loginType === 'admin') {
        if ($email === 'admin' && $password === 'adminpass') {
            session_start();
            $_SESSION['username'] = 'admin';
            $_SESSION['id'] = 1;
            header("Location: homepage.php");
            exit();
        } else {  
        }
    }
    else{
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            color:rgb(0, 0, 0);
            text-align: center;
            font-family: Arial, sans-serif;
        }
        h1 {
            color:rgb(131, 20, 20);
            font-family: "Comforter";
            font-size: 60;
        }
    </style>
    <title>Login Error</title>
</head>

<body>
    <div class="c404container">
        <h1>Login Error!</h1>
        <p>Incorrect username or password.</p>
        <br>
        <a class="c404btn" href="index.php">Return to Login</a>
        <br>
        <a class="c404btn" href="forgor.php">Forgot password?</a>
    </div>
</body>

</html>