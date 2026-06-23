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

if ($_SERVER["REQUEST_METHOD"] !== "POST" || empty($cart)) {
    header("Location: index.php");
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$address = trim($_POST["address"] ?? "");

if ($name === "" || $email === "" || $address === "") {
    die("Please complete all checkout fields.");
}

$total = 0;
$order_details = "New Showtime Sneakers Order\n\n";
$order_details .= "Customer Name: " . $name . "\n";
$order_details .= "Customer Email: " . $email . "\n";
$order_details .= "Shipping Address: " . $address . "\n\n";
$order_details .= "Order Items:\n";

foreach ($cart as $id => $quantity) {
    if (!isset($products[$id])) continue;

    $product_name = $products[$id]["name"];
    $price = $products[$id]["price"];
    $subtotal = $price * $quantity;
    $total += $subtotal;

    $order_details .= "- " . $product_name . " | Quantity: " . $quantity . " | Price: $" . number_format($price, 2) . " | Subtotal: $" . number_format($subtotal, 2) . "\n";
}

$order_details .= "\nTotal: $" . number_format($total, 2) . "\n";

// Change this to your own email address.
$to = "your-email@example.com";
$subject = "New Showtime Sneakers Order";
$headers = "From: no-reply@showtimesneakers.com\r\n";
$headers .= "Reply-To: " . $email . "\r\n";

// NOTE: PHP mail() may not work on local XAMPP unless SMTP is configured.
$email_sent = mail($to, $subject, $order_details, $headers);

// Clear the cart after order is placed.
unset($_SESSION["cart"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation - Showtime Sneakers</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 750px; margin: auto; background: white; padding: 25px; border-radius: 10px; }
        .success { background: #e7ffe7; padding: 15px; border: 1px solid #8ad18a; border-radius: 8px; }
        .warning { background: #fff5d6; padding: 15px; border: 1px solid #e0bf5b; border-radius: 8px; margin-top: 15px; }
        pre { background: #f0f0f0; padding: 15px; border-radius: 8px; white-space: pre-wrap; }
        a { color: #111; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order Confirmation</h1>

        <div class="success">
            <p>Thank you, <?php echo htmlspecialchars($name); ?>. Your order has been placed.</p>
        </div>

        <?php if ($email_sent): ?>
            <p>A confirmation email was sent to the store owner.</p>
        <?php else: ?>
            <div class="warning">
                <p>The order was created, but the email may not have sent because local XAMPP usually needs SMTP setup.</p>
                <p>For class/demo purposes, the order details are displayed below.</p>
            </div>
        <?php endif; ?>

        <h2>Order Details</h2>
        <pre><?php echo htmlspecialchars($order_details); ?></pre>

        <p><a href="index.php">Return to Products</a></p>
    </div>
</body>
</html>
