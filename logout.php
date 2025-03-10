<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php"); // Redirect if not logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <style>
        body {
            text-align: center;
            padding: 15%;
        }
        p {
            font-size: 30px;
            font-weight: bold;
        }
        form {
            margin-top: 20px;
        }
        button {
            font-size: 20px;
            padding: 10px 20px;
            background-color: red;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>

<form action="logoutprocess.php" method="post">
    <button type="submit">Logout</button>
</form>

</body>
</html>
