<?php
session_start();
include("connect.php");

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

if ($_SESSION['username']=='admin') {
    header("Location: adminhome.php");
    exit();
}

include('navbar.php');

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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy'])) {
    $username = $_SESSION['username'];
    $artworkID = intval($_POST['artworkID']);
    $customerID = $_SESSION['id'];

    $priceQuery = "SELECT Price, ArtistID FROM Artwork WHERE ArtworkID = $artworkID";
    $priceResult = $conn->query($priceQuery);
    
    if ($priceResult && $row = $priceResult->fetch_assoc()) {
        $artworkPrice = $row['Price'];
        $artistID = $row['ArtistID'];

        $insertBuyQuery = "INSERT INTO Buys (CustomerID, ArtworkID) VALUES ('$customerID', '$artworkID')";
        if ($conn->query($insertBuyQuery)) {
            $updateSpendingQuery = "UPDATE Customer SET TotalSpending = TotalSpending + $artworkPrice WHERE CustomerID = '$customerID'";
            $conn->query($updateSpendingQuery);

            $artistQuery = "SELECT A.ArtistID, COUNT(*) AS freq
                FROM Buys B
                JOIN Artwork A ON B.ArtworkID = A.ArtworkID
                WHERE B.CustomerID = '$customerID'
                GROUP BY A.ArtistID
                ORDER BY freq DESC
                LIMIT 3";
            $artistResult = $conn->query($artistQuery);
            if ($artistResult) {
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
    $artworkID = intval($_POST['artworkID']);

    if (isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array_diff($_SESSION['cart'], [$artworkID]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }

    header("Location: cart.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy_all'])) {
    $customerID = $_SESSION['id'];
    $totalPrice = 0;

    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $artworkID) {
            $priceQuery = "SELECT Price FROM Artwork WHERE ArtworkID = $artworkID";
            $priceResult = $conn->query($priceQuery);
            if ($priceResult && $row = $priceResult->fetch_assoc()) {
                $price = $row['Price'];
                $totalPrice += $price;

                $insertBuyQuery = "INSERT INTO Buys (CustomerID, ArtworkID) VALUES ('$customerID', '$artworkID')";
                $conn->query($insertBuyQuery);
            }
        }

        if ($totalPrice > 0) {
            $updateSpendingQuery = "UPDATE Customer SET TotalSpending = COALESCE(TotalSpending, 0) + $totalPrice WHERE CustomerID = '$customerID'";
            $conn->query($updateSpendingQuery);
        }

        $artistQuery = "SELECT A.ArtistID, COUNT(*) AS freq
                        FROM Buys B
                        JOIN Artwork A ON B.ArtworkID = A.ArtworkID
                        WHERE B.CustomerID = '$customerID'
                        GROUP BY A.ArtistID
                        ORDER BY freq DESC
                        LIMIT 3";
        $artistResult = $conn->query($artistQuery);
        if ($artistResult) {
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

        $_SESSION['cart'] = [];
    }
    
    header("Location: cart.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['clear_all'])) {
    $_SESSION['cart'] = [];
    header("Location: cart.php");
    exit;
}

$totalCartValue = 0;
if (!empty($_SESSION['cart'])) {
    $cartIDs = implode(',', array_map('intval', $_SESSION['cart']));
    $cartQuery = "SELECT SUM(Price) AS Total FROM Artwork WHERE ArtworkID IN ($cartIDs)";
    $cartResult = $conn->query($cartQuery);
    
    if ($cartResult && $cartRow = $cartResult->fetch_assoc()) {
        $totalCartValue = $cartRow['Total'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="cart-container">
    <h1 class="home-title">Your Cart</h1>
        <?php if ($cartEmpty): ?>
            <div class="cart-empty-message">Your cart is empty.</div>
        <?php else: ?>
            <form method="POST" action="cart.php" style="display: flex; justify-content: center;">
                <button type="submit" name="buy_all" class="cart-button cart-buy">Buy All</button>
                <button type="submit" name="clear_all" class="cart-button cart-remove">Clear All</button>
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
                    <tr>
                        <td><strong>Total</strong></td>
                        <td><strong>$<?php echo number_format($totalCartValue, 2); ?></strong></td>
                        <td>
                            <form method="POST" action="cart.php" style="display: inline;">
                                    <input type="hidden" name="artworkID" value="<?php echo $item['ArtworkID']; ?>">
                                    <button type="submit" name="buy_all" class="cart-button cart-buy">Buy All</button>
                            </form>
                            <form method="POST" action="cart.php" style="display: inline;">
                                <input type="hidden" name="artworkID" value="<?php echo $item['ArtworkID']; ?>">
                                <button type="submit" name="clear_all" class="cart-button cart-remove">Clear All</button>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>

