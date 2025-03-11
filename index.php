<?php
session_start();
if (isset($_SESSION['username'])) {
    header("Location: homepage.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles.css">   
</head>

<body>
    <div class="login-form-container">
        <h1 class="index-name">ArtBase</h1>
        <div class="small-container" id="signIn">
            <h1 class="form-title">Sign In</h1>
            <form method="post" action="auth.php">
                <div class="input-group">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="username" id="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                </div>
                <div class="input-group">
                    <select name="loginType" id="loginType" class="dropdown" required>
                        <option value="" disabled selected>Login as</option>
                        <option value="customer">Customer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <input type="submit" class="btn" value="Sign In" name="signIn">
            </form>
        </div>
    </div>
</body>

</html>
