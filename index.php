<?php
require_once 'includes/config.php';
$page_title = "Home";
require_once 'includes/header.php';
?>

<div class="hero-section mb-5">
    <div class="hero-content text-center p-5 bg-light rounded">
        <h1>Welcome to <?php echo $site_name; ?></h1>
        <p class="lead">Discover our exclusive collection of merchandise</p>
        <a href="products.php" class="btn btn-primary btn-lg">Shop Now</a>
    </div>
</div>

<h2 class="mb-4">Featured Products</h2>
<div class="row">
    <?php
    $query = "SELECT * FROM products ORDER BY RAND() LIMIT 3";
    $result = $conn->query($query);
    
    while($row = $result->fetch_assoc()):
    ?>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <img src="assets/images/<?php echo $row['image']; ?>" class="card-img-top" alt="<?php echo $row['name']; ?>">
            <div class="card-body">
                <h5 class="card-title"><?php echo $row['name']; ?></h5>
                <p class="card-text"><?php echo substr($row['description'], 0, 100); ?>...</p>
                <p class="text-primary"><?php echo $currency . $row['price']; ?></p>
            </div>
            <div class="card-footer bg-white">
                <a href="product.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">View Details</a>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php require_once 'includes/footer.php'; ?>