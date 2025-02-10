<?php
// Database Connection
include "includes/connection.php";

// Fetch Categories Function
function getCategories($con) {
    $sql = "SELECT * FROM categories ORDER BY date_created DESC";
    $result = $con->query($sql);
    if (!$result) {
        die("Query failed: " . $con->error);
    }
    return $result;
}

// Fetch Products Function with Category Name and Inventory
function getProducts($con) {
    $sql = "SELECT products.*, categories.name AS category_name, inventory.stock_quantity FROM products 
            JOIN categories ON products.category_id = categories.id 
            LEFT JOIN inventory ON products.id = inventory.product_id
            ORDER BY products.date_created DESC";
    $result = $con->query($sql);
    if (!$result) {
        die("Query failed: " . $con->error);
    }
    return $result;
}

// Add Product Function
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $stock_quantity = $_POST['stock_quantity'];
    $status = 'Active';
    $sql = "INSERT INTO products (name, description, price, category_id, status, date_created) VALUES ('$name', '$description', '$price', '$category_id', '$status', NOW())";
    if (!$con->query($sql)) {
        die("Query failed: " . $con->error);
    }
    
    $product_id = $con->insert_id;
    $sql_inventory = "INSERT INTO inventory (product_id, stock_quantity) VALUES ('$product_id', '$stock_quantity')";
    if (!$con->query($sql_inventory)) {
        die("Query failed: " . $con->error);
    }
}

// Update Product Function
if (isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $stock_quantity = $_POST['stock_quantity'];
    $sql = "UPDATE products SET name='$name', description='$description', price='$price', category_id='$category_id' WHERE id='$id'";
    if (!$con->query($sql)) {
        die("Query failed: " . $con->error);
    }
    
    $sql_inventory = "UPDATE inventory SET stock_quantity='$stock_quantity' WHERE product_id='$id'";
    if (!$con->query($sql_inventory)) {
        die("Query failed: " . $con->error);
    }
}

// Delete Product Function
if (isset($_POST['delete_product'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM products WHERE id='$id'";
    if (!$con->query($sql)) {
        die("Query failed: " . $con->error);
    }
    
    $sql_inventory = "DELETE FROM inventory WHERE product_id='$id'";
    if (!$con->query($sql_inventory)) {
        die("Query failed: " . $con->error);
    }
}
?>

<div class="container mt-4">
    <h2> Inventory List</h2>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addProductModal">+ Add Product</button>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Date Created</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Category</th>
                <th>Stock Quantity</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $products = getProducts($con); $i = 1; ?>
            <?php while ($row = $products->fetch_assoc()) : ?>
            <tr>
                <td><?= $i++; ?></td>
                <td><?= $row['date_created']; ?></td>
                <td><?= $row['name']; ?></td>
                <td><?= $row['description']; ?></td>
                <td><?= $row['price']; ?></td>
                <td><?= $row['category_name']; ?></td>
                <td><?= $row['stock_quantity']; ?></td>
                <td><span class="badge bg-success">Active</span></td>
                <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editProductModal<?= $row['id']; ?>">Edit</button>
                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteProductModal<?= $row['id']; ?>">Delete</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Inventory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                    <label>Description</label>
                    <textarea name="description" class="form-control" required></textarea>
                    <label>Price</label>
                    <input type="number" name="price" class="form-control" required>
                    <label>Category</label>
                    <select name="category_id" class="form-control" required>
                        <?php $categories = getCategories($con); ?>
                        <?php while ($cat = $categories->fetch_assoc()) : ?>
                            <option value="<?= $cat['id']; ?>"><?= $cat['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                    <label>Stock Quantity</label>
                    <input type="number" name="stock_quantity" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_product" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>


