<?php
session_start();
include("connect.php");

// Redirect if not logged in
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: homepage.php");
    exit();
}

include('navbar.php');

// Get a random quote from the file
$quotes = file("quotes.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$randomQuote = $quotes ? $quotes[array_rand($quotes)] : "Art speaks where words are unable to explain.";

// Fetch total sales amount
$salesQuery = "SELECT SUM(A.Price) AS TotalSales FROM Buys B JOIN Artwork A ON B.ArtworkID = A.ArtworkID";
$salesResult = $conn->query($salesQuery);
$totalSales = ($salesResult && $row = $salesResult->fetch_assoc()) ? $row['TotalSales'] : 0;

// Fetch total unsold items
$unsoldQuery = "SELECT COUNT(*) AS UnsoldItems FROM Artwork WHERE ArtworkID NOT IN (SELECT DISTINCT ArtworkID FROM Buys)";
$unsoldResult = $conn->query($unsoldQuery);
$totalUnsoldItems = ($unsoldResult && $row = $unsoldResult->fetch_assoc()) ? $row['UnsoldItems'] : 0;

// Fetch top 3 most sold artists
$topArtistsQuery = "
    SELECT Artist.ArtistName, COUNT(Buys.ArtworkID) AS Sales
    FROM Buys
    JOIN Artwork ON Buys.ArtworkID = Artwork.ArtworkID
    JOIN Artist ON Artwork.ArtistID = Artist.ArtistID
    GROUP BY Artist.ArtistID
    ORDER BY Sales DESC
    LIMIT 3";
$topArtistsResult = $conn->query($topArtistsQuery);
$topArtists = [];
while ($row = $topArtistsResult->fetch_assoc()) {
    $topArtists[] = $row['ArtistName'];
}

// Fetch top 3 most sold art groups
$topGroupsQuery = "
    SELECT ArtGroup.GroupName, COUNT(Buys.ArtworkID) AS Sales
    FROM Buys
    JOIN Belongs ON Buys.ArtworkID = Belongs.ArtworkID
    JOIN ArtGroup ON Belongs.GroupID = ArtGroup.GroupID
    GROUP BY ArtGroup.GroupID
    ORDER BY Sales DESC
    LIMIT 3";
$topGroupsResult = $conn->query($topGroupsQuery);
$topGroups = [];
while ($row = $topGroupsResult->fetch_assoc()) {
    $topGroups[] = $row['GroupName'];
}

// Fetch top 3 most valuable customers
$topCustomersQuery = "
    SELECT Customer.CustomerName, SUM(A.Price) AS TotalSpent
    FROM Buys
    JOIN Customer ON Buys.CustomerID = Customer.CustomerID
    JOIN Artwork A ON Buys.ArtworkID = A.ArtworkID
    GROUP BY Customer.CustomerID
    ORDER BY TotalSpent DESC
    LIMIT 3";
$topCustomersResult = $conn->query($topCustomersQuery);
$topCustomers = [];
while ($row = $topCustomersResult->fetch_assoc()) {
    $topCustomers[] = $row['CustomerName'];
}

// Fetch last sold artwork details
$lastSoldQuery = "
    SELECT A.ArtworkID, A.Title, A.Price, Artist.ArtistName 
    FROM Buys 
    JOIN Artwork A ON Buys.ArtworkID = A.ArtworkID
    JOIN Artist ON A.ArtistID = Artist.ArtistID
    ORDER BY Buys.TransactionTime DESC 
    LIMIT 1";
$lastSoldResult = $conn->query($lastSoldQuery);
$lastSoldArtwork = $lastSoldResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Homepage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body style="padding-top: 80px;">

<div class="admin-container">
    <h1 class="admin-title">Welcome, Front Man!</h1>

    <div class="admin-content">
        <!-- Left Panel -->
        <div class="admin-left-panel">
            <div class="admin-stats">
                <h2>Total Sales</h2>
                <p>$<?php echo number_format($totalSales, 2); ?></p>

                <h2>Total Unsold Items</h2>
                <p><?php echo $totalUnsoldItems; ?></p>

                <h2>Top 3 Sold Artists</h2>
                <ul>
                    <?php foreach ($topArtists as $artist) {
                        echo "<li>$artist</li>";
                    } ?>
                </ul>

                <h2>Top 3 Sold Art Groups</h2>
                <ul>
                    <?php foreach ($topGroups as $group) {
                        echo "<li>$group</li>";
                    } ?>
                </ul>

                <h2>Top 3 Customers</h2>
                <ul>
                    <?php foreach ($topCustomers as $customer) {
                        echo "<li>$customer</li>";
                    } ?>
                </ul>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="admin-right-panel">
            <?php if ($lastSoldArtwork): ?>
                <h2>Last Sold Artwork</h2>
                <img src="Images/<?php echo $lastSoldArtwork['ArtworkID']; ?>.jpg" alt="Last Sold Artwork" class="admin-artwork-image">
                <p><strong><?php echo htmlspecialchars($lastSoldArtwork['Title']); ?></strong></p>
                <p><em><?php echo htmlspecialchars($lastSoldArtwork['ArtistName']); ?></em></p>
                <p><?php echo "$" . number_format($lastSoldArtwork['Price'], 2); ?></p>
            <?php else: ?>
                <p class="admin-no-sales">No sales recorded yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sales Report Button -->
    <form action="report.php">
        <button type="submit" class="admin-report-btn">View Sales Report</button>
    </form>

    <!-- Random Quote -->
    <p class="admin-quote"><?php echo htmlspecialchars($randomQuote); ?></p>
</div>

</body>
</html>
