<?php
// Database Connection
include "includes/connection.php";

// Fetch Categories Function
function getCategories($con) {
    $sql = "SELECT * FROM categories ORDER BY date_created DESC";
    $result = $con->query($sql);
    return $result;
}

// Fetch Products Function with Category Name
function getProducts($con) {
    $sql = "SELECT products.*, categories.name AS category_name FROM products 
            JOIN categories ON products.category_id = categories.id 
            ORDER BY products.date_created DESC";
    $result = $con->query($sql);
    return $result;
}

// Add Product Function
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $status = 'Active';
    $sql = "INSERT INTO products (name, description, price, category_id, status, date_created) VALUES ('$name', '$description', '$price', '$category_id', '$status', NOW())";
    $con->query($sql);
}

// Update Product Function
if (isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $sql = "UPDATE products SET name='$name', description='$description', price='$price', category_id='$category_id' WHERE id='$id'";
    $con->query($sql);
}

// Delete Product Function
if (isset($_POST['delete_product'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM products WHERE id='$id'";
    $con->query($sql);
}
?>

<div class="container mt-4">
    <h2>Product List</h2>
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
                    <h5 class="modal-title">Add Product</h5>
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
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_product" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
