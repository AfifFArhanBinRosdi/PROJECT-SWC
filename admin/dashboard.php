<?php
require_once '../includes/config.php';
$page_title = "Admin Dashboard";

// Admin authentication check
if(!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once 'includes/admin_header.php';

// Get statistics
$products_count = $conn->query("SELECT COUNT(id) as count FROM products")->fetch_assoc()['count'];
$orders_count = $conn->query("SELECT COUNT(id) as count FROM orders")->fetch_assoc()['count'];
$users_count = $conn->query("SELECT COUNT(id) as count FROM users")->fetch_assoc()['count'];
?>

<div class="container">
    <h2 class="my-4">Admin Dashboard</h2>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Products</h5>
                    <p class="card-text display-4"><?= $products_count ?></p>
                    <a href="manage_products.php" class="text-white">Manage Products</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Orders</h5>
                    <p class="card-text display-4"><?= $orders_count ?></p>
                    <a href="manage_orders.php" class="text-white">View Orders</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Users</h5>
                    <p class="card-text display-4"><?= $users_count ?></p>
                    <a href="manage_users.php" class="text-white">Manage Users</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Orders</h5>
                </div>
                <div class="card-body">
                    <?php
                    $query = "SELECT o.id, o.created_at, o.total, o.status, u.username 
                              FROM orders o 
                              JOIN users u ON o.user_id = u.id 
                              ORDER BY o.created_at DESC 
                              LIMIT 5";
                    $result = $conn->query($query);
                    ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($order = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $order['id'] ?></td>
                                    <td><?= htmlspecialchars($order['username']) ?></td>
                                    <td><?= date('m/d/Y', strtotime($order['created_at'])) ?></td>
                                    <td>RM<?= number_format($order['total'], 2) ?></td>
                                    <td><?= ucfirst($order['status']) ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="manage_orders.php" class="btn btn-primary">View All Orders</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Low Stock Products</h5>
                </div>
                <div class="card-body">
                    <?php
                    $query = "SELECT * FROM products WHERE stock < 10 ORDER BY stock ASC LIMIT 5";
                    $result = $conn->query($query);
                    ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($product = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($product['name']) ?></td>
                                    <td>RM<?= number_format($product['price'], 2) ?></td>
                                    <td class="<?= $product['stock'] < 5 ? 'text-danger' : 'text-warning' ?>">
                                        <?= $product['stock'] ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="manage_products.php" class="btn btn-primary">View All Products</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?>