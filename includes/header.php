<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title . " | " . $site_name; ?></title>
    <link rel="stylesheet" href="<?php echo $site_url; ?>/assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="<?php echo $site_url; ?>"><?php echo $site_name; ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link" href="<?php echo $site_url; ?>">Home</a></li>
                        
                        <!-- Categories Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Products
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
                                <li><a class="dropdown-item" href="products.php?category=all">All Products</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="products.php?category=clothing">Clothing</a></li>
                                <li><a class="dropdown-item" href="products.php?category=poster">Posters</a></li>
                                <li><a class="dropdown-item" href="products.php?category=figurine">Figurines</a></li>
                                <li><a class="dropdown-item" href="products.php?category=collector">Collector's Items</a></li>
                                <li><a class="dropdown-item" href="products.php?category=gaming">Gaming Items</a></li>
                            </ul>
                        </li>
                        
                         <li class="nav-item"><a class="nav-link" href="special_offer.php">Special Offer</a></li>
                         <li class="nav-item"><a class="nav-link" href="about_us.php">About Us</a></li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="cart.php">
                                Cart <span class="badge bg-primary">
                                    <?php 
                                    // Initialize cart count
                                    $cart_count = 0;
                                    
                                    // Check if user is logged in
                                    if(isset($_SESSION['user_id']) && isset($conn)) {
                                        // For logged-in users, get count from database only
                                        $user_id = $_SESSION['user_id'];
                                        $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM user_carts WHERE user_id = ?");
                                        $stmt->bind_param("i", $user_id);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        
                                        if($row = $result->fetch_assoc()) {
                                            $cart_count = (int)$row['total'];
                                        }
                                    } else {
                                        // For guests, get count from session only
                                        if(isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                                            $cart_count = array_sum($_SESSION['cart']);
                                        }
                                    }
                                    
                                    echo $cart_count > 0 ? $cart_count : '0';
                                    ?>
                                </span>
                            </a>
                        </li>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li class="nav-item"><a class="nav-link" href="account.php">Account</a></li>
                            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                            <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main class="container my-4">