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
    <title>Homepage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>

<form action="logoutprocess.php" method="post">
    <button type="submit">Logout</button>
</form>

</body>
</html>
