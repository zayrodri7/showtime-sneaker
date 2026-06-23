<?php
session_start();

// Product list: id => product details
$products = [
    1 => ["name" => "Nike Air Force 1", "price" => 115.00, "available" => true],
    2 => ["name" => "Air Jordan 1 Retro", "price" => 180.00, "available" => true],
    3 => ["name" => "Adidas Samba OG", "price" => 100.00, "available" => true],
    4 => ["name" => "New Balance 550", "price" => 110.00, "available" => false],
    5 => ["name" => "Yeezy Boost 350", "price" => 230.00, "available" => true],
];

// Add product to cart
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["product_id"])) {
    $product_id = (int) $_POST["product_id"];

    if (isset($products[$product_id]) && $products[$product_id]["available"]) {
        if (!isset($_SESSION["cart"])) {
            $_SESSION["cart"] = [];
        }

        if (!isset($_SESSION["cart"][$product_id])) {
            $_SESSION["cart"][$product_id] = 0;
        }

        $_SESSION["cart"][$product_id]++;
        $message = $products[$product_id]["name"] . " was added to your cart.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Showtime Sneakers</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 900px; margin: auto; background: white; padding: 25px; border-radius: 10px; }
        h1 { margin-bottom: 5px; }
        .top-links { margin: 20px 0; }
        .top-links a { margin-right: 15px; color: #111; font-weight: bold; }
        .product { border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 8px; }
        .price { font-weight: bold; }
        .available { color: green; }
        .unavailable { color: red; }
        button { padding: 10px 14px; background: #111; color: white; border: none; border-radius: 6px; cursor: pointer; }
        button:disabled { background: #888; cursor: not-allowed; }
        .message { background: #e7ffe7; padding: 10px; border: 1px solid #8ad18a; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Showtime Sneakers</h1>
        <p>Shop sneakers and add available products to your cart.</p>

        <div class="top-links">
            <a href="index.php">Products</a>
            <a href="cart.php">View Cart</a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php foreach ($products as $id => $product): ?>
            <div class="product">
                <h2><?php echo htmlspecialchars($product["name"]); ?></h2>
                <p class="price">$<?php echo number_format($product["price"], 2); ?></p>

                <?php if ($product["available"]): ?>
                    <p class="available">In Stock</p>
                    <form method="POST" action="index.php">
                        <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                        <button type="submit">Add to Cart</button>
                    </form>
                <?php else: ?>
                    <p class="unavailable">Out of Stock</p>
                    <button disabled>Unavailable</button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
