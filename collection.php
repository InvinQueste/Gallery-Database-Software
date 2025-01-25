<?php
session_start();
include("connect.php");

// Search logic
$searchQuery = "";
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['search'])) {
    $searchQuery = $conn->real_escape_string($_GET['search']);
}

// Handle Add to Cart (Buy) action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy'])) {
    $username = $_SESSION['username']; // Assuming username is stored in session
    $artworkID = $_POST['artworkID']; // Artwork ID to be bought
    $customerID = $_SESSION['id'];
    // Insert into Buys table
    $insertBuyQuery = "INSERT INTO Buys (CustomerID, ArtworkID) VALUES ('$customerID', '$artworkID')";
    if ($conn->query($insertBuyQuery)) {
        header("Location: collection.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch products
$sql = "SELECT * FROM Artwork";
if (!empty($searchQuery)) {
    $sql .= " WHERE Title LIKE '%$searchQuery%'";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtBase Collection</title>
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
        .container {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            width: 80%;
            max-width: 1200px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 40px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .search-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .search-bar input[type="text"] {
            padding: 10px;
            font-size: 16px;
            border: 2px solid rgb(0, 0, 0);
            border-radius: 5px;
            width: 60%;
        }
        .search-bar button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            background: rgba(25, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            cursor: pointer;
            color: #333;
        }
        .search-bar button:hover {
            background: rgba(255, 255, 255, 0.4);
        }
        .product-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
            justify-content: center;
            gap: 30px;
        }
        .product {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }
        .product h3 {
            margin: 10px 0;
            color: #333;
            flex-grow: 1;
        }
        .product .price-year {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            color: #555;
            margin-bottom: 10px;
        }
        .product .add-to-cart {
            position: relative;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
            align-self: flex-end;
        }
        .product .add-to-cart:hover {
            background-color: #45a049;
        }
    </style>
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
                    echo "<button class='add-to-cart' name='buy'>Add to Cart</button>";
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
