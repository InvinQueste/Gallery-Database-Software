<?php

$username = $_SESSION['username'] ?? ""; 
?>

<div class="navbar">
    <div class="logo">
        <a href="homepage.php">ArtBase</a>
    </div>
    <div class="nav-buttons">
        <?php if ($username !== ""): ?>
            <a href="homepage.php">
                <button>Home</button>
            </a>
        <?php endif; ?>
        <?php if ($username !== "admin" && $username !== ""): ?>
            <a href="collection.php">
                <button>Our Collection</button>
            </a>
            <a href="cart.php">
                <button>Your Cart</button>
            </a>
        <?php endif; ?>
        <?php if ($username === "admin"): ?>
            <a href="collection.php">
                <button>View Report</button>
            </a>
        <?php endif; ?>

        <div class="divider"></div>

        <?php if ($username === ""): ?>
            <a href="index.php">
                <button>Login</button>
            </a>
        <?php else: ?>
            <div class="user-info">
                <span>Signed in as <?php echo htmlspecialchars($username); ?></span>
                <a href="logout.php">
                    <button class="logout-button">Logout</button>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
