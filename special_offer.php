<?php
require_once 'includes/config.php';
$page_title = "Special Offers";
require_once 'includes/header.php';

// Only show products with stock less than 30
$query = "SELECT *, 
          CASE WHEN stock < 30 THEN price * 0.9 ELSE price END AS discounted_price,
          CASE WHEN stock < 30 THEN 1 ELSE 0 END AS has_discount
          FROM products 
          WHERE stock < 30
          ORDER BY stock ASC"; // Show items with lowest stock first

// Get products
$result = $conn->query($query);

// Category and game names for display
$category_names = [
    'clothing' => 'Clothing',
    'poster' => 'Posters',
    'figurine' => 'Figurines',
    'collector' => 'Collectibles',
    'gaming' => 'Gaming Gear'
];

$game_names = [
    'genshin' => 'Genshin Impact',
    'starrail' => 'Honkai: Star Rail',
    'zenless' => 'Zenless Zone Zero'
];
?>

<div class="shop-container">
    <div class="shop-header">
        <h1>SPECIAL OFFERS</h1>
        <div class="offer-description">
            <p>Limited stock items with <strong>10% discount</strong>! Grab them before they're gone!</p>
        </div>
    </div>

    <div class="product-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <a href="product.php?id=<?php echo $row['id']; ?>">
                        <div class="product-image">
                            <img src="assets/images/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                            <span class="discount-badge">10% OFF</span>
                            <span class="stock-badge">Only <?php echo $row['stock']; ?> left</span>
                        </div>
                        <div class="product-info">
                            <h3><?php echo $row['name']; ?></h3>
                            <div class="product-meta">
                                <span class="game-badge <?php echo $row['game_category']; ?>">
                                    <?php echo $game_names[$row['game_category']] ?? 'HoyoVerse'; ?>
                                </span>
                                <div class="price-container">
                                    <?php if ($row['has_discount']): ?>
                                        <span class="original-price"><?php echo $currency . number_format($row['price'], 2); ?></span>
                                    <?php endif; ?>
                                    <span class="product-price <?php echo $row['has_discount'] ? 'discounted' : ''; ?>">
                                        <?php echo $currency . number_format($row['discounted_price'], 2); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-products">
                <p>No special offers available at the moment.</p>
                <a href="products.php" class="btn btn-primary">View All Products</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>