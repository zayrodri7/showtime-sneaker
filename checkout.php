<?php
session_start();

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background: #f4f4f4;
        }

        form {
            background: white;
            padding: 25px;
            max-width: 500px;
            border-radius: 10px;
        }

        input, textarea, button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
        }

        button {
            background: black;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h1>Checkout</h1>

<form method="POST" action="send_order.php">
    <label>Name</label>
    <input type="text" name="customer_name" required>

    <label>Email</label>
    <input type="email" name="customer_email" required>

    <label>Address</label>
    <textarea name="address" required></textarea>

    <button type="submit">Place Order</button>
</form>

</body>
</html>
