<?php
session_start();
require_once "db.php";

$stmt = $pdo->query("SELECT * FROM shoes");
$shoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sneaker Store</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 30px;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }

        .shoe-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }

        .shoe-card img {
            width: 100%;
            height: 180px;
            object-fit: contain;
            background: #eee;
            border-radius: 8px;
        }

        select, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }

        button {
            background: black;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: #333;
        }

        .out {
            color: red;
        }

        .cart-link {
            display: inline-block;
            margin-bottom: 20px;
            background: black;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 6px;
        }
    </style>
</head>
<body>

<h1>Showtime Sneakers</h1>

<a class="cart-link" href="cart.php">View Cart</a>
<a class="cart-link" href="admin_login.php">Admin Login</a>

<div class="container">
    <?php foreach ($shoes as $shoe): ?>
        <?php
        $sizeStmt = $pdo->prepare("SELECT size, stock FROM shoe_sizes WHERE shoe_id = ? ORDER BY size ASC");
        $sizeStmt->execute([$shoe['id']]);
        $sizes = $sizeStmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div class="shoe-card">
            <img src="<?= htmlspecialchars($shoe['image']) ?>" alt="<?= htmlspecialchars($shoe['name']) ?>">

            <h2><?= htmlspecialchars($shoe['name']) ?></h2>
            <p><strong>$<?= number_format($shoe['price'], 2) ?></strong></p>

            <form method="POST" action="cart.php">
    <input type="hidden" name="shoe_id" value="<?= $shoe['id'] ?>">

    <label>Select Size:</label>
    <select name="size" required>
        <?php foreach ($sizes as $size): ?>
            <?php if ($size['stock'] > 0): ?>
                <option value="<?= $size['size'] ?>" data-stock="<?= $size['stock'] ?>">
                    Size <?= $size['size'] ?> - <?= $size['stock'] ?> in stock
                </option>
            <?php else: ?>
                <option disabled>
                    Size <?= $size['size'] ?> - Out of stock
                </option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>

    <label>Quantity:</label>
    <input 
        type="number" 
        name="quantity" 
        value="1" 
        min="1" 
        required
    >

    <button type="submit" name="add_to_cart">Add to Cart</button>
</form>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
