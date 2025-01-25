<?php

include 'connect.php';


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
            echo "Successful as customer";
            exit();
        } else {
        }
    }
    elseif ($loginType === 'admin') {
        if ($email === 'admin' && $password === 'adminpass') {
            echo "Login Successful as Admin";
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
    <title>Login Error</title>
</head>

<body>
    <div style="text-align:center; padding:15%;">
        <p style="font-size:50px;color:red;font-weight:bold;">
            Login Error!!<br> <br>

        </p>
        <p style="font-size:30px; font-weight:normal;">
            Incorrect Username or Password<br> <br>

        </p>

        <a href="index.php">
            <p style="font-size:25px; font-weight:normal;">Try Again</p>
        </a>
    </div>
</body>

</html>