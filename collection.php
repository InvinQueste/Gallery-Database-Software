<?php
session_start();
include("connect.php");

// Search logic
$searchQuery = "";
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['search'])) {
    $searchQuery = $conn->real_escape_string($_GET['search']);
}

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart'])) {
    $artworkID = intval($_POST['artworkID']); // Sanitize input
    if (!in_array($artworkID, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $artworkID; // Add product ID to the cart
    }
    header("Location: collection.php"); // Redirect to clear form data
    exit;
}

// Fetch products
$sql = "SELECT * FROM Artwork";
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

$conditions = [];

// Filter by search query
if (!empty($searchQuery)) {
    $conditions[] = "Title LIKE '%$searchQuery%'";
}

// Exclude items in cart
if (!empty($cartItems)) {
    $cartIds = implode(',', array_map('intval', $cartItems)); // Sanitize and format IDs
    $conditions[] = "ArtworkID NOT IN ($cartIds)";
}

// Exclude items that have been purchased
$conditions[] = "ArtworkID NOT IN (SELECT ArtworkID FROM Buys)";

// Combine all conditions into SQL query
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtBase Collection</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bokor&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            background: #f5f5f5;
        }
    </style>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Collection</h1>
        <form method="GET" action="" class="search-bar">
            <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button type="submit">Search</button>
        </form>
        <div class="product-list">
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='product'>";
                    echo "<h3>" . htmlspecialchars($row['Title']) . "</h3>";
                    echo "<div class='price-year'>";
                    echo "<span>$" . number_format($row['Price'], 2) . "</span>";
                    echo "<span><i>" . htmlspecialchars($row['ArtworkYear']) . "</i></span>";
                    echo "</div>";
                    echo "<form method='POST' action=''>";
                    echo "<input type='hidden' name='artworkID' value='" . $row['ArtworkID'] . "'>";
                    echo "<button class='add-to-cart' name='cart'>Add to Cart</button>";
                    echo "</form>";
                    echo "</div>";
                }
            } else {
                echo "<p>No products found.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
