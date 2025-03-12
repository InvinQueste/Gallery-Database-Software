<?php
session_start();
include("connect.php");

// Check if the cart exists and is not empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $cartEmpty = true;
} else {
    $cartEmpty = false;
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
        $cartEmpty = true;
    }
}

// Redirect to the cart page or display a success message
//header('Location: cart.php');
//exit;

//Handle Add to Cart (Buy) action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy'])) {
    $username = $_SESSION['username']; // Assuming username is stored in session
    $artworkID = intval($_POST['artworkID']); // Artwork ID to be bought
    $customerID = $_SESSION['id'];

    // Fetch the price of the artwork
    $priceQuery = "SELECT Price, ArtistID FROM Artwork WHERE ArtworkID = $artworkID";
    $priceResult = $conn->query($priceQuery);
    
    if ($priceResult && $row = $priceResult->fetch_assoc()) {
        $artworkPrice = $row['Price'];
        $artistID = $row['ArtistID'];

        // Insert into Buys table
        $insertBuyQuery = "INSERT INTO Buys (CustomerID, ArtworkID) VALUES ('$customerID', '$artworkID')";
        if ($conn->query($insertBuyQuery)) {
            // Update TotalSpending for the customer
            $updateSpendingQuery = "UPDATE Customer SET TotalSpending = TotalSpending + $artworkPrice WHERE CustomerID = '$customerID'";
            $conn->query($updateSpendingQuery);

            /* -------------------------------
            Update Preferred Artists Table
            -------------------------------
            1. Count the frequency of items bought for each artist
            2. Order by frequency (highest first)
            3. Take the top 3 and update the PrefersArtist table
            */
            $artistQuery = "SELECT A.ArtistID, COUNT(*) AS freq
                FROM Buys B
                JOIN Artwork A ON B.ArtworkID = A.ArtworkID
                WHERE B.CustomerID = '$customerID'
                GROUP BY A.ArtistID
                ORDER BY freq DESC
                LIMIT 3";
            $artistResult = $conn->query($artistQuery);
            if ($artistResult) {
                // Clear existing preferred artists for this customer
                $conn->query("DELETE FROM PrefersArtist WHERE CustomerID = '$customerID'");
                $priority = 1;
                while ($row = $artistResult->fetch_assoc()) {
                    $artistID = $row['ArtistID'];
                    $insertPrefArtist = "INSERT INTO PrefersArtist (CustomerID, ArtistID, Priority)
                                        VALUES ('$customerID', '$artistID', '$priority')";
                    $conn->query($insertPrefArtist);
                    $priority++;
                }
            }

            /* -------------------------------
            Update Preferred Groups Table
            -------------------------------
            1. Use a join on Artwork and Belongs (via Buys) to count frequency per group
            2. Order by frequency descending and take the top 3 groups
            3. Update the PrefersGroup table accordingly
            */
            $groupQuery = "SELECT B.GroupID, COUNT(*) AS freq
                FROM Buys Bu
                JOIN Artwork A ON Bu.ArtworkID = A.ArtworkID
                JOIN Belongs B ON A.ArtworkID = B.ArtworkID
                WHERE Bu.CustomerID = '$customerID'
                GROUP BY B.GroupID
                ORDER BY freq DESC
                LIMIT 3";
            $groupResult = $conn->query($groupQuery);
            if ($groupResult) {
                // Clear existing preferred groups for this customer
                $conn->query("DELETE FROM PrefersGroup WHERE CustomerID = '$customerID'");
                $priority = 1;
                while ($row = $groupResult->fetch_assoc()) {
                    $groupID = $row['GroupID'];
                    $insertPrefGroup = "INSERT INTO PrefersGroup (CustomerID, GroupID, Priority)
                                        VALUES ('$customerID', '$groupID', '$priority')";
                    $conn->query($insertPrefGroup);
                    $priority++;
                }
            }


            // Remove the purchased artwork from the cart
            $_SESSION['cart'] = array_diff($_SESSION['cart'], [$artworkID]);

            header("Location: cart.php");
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Error retrieving artwork details.";
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
    $totalPrice = 0;

    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $artworkID) {
            // Fetch artwork price
            $priceQuery = "SELECT Price FROM Artwork WHERE ArtworkID = $artworkID";
            $priceResult = $conn->query($priceQuery);
            if ($priceResult && $row = $priceResult->fetch_assoc()) {
                $price = $row['Price'];
                $totalPrice += $price;

                // Insert each artwork into Buys table
                $insertBuyQuery = "INSERT INTO Buys (CustomerID, ArtworkID) VALUES ('$customerID', '$artworkID')";
                $conn->query($insertBuyQuery);
            }
        }

        // Update TotalSpending for the customer (using COALESCE to avoid NULL issues)
        if ($totalPrice > 0) {
            $updateSpendingQuery = "UPDATE Customer SET TotalSpending = COALESCE(TotalSpending, 0) + $totalPrice WHERE CustomerID = '$customerID'";
            $conn->query($updateSpendingQuery);
        }

        /* -------------------------------
           Update Preferred Artists Table
           -------------------------------
           1. Count the frequency of items bought for each artist
           2. Order by frequency (highest first)
           3. Take the top 3 and update the PrefersArtist table
        */
        $artistQuery = "SELECT A.ArtistID, COUNT(*) AS freq
                        FROM Buys B
                        JOIN Artwork A ON B.ArtworkID = A.ArtworkID
                        WHERE B.CustomerID = '$customerID'
                        GROUP BY A.ArtistID
                        ORDER BY freq DESC
                        LIMIT 3";
        $artistResult = $conn->query($artistQuery);
        if ($artistResult) {
            // Clear existing preferred artists for this customer
            $conn->query("DELETE FROM PrefersArtist WHERE CustomerID = '$customerID'");
            $priority = 1;
            while ($row = $artistResult->fetch_assoc()) {
                $artistID = $row['ArtistID'];
                $insertPrefArtist = "INSERT INTO PrefersArtist (CustomerID, ArtistID, Priority)
                                       VALUES ('$customerID', '$artistID', '$priority')";
                $conn->query($insertPrefArtist);
                $priority++;
            }
        }

        /* -------------------------------
           Update Preferred Groups Table
           -------------------------------
           1. Use a join on Artwork and Belongs (via Buys) to count frequency per group
           2. Order by frequency descending and take the top 3 groups
           3. Update the PrefersGroup table accordingly
        */
        $groupQuery = "SELECT B.GroupID, COUNT(*) AS freq
                       FROM Buys Bu
                       JOIN Artwork A ON Bu.ArtworkID = A.ArtworkID
                       JOIN Belongs B ON A.ArtworkID = B.ArtworkID
                       WHERE Bu.CustomerID = '$customerID'
                       GROUP BY B.GroupID
                       ORDER BY freq DESC
                       LIMIT 3";
        $groupResult = $conn->query($groupQuery);
        if ($groupResult) {
            // Clear existing preferred groups for this customer
            $conn->query("DELETE FROM PrefersGroup WHERE CustomerID = '$customerID'");
            $priority = 1;
            while ($row = $groupResult->fetch_assoc()) {
                $groupID = $row['GroupID'];
                $insertPrefGroup = "INSERT INTO PrefersGroup (CustomerID, GroupID, Priority)
                                      VALUES ('$customerID', '$groupID', '$priority')";
                $conn->query($insertPrefGroup);
                $priority++;
            }
        }

        // Finally, clear the cart
        $_SESSION['cart'] = [];
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
    <link rel="stylesheet" href="styles.css">
    <!--
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
    -->
</head>
<body class="cart-body">
    <h1 class="cart-heading">Your Cart</h1>
    <h4 class="cart-logout"><a href="logout.php">logout</a></h4>
    <div class="cart-container">
        <?php if ($cartEmpty): ?>
            <div class="cart-empty-message">Your cart is empty.</div>
        <?php else: ?>
            <form method="POST" action="cart.php">
                <button type="submit" name="buy_all">Buy All</button>
                <button type="submit" name="clear_all">Clear All</button>
            </form>

            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['Title']); ?></td>
                            <td>$<?php echo number_format($item['Price'], 2); ?></td>
                            <td>
                                <form method="POST" action="cart.php" style="display: inline;">
                                    <input type="hidden" name="artworkID" value="<?php echo $item['ArtworkID']; ?>">
                                    <button type="submit" name="buy" class="cart-button cart-buy">Buy</button>
                                </form>
                                <form method="POST" action="cart.php" style="display: inline;">
                                    <input type="hidden" name="artworkID" value="<?php echo $item['ArtworkID']; ?>">
                                    <button type="submit" name="remove" class="cart-button cart-remove">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>

