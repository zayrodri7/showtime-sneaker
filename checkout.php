<?php
session_start();

$products = [
    1 => ["name" => "Nike Air Force 1", "price" => 115.00, "available" => true],
    2 => ["name" => "Air Jordan 1 Retro", "price" => 180.00, "available" => true],
    3 => ["name" => "Adidas Samba OG", "price" => 100.00, "available" => true],
    4 => ["name" => "New Balance 550", "price" => 110.00, "available" => false],
    5 => ["name" => "Yeezy Boost 350", "price" => 230.00, "available" => true],
];

$cart = $_SESSION["cart"] ?? [];
$total = 0;

foreach ($cart as $id => $quantity) {
    if (isset($products[$id])) {
        $total += $products[$id]["price"] * $quantity;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Showtime Sneakers</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 700px; margin: auto; background: white; padding: 25px; border-radius: 10px; }
        .top-links { margin: 20px 0; }
        .top-links a { margin-right: 15px; color: #111; font-weight: bold; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input, textarea { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 6px; }
        button { padding: 12px 16px; background: #111; color: white; border: none; border-radius: 6px; margin-top: 20px; cursor: pointer; }
        .summary { background: #f0f0f0; padding: 15px; border-radius: 8px; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Checkout</h1>

        <div class="top-links">
            <a href="index.php">Products</a>
            <a href="cart.php">Back to Cart</a>
        </div>

        <?php if (empty($cart)): ?>
            <p>Your cart is empty. Please add products before checking out.</p>
        <?php else: ?>
            <div class="summary">
                <h3>Order Total: $<?php echo number_format($total, 2); ?></h3>
                <p>Enter your information below to place your order.</p>
            </div>

            <form method="POST" action="send_order.php">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>

                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>

                <label for="address">Shipping Address</label>
                <textarea id="address" name="address" rows="4" required></textarea>

                <button type="submit">Place Order</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
