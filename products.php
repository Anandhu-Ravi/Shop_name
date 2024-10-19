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

$query = "SELECT * FROM products";
$result = $db->query($query);
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .btn-custom {
            background-color: #28a745;
            color: white;
        }

        .btn-custom:hover {
            background-color: #218838;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Available Products</h1>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card shadow-sm border-light">
                        <img src="images/<?php echo $product['image']; ?>" class="card-img-top" alt="<?php echo $product['name']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $product['name']; ?></h5>
                            <p class="card-text"><?php echo $product['description']; ?></p>
                            <p class="text-success fw-bold">$<?php echo number_format($product['price'], 2); ?></p>
                            <form method="POST" action="cart.php">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="add_to_cart" value="1">
                                <div class="mb-3">
                                    <input type="number" name="quantity" class="form-control" value="1" min="1" aria-label="Quantity">
                                </div>
                                <button type="submit" class="btn btn-custom w-100">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
