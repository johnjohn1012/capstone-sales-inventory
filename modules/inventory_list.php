<?php
// Database Connection
include "includes/connection.php";

// Handle Pagination
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? intval($_GET['limit']) : 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = max(0, ($page - 1) * $limit);

// Search Input Handling
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Count total categories
$count_query = "SELECT COUNT(*) as total FROM categories";
if (!empty($search)) {
    $count_query .= " WHERE name LIKE ? OR description LIKE ?";
}
$stmt = $con->prepare($count_query);

if (!empty($search)) {
    $search_param = "%$search%";
    $stmt->bind_param("ss", $search_param, $search_param);
}

$stmt->execute();
$count_result = $stmt->get_result();
$total_categories = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_categories / $limit);

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
<br>
<h1>Inventory List</h1>

<div class="container mt-4">

  

            <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 10px;">

        <!-- Show Entries Dropdown -->
        <label style="display: flex; align-items: center; gap: 5px;">
        Show entries
        <select id="entriesSelect" class="form-control form-control-sm">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
        </label>


        <!-- Search Input -->
        <form method="GET" action="">
        <div style="display: flex; gap: 5px; align-items: center;">
            <input type="text" name="search" value="<?= $search; ?>" class="form-control" 
                placeholder="Search here" style="flex: 1; max-width: 250px;">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
        </form>


        <!-- Create New Button -->
        <button class="btn btn-primary" style="padding: 10px; width: 200px;" data-bs-toggle="modal" data-bs-target="#addProductModal">
        + Create New
        </button>

        </div>

  

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
                <div style="display: flex; gap: 5px; align-items: center;">
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" 
                            data-bs-target="#editProductModal<?= $row['id']; ?>">Edit</button>
                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" 
                            data-bs-target="#deleteProductModal<?= $row['id']; ?>">Delete</button>
                </div>

                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

                        <!-- Pagination -->
                <div style="display: flex; justify-content: flex-end;">
                    <nav>
                        <ul class="pagination">
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page - 1; ?>&limit=<?= $limit; ?>">Previous</a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i; ?>&limit=<?= $limit; ?>"> <?= $i; ?> </a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page + 1; ?>&limit=<?= $limit; ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
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

<!-- Edit Product Modal -->
<?php $products->data_seek(0); // Reset the products result pointer ?>
<?php while ($row = $products->fetch_assoc()) : ?>
<div class="modal fade" id="editProductModal<?= $row['id']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Inventory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="<?= $row['name']; ?>" required>
                    <label>Description</label>
                    <textarea name="description" class="form-control" required><?= $row['description']; ?></textarea>
                    <label>Price</label>
                    <input type="number" name="price" class="form-control" value="<?= $row['price']; ?>" required>
                    <label>Category</label>
                    <select name="category_id" class="form-control" required>
                        <?php 
                        $categories->data_seek(0); // Reset categories result pointer
                        while ($cat = $categories->fetch_assoc()) : 
                        ?>
                            <option value="<?= $cat['id']; ?>" <?= ($cat['id'] == $row['category_id']) ? 'selected' : ''; ?>>
                                <?= $cat['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <label>Stock Quantity</label>
                    <input type="number" name="stock_quantity" class="form-control" value="<?= $row['stock_quantity']; ?>" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update_product" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Product Modal -->
<div class="modal fade" id="deleteProductModal<?= $row['id']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Inventory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                    <p>Are you sure you want to delete <strong><?= $row['name']; ?></strong>?</p>
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


