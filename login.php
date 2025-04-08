<?php
require_once 'includes/config.php';
$page_title = "Login";
require_once 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $login_type = $_POST['login_type'] ?? 'user';
    
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $table = ($login_type == 'admin') ? 'admins' : 'users';
        
        // Improved query with error handling
        $query = "SELECT id, username, password FROM $table WHERE username = ? LIMIT 1";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            die("Database error: " . $conn->error);
        }
        
        $stmt->bind_param("s", $username);
        
        if (!$stmt->execute()) {
            die("Query failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Debug output (remove after testing)
            error_log("Login attempt - User: $username, Hash: ".$user['password']);
            
            if (password_verify($password, $user['password'])) {
                // Successful login
                if ($login_type == 'admin') {
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    header("Location: admin/dashboard.php");
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    
                    // Check for pending cart action after successful user login
                    if (isset($_SESSION['cart_pending_product'])) {
                        $pending_product = $_SESSION['cart_pending_product'];
                        unset($_SESSION['cart_pending_product']);
                        
                        // Redirect to cart to process the pending action
                        header("Location: cart.php");
                    } 
                    // Check for regular login redirect
                    elseif (isset($_SESSION['login_redirect'])) {
                        $redirect = $_SESSION['login_redirect'];
                        unset($_SESSION['login_redirect']);
                        header("Location: $redirect");
                    } 
                    // Default redirect for users
                    else {
                        header("Location: index.php");
                    }
                }
                exit();
            } else {
                $error = "Invalid password. Please try again.";
                // Debug output
                error_log("Password verification failed for $username");
            }
        } else {
            $error = "Username not found.";
        }
    }
}?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Login</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Login As:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="login_type" id="userLogin" value="user" checked>
                            <label class="form-check-label" for="userLogin">User</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="login_type" id="adminLogin" value="admin">
                            <label class="form-check-label" for="adminLogin">Administrator</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
                
                <div class="mt-3">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>