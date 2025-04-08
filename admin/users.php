<?php
require_once '../includes/config.php';
$page_title = "Manage Users";

// Admin authentication check
if(!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once 'includes/admin_header.php';

// Handle user actions
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['delete_user'])) {
        $user_id = (int)$_POST['user_id'];
        
        // First delete user's cart items
        $conn->query("DELETE FROM user_carts WHERE user_id = $user_id");
        
        // Then delete the user
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        
        if($stmt->execute()) {
            $_SESSION['message'] = "User deleted successfully!";
            header("Location: users.php");
            exit();
        } else {
            $error = "Failed to delete user.";
        }
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
    <h2 class="my-4">Manage Users</h2>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Full Name</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM users ORDER BY id DESC";
                $result = $conn->query($query);
                
                while($user = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['full_name'] ?? 'N/A') ?></td>
                    <td><?= date('m/d/Y', strtotime($user['created_at'])) ?></td>
                    <td>
                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal<?= $user['id'] ?>">Delete</button>
                    </td>
                </tr>
                
                <!-- Delete User Modal -->
                <div class="modal fade" id="deleteUserModal<?= $user['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Delete User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="post">
                                <div class="modal-body">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <p>Are you sure you want to delete user "<?= htmlspecialchars($user['username']) ?>"?</p>
                                    <p class="text-danger">Warning: This will also delete all their cart items!</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="delete_user" class="btn btn-danger">Delete</button>
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