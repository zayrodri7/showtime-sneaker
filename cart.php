<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['add_to_cart'])) {
    $shoe_id = intval($_POST['shoe_id']);
    $size = intval($_POST['size']);
    $quantity = intval($_POST['quantity']);

    if ($quantity < 1) {
        die("Quantity must be at least 1.");
    }

    $stockStmt = $pdo->prepare("SELECT stock FROM shoe_sizes WHERE shoe_id = ? AND size = ?");
    $stockStmt->execute([$shoe_id, $size]);
    $stock = $stockStmt->fetchColumn();

    if ($stock === false || $stock <= 0) {
        die("This size is out of stock.");
    }

    if ($quantity > $stock) {
        die("You cannot add more than the available stock.");
    }

    $cartKey = $shoe_id . "_" . $size;

    if (!isset($_SESSION['cart'][$cartKey])) {
        $_SESSION['cart'][$cartKey] = [
            "shoe_id" => $shoe_id,
            "size" => $size,
            "quantity" => $quantity
        ];
    } else {
        $newQuantity = $_SESSION['cart'][$cartKey]['quantity'] + $quantity;

        if ($newQuantity > $stock) {
            die("You cannot add more than the available stock.");
        }

        $_SESSION['cart'][$cartKey]['quantity'] = $newQuantity;
    }

    header("Location: cart.php");
    exit;
}

if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background: #f4f4f4;
        }

        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .btn {
            display: inline-block;
            background: black;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }

        .remove {
            color: red;
            text-decoration: none;
        }
    </style>
</head>
<body>

<h1>Your Cart</h1>

<a class="btn" href="index.php">Continue Shopping</a>

<?php if (empty($_SESSION['cart'])): ?>
    <p>Your cart is empty.</p>
<?php else: ?>

<table>
    <tr>
        <th>Shoe</th>
        <th>Size</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Subtotal</th>
        <th>Remove</th>
    </tr>

    <?php
    $total = 0;

    foreach ($_SESSION['cart'] as $key => $item):
        $stmt = $pdo->prepare("SELECT * FROM shoes WHERE id = ?");
        $stmt->execute([$item['shoe_id']]);
        $shoe = $stmt->fetch(PDO::FETCH_ASSOC);

        $subtotal = $shoe['price'] * $item['quantity'];
        $total += $subtotal;
    ?>
        <tr>
            <td><?= htmlspecialchars($shoe['name']) ?></td>
            <td><?= htmlspecialchars($item['size']) ?></td>
            <td><?= htmlspecialchars($item['quantity']) ?></td>
            <td>$<?= number_format($shoe['price'], 2) ?></td>
            <td>$<?= number_format($subtotal, 2) ?></td>
            <td><a class="remove" href="cart.php?remove=<?= urlencode($key) ?>">Remove</a></td>
        </tr>
    <?php endforeach; ?>

    <tr>
        <td colspan="4"><strong>Total</strong></td>
        <td><strong>$<?= number_format($total, 2) ?></strong></td>
        <td></td>
    </tr>
</table>

<a class="btn" href="checkout.php">Checkout</a>

<?php endif; ?>

</body>
</html>
