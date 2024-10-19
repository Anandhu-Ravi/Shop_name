<?php
session_start();
include_once 'dbinit.php';
include_once 'navbar.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_to_cart'])) {
        $user_id = $_SESSION['user']['id'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        $query = "SELECT quantity FROM products WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->bind_result($available_quantity);
        $stmt->fetch();
        $stmt->close();

        if ($quantity <= $available_quantity) {
            $query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->bind_param("iii", $user_id, $product_id, $quantity);
            $stmt->execute();
        } else {
            echo "Insufficient stock for this product.";
        }
    }

    if (isset($_POST['delete_item'])) {
        $cart_id = $_POST['cart_id'];

        $query = "DELETE FROM cart WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $cart_id);
        $stmt->execute();
    }

    if (isset($_POST['buy_now'])) {
        $user_id = $_SESSION['user']['id'];

        $query = "DELETE FROM cart WHERE user_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        echo "<script>alert('Order Successful!'); window.location.href='products.php';</script>";
        exit;
    }
}

$query = "SELECT c.id as cart_id, p.name, p.price, c.quantity 
          FROM cart c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.user_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $_SESSION['user']['id']);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Your Cart</h1>
        <ul class="list-group">
            <?php foreach ($cart_items as $item): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?php echo $item['name'] . " x " . $item['quantity']; ?>
                    <span class="badge bg-primary rounded-pill">$<?php echo $item['price']; ?></span>
                    <form method="POST" action="cart.php" class="ms-2">
                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                        <button type="submit" name="delete_item" class="btn btn-danger btn-sm">Remove</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if (count($cart_items) > 0): ?>
            <form method="POST" action="cart.php" class="mt-4">
                <button type="submit" name="buy_now" class="btn btn-success w-100">Buy Now</button>
            </form>
        <?php else: ?>
            <p class="text-center mt-4">Your cart is empty.</p>
        <?php endif; ?>
    </div>
</body>
</html>
