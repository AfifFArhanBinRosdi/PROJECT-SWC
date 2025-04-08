<?php
require_once '../includes/config.php';
$page_title = "Manage Products";

// Admin authentication check
if(!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once 'includes/admin_header.php';

// Handle product actions
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['add_product'])) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = (float)$_POST['price'];
        $category = trim($_POST['category']);
        $game_category = trim($_POST['game_category']);
        $stock = (int)$_POST['stock'];
        
        // Handle image upload
        $image = '';
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../assets/images/";
            $imageFileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $valid_extensions = ['jpg', 'jpeg', 'png', 'webp'];
            
            if(in_array($imageFileType, $valid_extensions)) {
                // Generate unique filename
                $image = uniqid() . '.' . $imageFileType;
                $target_file = $target_dir . $image;
                
                if(move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    // File uploaded successfully
                } else {
                    $error = "Sorry, there was an error uploading your file.";
                }
            } else {
                $error = "Only JPG, JPEG, PNG & WEBP files are allowed.";
            }
        } else {
            $error = "Please select an image file.";
        }
        
        if(!isset($error)) {
            $query = "INSERT INTO products (name, description, price, image, category, game_category, stock) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssdsssi", $name, $description, $price, $image, $category, $game_category, $stock);
            
            if($stmt->execute()) {
                $_SESSION['message'] = "Product added successfully!";
                header("Location: manage_products.php");
                exit();
            } else {
                $error = "Failed to add product. Error: " . $conn->error;
            }
        }
    } elseif(isset($_POST['update_product'])) {
        $id = (int)$_POST['id'];
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = (float)$_POST['price'];
        $category = trim($_POST['category']);
        $game_category = trim($_POST['game_category']);
        $stock = (int)$_POST['stock'];
        $current_image = $_POST['current_image'];
        
        // Handle image upload if new image is provided
        $image = $current_image;
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../assets/images/";
            $imageFileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $valid_extensions = ['jpg', 'jpeg', 'png', 'webp'];
            
            if(in_array($imageFileType, $valid_extensions)) {
                // Delete old image if exists
                if($current_image && file_exists($target_dir . $current_image)) {
                    unlink($target_dir . $current_image);
                }
                
                // Generate unique filename
                $image = uniqid() . '.' . $imageFileType;
                $target_file = $target_dir . $image;
                
                if(!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $error = "Sorry, there was an error uploading your file.";
                }
            } else {
                $error = "Only JPG, JPEG, PNG & WEBP files are allowed.";
            }
        }
        
        if(!isset($error)) {
            $query = "UPDATE products SET 
                      name = ?, 
                      description = ?, 
                      price = ?, 
                      image = ?, 
                      category = ?, 
                      game_category = ?, 
                      stock = ? 
                      WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssdsssii", $name, $description, $price, $image, $category, $game_category, $stock, $id);
            
            if($stmt->execute()) {
                $_SESSION['message'] = "Product updated successfully!";
                header("Location: manage_products.php");
                exit();
            } else {
                $error = "Failed to update product. Error: " . $conn->error;
            }
        }
    } elseif(isset($_POST['delete_product'])) {
        $id = (int)$_POST['id'];
        
        // First get image path to delete the file
        $query = "SELECT image FROM products WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        
        // Delete product
        $query = "DELETE FROM products WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        
        if($stmt->execute()) {
            // Delete image file if exists
            if($product['image'] && file_exists("../assets/images/" . $product['image'])) {
                unlink("../assets/images/" . $product['image']);
            }
            
            $_SESSION['message'] = "Product deleted successfully!";
            header("Location: manage_products.php");
            exit();
        } else {
            $error = "Failed to delete product.";
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Products</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product</button>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price (RM)</th>
                    <th>Stock</th>
                    <th>Category</th>
                    <th>Game</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM products ORDER BY id DESC";
                $result = $conn->query($query);
                
                while($product = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td>
                        <?php if($product['image']): ?>
                            <img src="../assets/images/<?= $product['image'] ?>" width="50" alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td>RM<?= number_format($product['price'], 2) ?></td>
                    <td><?= $product['stock'] ?></td>
                    <td><?= ucfirst($product['category']) ?></td>
                    <td><?= ucfirst($product['game_category']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editProductModal<?= $product['id'] ?>">Edit</button>
                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteProductModal<?= $product['id'] ?>">Delete</button>
                    </td>
                </tr>
                
                <!-- Edit Product Modal -->
                <div class="modal fade" id="editProductModal<?= $product['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Product</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="post" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                    <input type="hidden" name="current_image" value="<?= $product['image'] ?>">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" rows="3" required><?= htmlspecialchars($product['description']) ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Price (RM)</label>
                                        <input type="number" step="0.01" class="form-control" name="price" value="<?= $product['price'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Category</label>
                                        <select class="form-select" name="category" required>
                                            <option value="clothing" <?= $product['category'] == 'clothing' ? 'selected' : '' ?>>Clothing</option>
                                            <option value="poster" <?= $product['category'] == 'poster' ? 'selected' : '' ?>>Poster</option>
                                            <option value="figurine" <?= $product['category'] == 'figurine' ? 'selected' : '' ?>>Figurine</option>
                                            <option value="collector" <?= $product['category'] == 'collector' ? 'selected' : '' ?>>Collector's Item</option>
                                            <option value="gaming" <?= $product['category'] == 'gaming' ? 'selected' : '' ?>>Gaming Item</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Game Category</label>
                                        <select class="form-select" name="game_category" required>
                                            <option value="genshin" <?= $product['game_category'] == 'genshin' ? 'selected' : '' ?>>Genshin Impact</option>
                                            <option value="starrail" <?= $product['game_category'] == 'starrail' ? 'selected' : '' ?>>Honkai Star Rail</option>
                                            <option value="zenless" <?= $product['game_category'] == 'zenless' ? 'selected' : '' ?>>Zenless Zone Zero</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Stock</label>
                                        <input type="number" class="form-control" name="stock" value="<?= $product['stock'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Image</label>
                                        <input type="file" class="form-control" name="image">
                                        <?php if($product['image']): ?>
                                            <small class="text-muted">Current: <?= $product['image'] ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="update_product" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Delete Product Modal -->
                <div class="modal fade" id="deleteProductModal<?= $product['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Delete Product</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="post">
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                    <p>Are you sure you want to delete "<?= htmlspecialchars($product['name']) ?>"?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="delete_product" class="btn btn-danger">Delete</button>
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

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price (RM)</label>
                        <input type="number" step="0.01" class="form-control" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category" required>
                            <option value="clothing">Clothing</option>
                            <option value="poster">Poster</option>
                            <option value="figurine">Figurine</option>
                            <option value="collector">Collector's Item</option>
                            <option value="gaming">Gaming Item</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Game Category</label>
                        <select class="form-select" name="game_category" required>
                            <option value="genshin">Genshin Impact</option>
                            <option value="starrail">Honkai Star Rail</option>
                            <option value="zenless">Zenless Zone Zero</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock</label>
                        <input type="number" class="form-control" name="stock" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" class="form-control" name="image" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?>