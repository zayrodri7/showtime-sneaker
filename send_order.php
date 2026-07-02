<?php
session_start();
require_once "db.php";

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$customer_name = $_POST['customer_name'];
$customer_email = $_POST['customer_email'];
$address = $_POST['address'];

try {
    $pdo->beginTransaction();

    $orderDetails = "";
    $total = 0;
    $itemsToSave = [];

    foreach ($_SESSION['cart'] as $item) {
        $shoe_id = $item['shoe_id'];
        $size = $item['size'];
        $quantity = $item['quantity'];

        $stockStmt = $pdo->prepare("
            SELECT stock 
            FROM shoe_sizes 
            WHERE shoe_id = ? AND size = ? 
            FOR UPDATE
        ");
        $stockStmt->execute([$shoe_id, $size]);
        $currentStock = $stockStmt->fetchColumn();

        if ($currentStock === false) {
            throw new Exception("Invalid shoe size selected.");
        }

        if ($currentStock < $quantity) {
            throw new Exception("Not enough stock available for one of the selected items.");
        }

        $shoeStmt = $pdo->prepare("SELECT name, price FROM shoes WHERE id = ?");
        $shoeStmt->execute([$shoe_id]);
        $shoe = $shoeStmt->fetch(PDO::FETCH_ASSOC);

        $subtotal = $shoe['price'] * $quantity;
        $total += $subtotal;

        $itemsToSave[] = [
            "shoe_id" => $shoe_id,
            "shoe_name" => $shoe['name'],
            "size" => $size,
            "quantity" => $quantity,
            "price" => $shoe['price'],
            "subtotal" => $subtotal
        ];

        $orderDetails .= $shoe['name'] . " - Size " . $size . " - Qty: " . $quantity . " - $" . number_format($subtotal, 2) . "\n";

        $updateStmt = $pdo->prepare("
            UPDATE shoe_sizes 
            SET stock = stock - ? 
            WHERE shoe_id = ? AND size = ?
        ");
        $updateStmt->execute([$quantity, $shoe_id, $size]);
    }

    $orderStmt = $pdo->prepare("
        INSERT INTO orders (customer_name, customer_email, address, total, status)
        VALUES (?, ?, ?, ?, 'Active')
    ");
    $orderStmt->execute([$customer_name, $customer_email, $address, $total]);

    $order_id = $pdo->lastInsertId();

    foreach ($itemsToSave as $item) {
        $itemStmt = $pdo->prepare("
            INSERT INTO order_items 
            (order_id, shoe_id, shoe_name, size, quantity, price, subtotal)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $itemStmt->execute([
            $order_id,
            $item['shoe_id'],
            $item['shoe_name'],
            $item['size'],
            $item['quantity'],
            $item['price'],
            $item['subtotal']
        ]);
    }

    $pdo->commit();

    $to = "Isaiahboss27@gmail.com";
    $subject = "New Sneaker Order #" . $order_id;
    $message = "
New order received:

Order ID: $order_id
Customer Name: $customer_name
Customer Email: $customer_email
Address: $address

Order Details:
$orderDetails

Total: $" . number_format($total, 2);

    $headers = "From: no-reply@showtimesneakers.com";

    mail($to, $subject, $message, $headers);

    $_SESSION['cart'] = [];

} catch (Exception $e) {
    $pdo->rollBack();
    die("Order failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmed</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background: #f4f4f4;
        }

        .box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 800px;
            margin: auto;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }

        h1 {
            color: green;
        }

        .info {
            background: #f7f7f7;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #111;
            color: white;
        }

        .total {
            font-size: 20px;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            background: black;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 6px;
        }
    </style>
</head>
<body>

<div class="box">
    <h1>Order Confirmed</h1>

    <p>Thank you, <?= htmlspecialchars($customer_name) ?>. Your order has been placed successfully.</p>

    <div class="info">
        <p><strong>Order ID:</strong> #<?= htmlspecialchars($order_id) ?></p>
        <p><strong>Name:</strong> <?= htmlspecialchars($customer_name) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($customer_email) ?></p>
        <p><strong>Address:</strong><br><?= nl2br(htmlspecialchars($address)) ?></p>
    </div>

    <h2>Order Summary</h2>

    <table>
        <tr>
            <th>Shoe</th>
            <th>Size</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Subtotal</th>
        </tr>

        <?php foreach ($itemsToSave as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['shoe_name']) ?></td>
                <td><?= htmlspecialchars($item['size']) ?></td>
                <td><?= htmlspecialchars($item['quantity']) ?></td>
                <td>$<?= number_format($item['price'], 2) ?></td>
                <td>$<?= number_format($item['subtotal'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p class="total">Total: $<?= number_format($total, 2) ?></p>

    <p>The stock has been updated automatically.</p>

    <a href="index.php">Back to Store</a>
</div>

</body>
</html>
