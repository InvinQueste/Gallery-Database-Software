<?php
session_start();
include("connect.php");

if (!isset($_SESSION['id'])) {
    die("User not logged in.");
}
$customerID = intval($_SESSION['id']);

include('navbar.php');

$searchQuery = "";
$searchCondition = "";
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchQuery = $conn->real_escape_string($_GET['search']);
    $searchCondition = "AND (A.Title LIKE '%$searchQuery%' OR AR.ArtistName LIKE '%$searchQuery%')";
}
$cartCondition = "";
if($customerID != 1) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart'])) {
        $artworkID = intval($_POST['artworkID']);
        if (!in_array($artworkID, $_SESSION['cart'])) {
            $_SESSION['cart'][] = $artworkID;
        }
        header("Location: collection.php");
        exit;
    }

    $cartItems = $_SESSION['cart'];
    $cartCondition = empty($cartItems) ? "" : "AND A.ArtworkID NOT IN (" . implode(',', array_map('intval', $cartItems)) . ")";

    $preferredArtistSQL = "SELECT DISTINCT A.*, AR.ArtistName FROM Artwork A
    JOIN Artist AR ON A.ArtistID = AR.ArtistID
    JOIN PrefersArtist PA ON A.ArtistID = PA.ArtistID
    WHERE PA.CustomerID = $customerID AND A.ArtworkID NOT IN (SELECT ArtworkID FROM Buys) $cartCondition $searchCondition
    ORDER BY PA.Priority LIMIT 8";
    $preferredArtistResult = $conn->query($preferredArtistSQL);

    $preferredGroupSQL = "SELECT DISTINCT A.*, AR.ArtistName FROM Artwork A
    JOIN Artist AR ON A.ArtistID = AR.ArtistID
    JOIN Belongs B ON A.ArtworkID = B.ArtworkID
    JOIN PrefersGroup PG ON B.GroupID = PG.GroupID
    WHERE PG.CustomerID = $customerID AND A.ArtworkID NOT IN (SELECT ArtworkID FROM Buys) $cartCondition $searchCondition
    ORDER BY PG.Priority LIMIT 8";
    $preferredGroupResult = $conn->query($preferredGroupSQL);
}

$sql = "SELECT A.*, AR.ArtistName FROM Artwork A
JOIN Artist AR ON A.ArtistID = AR.ArtistID
WHERE A.ArtworkID NOT IN (SELECT ArtworkID FROM Buys) $cartCondition $searchCondition";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtBase Collection</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1 class="home-title">Collection</h1>
        <form method="GET" action="" class="search-bar">
            <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button type="submit">Search</button>
        </form>

        <?php function displayArtworkGrid($result, $heading, $customerID) {
            if ($result && $result->num_rows > 0) {
                echo "<h2 class='section-heading'>$heading</h2><div class='product-list'>";
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='product'>";
                    echo "<img src='Images/" . htmlspecialchars($row['ArtworkID']) . ".jpg' alt='" . htmlspecialchars($row['Title']) . "' class='product-image'>";
                    echo "<h3>" . htmlspecialchars($row['Title']) . "</h3>";
                    echo "<p><i>" . htmlspecialchars($row['ArtistName']) . "</i></p>";
                    echo "<div class='price-year'>";
                    echo "<span>$" . number_format($row['Price'], 2) . "</span>";
                    echo "<span><i>" . htmlspecialchars($row['ArtworkYear']) . "</i></span>";
                    echo "</div>";
                    if ($customerID != 1) {
                    echo "<form method='POST' action=''>";
                    echo "<input type='hidden' name='artworkID' value='" . $row['ArtworkID'] . "'>";
                    echo "<button class='add-to-cart' name='cart'>Add to Cart</button>";
                    echo "</form>";
                    }
                    echo "</div>";
                }
                echo "</div>";
            }
        }
        
        if ($customerID != 1) displayArtworkGrid($preferredArtistResult, "Your Preferred Artists", $customerID);
        if ($customerID != 1) displayArtworkGrid($preferredGroupResult, "Your Preferred Groups", $customerID);
        displayArtworkGrid($result, "All Artworks", $customerID);
        ?>
    </div>
</body>
</html>
