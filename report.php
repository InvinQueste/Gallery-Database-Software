<?php
// Include database connection
session_start();
include 'connect.php';

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: homepage.php");
    exit;
}

include('navbar.php');

// Initialize variables
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : '';
$search_customer = isset($_GET['search_customer']) ? $_GET['search_customer'] : '';
$search_artist = isset($_GET['search_artist']) ? $_GET['search_artist'] : '';

// Base SQL query
$sql = "SELECT Artwork.ArtworkID, Artwork.Title, Artist.ArtistName, Artwork.ArtworkYear, Artwork.Type, 
               Artwork.Price, Customer.CustomerName, Buys.TransactionTime
        FROM Buys
        INNER JOIN Customer ON Buys.CustomerID = Customer.CustomerID
        INNER JOIN Artwork ON Buys.ArtworkID = Artwork.ArtworkID
        INNER JOIN Artist ON Artwork.ArtistID = Artist.ArtistID
        WHERE 1=1";

// Apply date filter
if (!empty($from_date) && !empty($to_date)) {
    if ($from_date > $to_date) {
        echo "<script>alert('Error: From date cannot be later than To date!');</script>";
    } else {
        $sql .= " AND DATE(Buys.TransactionTime) >= '$from_date' AND DATE(Buys.TransactionTime) <= '$to_date'";
    }
}

// Apply search filters
if (!empty($search_customer)) {
    $sql .= " AND Customer.CustomerName LIKE '%$search_customer%'";
}
if (!empty($search_artist)) {
    $sql .= " AND Artist.ArtistName LIKE '%$search_artist%'";
}

// Apply sorting options
switch ($sort_by) {
    case 'price_asc':
        $sql .= " ORDER BY Artwork.Price ASC, Customer.CustomerName ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY Artwork.Price DESC, Customer.CustomerName ASC";
        break;
    case 'year_asc':
        $sql .= " ORDER BY Artwork.ArtworkYear ASC, Customer.CustomerName ASC";
        break;
    case 'year_desc':
        $sql .= " ORDER BY Artwork.ArtworkYear DESC, Customer.CustomerName ASC";
        break;
    case 'type_asc':
    case '':
        $sql .= " ORDER BY Artwork.Type ASC, Customer.CustomerName ASC";
        break;
    case 'artist_asc':
        $sql .= " ORDER BY Artist.ArtistName ASC, Customer.CustomerName ASC";
        break;
    case 'artworkid_asc':
        $sql .= " ORDER BY Artwork.ArtworkID ASC, Customer.CustomerName ASC";
        break;
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Report</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="report-container">
    <h1 class="home-title">Transaction Report</h1>
    <form method="GET">
    <div class="form-container">
        <div class="form-group">
        <label>From Date:</label>
        <input type="date" name="from_date" value="<?php echo $from_date; ?>">
        </div>
        <div class="form-group">
        <label>To Date:</label>
        <input type="date" name="to_date" value="<?php echo $to_date; ?>">
        </div>
        <div class="form-group">
        <label>Search Customer:</label>
        <input type="text" name="search_customer" value="<?php echo $search_customer; ?>" placeholder="Enter customer name">
        </div>
        <div class="form-group">
        <label>Search Artist:</label>
        <input type="text" name="search_artist" value="<?php echo $search_artist; ?>" placeholder="Enter artist name">
        </div>
        <div class="form-group">
        <label>Sort By:</label>
        <select name="sort_by">
            <option value="" <?php if ($sort_by == '') echo 'selected'; ?>>None</option>
            <option value="artworkid_asc" <?php if ($sort_by == 'artworkid_asc') echo 'selected'; ?>>Artwork ID</option>
            <option value="price_asc" <?php if ($sort_by == 'price_asc') echo 'selected'; ?>>Price: Low to High</option>
            <option value="price_desc" <?php if ($sort_by == 'price_desc') echo 'selected'; ?>>Price: High to Low</option>
            <option value="year_asc" <?php if ($sort_by == 'year_asc') echo 'selected'; ?>>Artwork Year: Oldest First</option>
            <option value="year_desc" <?php if ($sort_by == 'year_desc') echo 'selected'; ?>>Artwork Year: Newest First</option>
            <option value="type_asc" <?php if ($sort_by == 'type_asc') echo 'selected'; ?>>Artwork Type: A-Z</option>
            <option value="artist_asc" <?php if ($sort_by == 'artist_asc') echo 'selected'; ?>>Artist Name: A-Z</option>
        </select>
        </div>
        <div class="submit-group">
        <button type="submit" class="report-btn">Filter</button>
        </div>
    </div>
    </form>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Artwork ID</th>
                <th>Title</th>
                <th>Artist Name</th>
                <th>Artwork Year</th>
                <th>Type</th>
                <th>Price</th>
                <th>Customer Name</th>
                <th>Transaction Time</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['ArtworkID']); ?></td>
                        <td><?php echo htmlspecialchars($row['Title']); ?></td>
                        <td><?php echo htmlspecialchars($row['ArtistName']); ?></td>
                        <td><?php echo htmlspecialchars($row['ArtworkYear']); ?></td>
                        <td><?php echo htmlspecialchars($row['Type']); ?></td>
                        <td><?php echo htmlspecialchars($row['Price']); ?></td>
                        <td><?php echo htmlspecialchars($row['CustomerName']); ?></td>
                        <td><?php echo htmlspecialchars($row['TransactionTime']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan='8' style='text-align: center;'>No transactions found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</body>
</html>
