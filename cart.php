<?php
session_start();

$products = [
    1 => ["name" => "Nike Air Force 1", "price" => 115.00, "available" => true],
    2 => ["name" => "Air Jordan 1 Retro", "price" => 180.00, "available" => true],
    3 => ["name" => "Adidas Samba OG", "price" => 100.00, "available" => true],
    4 => ["name" => "New Balance 550", "price" => 110.00, "available" => false],
    5 => ["name" => "Yeezy Boost 350", "price" => 230.00, "available" => true],
];

// Remove one product from cart
if (isset($_GET["remove"])) {
    $remove_id = (int) $_GET["remove"];
    unset($_SESSION["cart"][$remove_id]);
    header("Location: cart.php");
    exit;
}

// Clear entire cart
if (isset($_GET["clear"])) {
    unset($_SESSION["cart"]);
    header("Location: cart.php");
    exit;
}

$cart = $_SESSION["cart"] ?? [];
$total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart - Showtime Sneakers</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 900px; margin: auto; background: white; padding: 25px; border-radius: 10px; }
        .top-links { margin: 20px 0; }
        .top-links a { margin-right: 15px; color: #111; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #111; color: white; }
        .btn { display: inline-block; padding: 10px 14px; background: #111; color: white; text-decoration: none; border-radius: 6px; margin-top: 15px; }
        .danger { background: #b00020; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Shopping Cart</h1>

        <div class="top-links">
            <a href="index.php">Continue Shopping</a>
            <a href="cart.php">View Cart</a>
        </div>

        <?php if (empty($cart)): ?>
            <p>Your cart is currently empty.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>

                <?php foreach ($cart as $id => $quantity): ?>
                    <?php
                    if (!isset($products[$id])) continue;
                    $subtotal = $products[$id]["price"] * $quantity;
                    $total += $subtotal;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($products[$id]["name"]); ?></td>
                        <td>$<?php echo number_format($products[$id]["price"], 2); ?></td>
                        <td><?php echo $quantity; ?></td>
                        <td>$<?php echo number_format($subtotal, 2); ?></td>
                        <td><a href="cart.php?remove=<?php echo $id; ?>">Remove</a></td>
                    </tr>
                <?php endforeach; ?>

                <tr>
                    <th colspan="3">Total</th>
                    <th colspan="2">$<?php echo number_format($total, 2); ?></th>
                </tr>
            </table>

            <a class="btn" href="checkout.php">Proceed to Checkout</a>
            <a class="btn danger" href="cart.php?clear=1">Clear Cart</a>
        <?php endif; ?>
    </div>
</body>
</html>
