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
    $sql = "SELECT * FROM categories ORDER BY date_created ASC";
    return $con->query($sql);
}

// Fetch Products Function with Category Name
function getProducts($con) {
    $sql = "SELECT p.*, c.name AS category_name, 
                   IFNULL(i.stock_quantity, 0) AS stock_quantity, 
                   IFNULL(i.stock_in, 0) AS stock_in, 
                   IFNULL(i.stock_out, 0) AS stock_out, 
                   IFNULL(i.reorder_level, 0) AS reorder_level
            FROM products p
            JOIN categories c ON p.category_id = c.id
            LEFT JOIN inventory i ON p.id = i.product_id
            ORDER BY p.date_created DESC";

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
    $stock_in = isset($_POST['stock_in']) ? intval($_POST['stock_in']) : 0;
    $stock_out = 0; // New products have no stock out initially
    $stock_quantity = $stock_in; // Initial stock is equal to stock in

    // Insert into products table
    $sql_product = $con->prepare("INSERT INTO products (name, description, price, category_id, date_created) VALUES (?, ?, ?, ?, NOW())");
    $sql_product->bind_param("ssdi", $name, $description, $price, $category_id);
    $sql_product->execute();
    $product_id = $con->insert_id; // Get last inserted product ID

    // Insert into inventory table
    $sql_inventory = $con->prepare("INSERT INTO inventory (product_id, stock_quantity, stock_in, stock_out, last_updated) VALUES (?, ?, ?, ?, NOW())");
    $sql_inventory->bind_param("iiii", $product_id, $stock_quantity, $stock_in, $stock_out);
    $sql_inventory->execute();

    // Redirect to avoid form resubmission
    header("Location: inventory_page.php");
    exit();
}


// Update Product Function
if (isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    
    if (!empty($_FILES['image']['name'])) {
        $image = basename($_FILES['image']['name']);
        $target = "uploads/" . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $sql = "UPDATE products SET name='$name', image='$image', description='$description', price='$price', category_id='$category_id' WHERE id='$id'";
    } else {
        $sql = "UPDATE products SET name='$name', description='$description', price='$price', category_id='$category_id' WHERE id='$id'";
    }
    $con->query($sql);
}

// Delete Product Function
if (isset($_POST['delete_product'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM products WHERE id='$id'";
    $con->query($sql);
}
?>
<br>

<h1>Product List</h1>
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
                <th>Id</th>
                <th>Image</th>
                <th>Name</th>
           <!--     <th>Description</th> -->
                <th>Price</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Date Created</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $products = getProducts($con); $i = 1; ?>
            <?php while ($row = $products->fetch_assoc()) : ?>
            <tr>
                <td><?= $i++; ?></td>
                <td><img src="uploads/<?= $row['image']; ?>" width="50"></td>
              
                <td><?= $row['name']; ?></td>
           <!--     <td><?= $row['description']; ?></td> -->
                <td><?= $row['price']; ?></td>
                <td><?= $row['category_name']; ?></td>
                <td><?= isset($row['stock_quantity']) ? htmlspecialchars($row['stock_quantity']) : 0; ?></td>

                <td><?= $row['date_created']; ?></td>
                <td><span class="badge bg-success">Active</span></td>
                <td>
                <div style="display: flex; gap: 5px; align-items: center;">
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editProductModal<?= $row['id']; ?>">Edit</button>
                   <!-- <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteProductModal<?= $row['id']; ?>">Delete</button> -->
                </div>    
                </td>
            </tr>

            <!-- Edit Product Modal -->
            <div class="modal fade" id="editProductModal<?= $row['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                            <h2 class="modal-title" style="text-align: center; width: 100%;">Edit Product</h2>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" value="<?= $row['name']; ?>" required>
                                <label>Image</label>
                                <input type="file" name="image" class="form-control">
                                <label>Description</label>
                                <textarea name="description" class="form-control" required><?= $row['description']; ?></textarea>
                                <label>Price</label>
                                <input type="number" name="price" class="form-control" value="<?= $row['price']; ?>" required>
                                <label>Category</label>
                                <select name="category_id" class="form-control" required>
                                    <?php $categories = getCategories($con); ?>
                                    <?php while ($cat = $categories->fetch_assoc()) : ?>
                                        <option value="<?= $cat['id']; ?>" <?= ($cat['id'] == $row['category_id']) ? 'selected' : ''; ?>><?= $cat['name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="update_product" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete Product Modal 
            <div class="modal fade" id="deleteProductModal<?= $row['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title">Confirm Delete</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete "<?= $row['name']; ?>"?
                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="delete_product" class="btn btn-danger">Delete</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div> -->
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

                <h2 class="modal-title" style="text-align: center; width: 100%;">Add New Inventory</h2>

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

                    <label>Stock In</label>
                    <input type="number" name="stock_in" class="form-control" required placeholder="enter initial stock in">

                    <label>Stock Out</label>
                    <input type="number" name="stock_out" class="form-control" disabled value="" readonly placeholder="Auto-calculated (No stock out yet)">

                    <label>Stock Quantity</label>
                    <input type="number" name="stock_quantity" class="form-control" disabled required readonly placeholder="Calculated based on Stock In">

                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_product" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
