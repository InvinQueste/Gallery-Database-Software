<?php
session_start();
include("connect.php");

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

if ($_SESSION['username']=='admin') {
    header("Location: adminhome.php");
    exit();
}

$username = $_SESSION['username'];
$customerID = $_SESSION['id']; // Assuming CustomerID is stored in session

include('navbar.php');

// Fetch customer details
$query = "SELECT Username, CustomerName, TotalSpending FROM Customer WHERE CustomerID = '$customerID'";
$result = $conn->query($query);

if ($result && $row = $result->fetch_assoc()) {
    $customerUsername = $row['Username'];
    $customerName = $row['CustomerName'];
    $totalSpending = $row['TotalSpending'];
} else {
    echo "Error fetching customer details.";
    exit();
}

// Calculate total cart value
$totalCartValue = 0;
if (!empty($_SESSION['cart'])) {
    $cartIDs = implode(',', array_map('intval', $_SESSION['cart']));
    $cartQuery = "SELECT SUM(Price) AS Total FROM Artwork WHERE ArtworkID IN ($cartIDs)";
    $cartResult = $conn->query($cartQuery);
    
    if ($cartResult && $cartRow = $cartResult->fetch_assoc()) {
        $totalCartValue = $cartRow['Total'];
    }
}

// Fetch most expensive item ever bought
$expensiveQuery = "
    SELECT A.ArtworkID, A.Title, A.Price 
    FROM Buys B
    JOIN Artwork A ON B.ArtworkID = A.ArtworkID
    WHERE B.CustomerID = '$customerID'
    ORDER BY A.Price DESC 
    LIMIT 1";
$expensiveResult = $conn->query($expensiveQuery);
$expensiveItem = $expensiveResult->fetch_assoc();

// Fetch preferred artists (sorted by priority)
$artistsQuery = "SELECT Artist.ArtistName 
                 FROM PrefersArtist 
                 JOIN Artist ON PrefersArtist.ArtistID = Artist.ArtistID 
                 WHERE PrefersArtist.CustomerID = '$customerID' 
                 ORDER BY PrefersArtist.Priority ASC";
$artistsResult = $conn->query($artistsQuery);
$preferredArtists = [];
while ($row = $artistsResult->fetch_assoc()) {
    $preferredArtists[] = $row['ArtistName'];
}

// Fetch preferred art groups (sorted by priority)
$groupsQuery = "SELECT ArtGroup.GroupName 
                FROM PrefersGroup 
                JOIN ArtGroup ON PrefersGroup.GroupID = ArtGroup.GroupID 
                WHERE PrefersGroup.CustomerID = '$customerID' 
                ORDER BY PrefersGroup.Priority ASC";
$groupsResult = $conn->query($groupsQuery);
$preferredGroups = [];
while ($row = $groupsResult->fetch_assoc()) {
    $preferredGroups[] = $row['GroupName'];
}

// Get a random quote from the file
$quotes = file("quotes.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$randomQuote = $quotes[array_rand($quotes)];
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

<div class="home-container">
    <h1 class="home-title">Welcome, <?php echo htmlspecialchars($customerUsername); ?>!</h1>

    <div class="home-content">
        <!-- Left Panel -->
        <div class="home-left-panel">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($customerName); ?></p>
            <p><strong>Total Spending:</strong> $<?php echo number_format($totalSpending, 2); ?></p>
            <p><strong>Current Cart Total:</strong> $<?php echo number_format($totalCartValue, 2); ?></p>
            
            <form action="cart.php">
                <button type="submit" class="home-cart-btn">Go to your cart!</button>
            </form>

            <form action="collection.php">
                <button type="submit" class="home-collection-btn">View our collection!</button>
            </form>

            <h2>Your Preferred Artists</h2>
            <?php if (!empty($preferredArtists)): ?>
                <ul>
                    <?php foreach ($preferredArtists as $artist): ?>
                        <li><?php echo htmlspecialchars($artist); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="home-no-preference">You haven't selected any preferred artists.</p>
            <?php endif; ?>

            <h2>Your Preferred Art Groups</h2>
            <?php if (!empty($preferredGroups)): ?>
                <ul>
                    <?php foreach ($preferredGroups as $group): ?>
                        <li><?php echo htmlspecialchars($group); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="home-no-preference">You haven't selected any preferred groups.</p>
            <?php endif; ?>
        </div>

        <!-- Right Panel -->
        <div class="home-right-panel">
            <?php if ($expensiveItem): ?>
                <h2>Your Most Expensive Purchase</h2>
                <p><strong>Title:</strong> <?php echo htmlspecialchars($expensiveItem['Title']); ?></p>
                <p><strong>Price:</strong> $<?php echo number_format($expensiveItem['Price'], 2); ?></p>
                <img src="Images/<?php echo $expensiveItem['ArtworkID']; ?>.jpg" alt="Most Expensive Item" class="home-expensive-image">
            <?php else: ?>
                <p class="home-no-purchase">You haven't made any purchases yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Centered Logout Button -->
    <form action="logout.php" method="post" class="home-logout-form">
        <button type="submit" class="home-logout-btn">Logout</button>
    </form>

    <p class="home-quote"><?php echo htmlspecialchars($randomQuote); ?></p>
</div>

</body>
</html>
