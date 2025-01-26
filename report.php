<?php
// Include database connection
include 'connect.php';

// SQL query to fetch only the required columns
$sql = "SELECT 
            Artwork.ArtworkID, 
            Artwork.Title, 
            Artist.ArtistName, 
            Artwork.ArtworkYear, 
            Artwork.Type, 
            Artwork.Price, 
            Customer.CustomerName, 
            Buys.TransactionTime
        FROM 
            Buys
        INNER JOIN 
            Customer ON Buys.CustomerID = Customer.CustomerID
        INNER JOIN 
            Artwork ON Buys.ArtworkID = Artwork.ArtworkID
        INNER JOIN
            Artist ON Artwork.ArtistID = Artist.ArtistID";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            text-align: center;
            margin-top: 20px;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <h1>Transaction Report</h1>
    <table>
        <thead>
            <tr>
                <th>Artwork ID</th>
                <th>Title</th>
                <th>Artist Name</th>
                <th>Artwork Year</th>
                <th>Type</th>
                <th>Price(in $)</th>
                <th>Customer Name</th>
                <th>Transaction Time</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                // Output each row as a table row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['ArtworkID']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Title']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ArtistName']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ArtworkYear']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Price']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['CustomerName']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['TransactionTime']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8' style='text-align: center;'>No transactions found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
