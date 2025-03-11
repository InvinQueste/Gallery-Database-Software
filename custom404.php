<?php
session_start();
include("connect.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            color:rgb(0, 0, 0);
            text-align: center;
            font-family: Arial, sans-serif;
        }
        h1 {
            color:rgb(0, 0, 0);
        }
    </style>
</head>
<body>
    <div class="c404container">
        <h1>This page must be minimalist art...</h1>
        <br>
        <p>There's nothing here.</p>
        <br>
        <a class="c404btn" href="/Gallery-Database-Software/homepage.php">Return to Home</a>
        <br>
        <a class="c404btn" href="javascript:history.back();">Return to Previous Page</a>
    </div>
</body>
</html>
