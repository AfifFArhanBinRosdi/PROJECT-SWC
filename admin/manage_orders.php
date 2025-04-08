<?php
require_once '../includes/config.php';
$page_title = "Manage Orders";

// Admin authentication check
if(!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once 'includes/admin_header.php';

// Handle order status update
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    
    $query = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $order_id);
    
    if($stmt->execute()) {
        $_SESSION['message'] = "Order status updated successfully!";
        header("Location: manage_orders.php");
        exit();
    } else {
        $error = "Failed to update order status.";
    }
}

// Display message if exists
if(isset($_SESSION['message'])) {
    echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']);
}

if(isset($error)) {
    echo '<div class="alert alert-danger">' . $error . '</div>';
}
?>

<div class="container">
    <h2 class="my-4">Manage Orders</h2>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total (RM)</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT o.id, o.created_at, o.total, o.status, u.username, u.email 
                          FROM orders o 
                          JOIN users u ON o.user_id = u.id 
                          ORDER BY o.created_at DESC";
                $result = $conn->query($query);
                
                while($order = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($order['username']) ?></strong><br>
                    <small class="text-muted"><?= htmlspecialchars($order['email']) ?></small><br>
                    <?php
                    // Fetch first product name for display in the table
                     $product_query = "SELECT p.name FROM order_items oi 
                     JOIN products p ON oi.product_id = p.id 
                     WHERE oi.order_id = ? LIMIT 1";
                    $stmt = $conn->prepare($product_query);
                    $stmt->bind_param("i", $order['id']);
                    $stmt->execute();
                    $product_result = $stmt->get_result();
                    if ($product_row = $product_result->fetch_assoc()) {
                        echo '<small class="text-muted">' . htmlspecialchars($product_row['name']) . '</small>';
                    }
                    ?>
                    </td>
                    <td><?= date('F j, Y H:i', strtotime($order['created_at'])) ?></td>
                    <td>RM<?= number_format($order['total'], 2) ?></td>
                    <td>
                        <span class="badge 
                            <?= $order['status'] == 'completed' ? 'bg-success' : 
                               ($order['status'] == 'cancelled' ? 'bg-danger' : 'bg-warning') ?>">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#orderDetailsModal<?= $order['id'] ?>">Details</button>
                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#statusModal<?= $order['id'] ?>">Update Status</button>
                    </td>
                </tr>
                
                <!-- Order Details Modal - Same structure for ALL orders -->
                <div class="modal fade" id="orderDetailsModal<?= $order['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Order #<?= $order['id'] ?> Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <?php
                                // Get order items
                                $items_query = "SELECT oi.*, p.name, p.image 
                                               FROM order_items oi 
                                               JOIN products p ON oi.product_id = p.id 
                                               WHERE oi.order_id = ?";
                                $stmt = $conn->prepare($items_query);
                                $stmt->bind_param("i", $order['id']);
                                $stmt->execute();
                                $items_result = $stmt->get_result();
                                ?>
                                
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6>Customer Information</h6>
                                        <p>
                                            <strong>Username:</strong> <?= htmlspecialchars($order['username']) ?><br>
                                            <strong>Email:</strong> <?= htmlspecialchars($order['email']) ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Order Information</h6>
                                        <p>
                                            <strong>Order Date:</strong> <?= date('F j, Y H:i', strtotime($order['created_at'])) ?><br>
                                            <strong>Status:</strong> <span class="badge 
                                                <?= $order['status'] == 'completed' ? 'bg-success' : 
                                                   ($order['status'] == 'cancelled' ? 'bg-danger' : 'bg-warning') ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                <h6>Order Items</h6>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Price</th>
                                                <th>Quantity</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            while($item = $items_result->fetch_assoc()): 
                                                $subtotal = $item['price'] * $item['quantity'];
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if($item['image']): ?>
                                                            <img src="../assets/images/<?= $item['image'] ?>" width="50" class="me-3" alt="<?= htmlspecialchars($item['name']) ?>">
                                                        <?php endif; ?>
                                                        <div>
                                                            <?= htmlspecialchars($item['name']) ?><br>
                                                            <small class="text-muted">SKU: <?= $item['product_id'] ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>RM<?= number_format($item['price'], 2) ?></td>
                                                <td><?= $item['quantity'] ?></td>
                                                <td>RM<?= number_format($subtotal, 2) ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                            <tr class="table-active">
                                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                                <td><strong>RM<?= number_format($order['total'], 2) ?></strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" onclick="window.location.href='dashboard.php'">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Status Update Modal -->
                <div class="modal fade" id="statusModal<?= $order['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Update Order Status</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="post">
                                <div class="modal-body">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status">
                                            <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                                            <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                            <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                            <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="update_status" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?>