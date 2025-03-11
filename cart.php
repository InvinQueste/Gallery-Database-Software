<?php
session_start();
include("connect.php");

// Check if the cart exists and is not empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "Your cart is empty.";
    exit;
}

$cartItems = [];
$ids = implode(',', array_map('intval', $_SESSION['cart']));
$sql = "SELECT * FROM Artwork WHERE ArtworkID IN ($ids)";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
    }
}
if (empty($cartItems)) {
    echo "Your cart is empty.";
    exit;
}

// Redirect to the cart page or display a success message
//header('Location: cart.php');
//exit;

//Handle Add to Cart (Buy) action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy'])) {
    $username = $_SESSION['username']; // Assuming username is stored in session
    $artworkID = intval($_POST['artworkID']); // Artwork ID to be bought
    $customerID = $_SESSION['id'];
    // Insert into Buys table
    $insertBuyQuery = "INSERT INTO Buys (CustomerID, ArtworkID) VALUES ('$customerID', '$artworkID')";
    if ($conn->query($insertBuyQuery)) {
        $_SESSION['cart'] = array_diff($_SESSION['cart'], [$artworkID]);
        header("Location: cart.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove'])) {
    $artworkID = intval($_POST['artworkID']); // Sanitize input

    if (isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array_diff($_SESSION['cart'], [$artworkID]); // Remove item
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
    }

    header("Location: cart.php"); // Refresh cart page
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy_all'])) {
    $customerID = $_SESSION['id'];
    
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $artworkID) {
            $insertBuyQuery = "INSERT INTO Buys (CustomerID, ArtworkID) VALUES ('$customerID', '$artworkID')";
            $conn->query($insertBuyQuery); // Execute query
        }
        $_SESSION['cart'] = []; // Clear the cart after buying
    }
    
    header("Location: cart.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['clear_all'])) {
    $_SESSION['cart'] = []; // Clear the cart
    header("Location: cart.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Your Cart</h1>
    <h4><a href="logout.php">logout</a></h4>
    <div>
    <form method="POST" action="cart.php">
        <button type="submit" name="buy_all">Buy All</button>
        <button type="submit" name="clear_all">Clear All</button>
    </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cartItems as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['Title']); ?></td>
                    <td>$<?php echo number_format($item['Price'], 2); ?></td>
                    <td>
                        <form method="POST" action="cart.php">
                            <input type="hidden" name="artworkID" value="<?php echo $item['ArtworkID']; ?>">
                            <button type="submit" name="buy">Buy</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="cart.php">
                            <input type="hidden" name="artworkID" value="<?php echo $item['ArtworkID']; ?>">
                            <button type="submit" name="remove">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

