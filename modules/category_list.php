<?php
// Database Connection
include "includes/connection.php";

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch Categories Function
function getCategories($con, $search = '') {
    $sql = "SELECT * FROM categories";
    if ($search) {
        $sql .= " WHERE name LIKE ? OR description LIKE ?";
    }
    $sql .= " ORDER BY date_created DESC";
    
    $stmt = $con->prepare($sql);
    if ($search) {
        $search_param = "%$search%";
        $stmt->bind_param("ss", $search_param, $search_param);
    }
    $stmt->execute();
    return $stmt->get_result();
}

// Add Category Function
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $status = 'Active';
    $sql = "INSERT INTO categories (name, description, status, date_created) VALUES (?, ?, ?, NOW())";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sss", $name, $description, $status);
    $stmt->execute();
}

// Update Category Function
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $sql = "UPDATE categories SET name = ?, description = ? WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssi", $name, $description, $id);
    $stmt->execute();
}

// Delete Category Function
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_category'])) {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM categories WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Toggle Status Function
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    $id = intval($_POST['id']);
    $current_status = $_POST['current_status'];
    $new_status = ($current_status === 'Active') ? 'Inactive' : 'Active';
    $sql = "UPDATE categories SET status = ? WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("si", $new_status, $id);
    $stmt->execute();
}
?>

<br><br><br>
<form method="GET" class="mb-3">
    <input type="text" name="search" class="form-control" placeholder="Search categories..." value="<?= htmlspecialchars($search); ?>">
    <button type="submit" class="btn btn-secondary mt-2">Search</button>
</form>




<div class="container mt-4">
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">+ Create New</button>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Date Created</th>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $categories = getCategories($con); $i = 1; ?>
            <?php while ($row = $categories->fetch_assoc()) : ?>
            <tr>
                <td><?= $i++; ?></td>
                <td><?= $row['date_created']; ?></td>
                <td><?= $row['name']; ?></td>
                <td><?= $row['description']; ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $row['id']; ?>">
                        <input type="hidden" name="current_status" value="<?= $row['status']; ?>">
                        <button type="submit" name="toggle_status" class="btn btn-sm <?= $row['status'] == 'Active' ? 'btn-success' : 'btn-secondary'; ?>">
                            <?= $row['status']; ?>
                        </button>
                    </form>
                </td>
                <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id']; ?>">Edit</button>
                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['id']; ?>">Delete</button>
                </td>
            </tr>
            <!-- Edit Modal -->
            <div class="modal fade" id="editModal<?= $row['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Category</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" value="<?= $row['name']; ?>" required>
                                <label>Description</label>
                                <textarea name="description" class="form-control" required><?= $row['description']; ?></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="update_category" class="btn btn-success">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Delete Modal -->
            <div class="modal fade" id="deleteModal<?= $row['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title">Delete Category</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                <p>Are you sure you want to delete this category?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="delete_category" class="btn btn-danger">Delete</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                    <label>Description</label>
                    <textarea name="description" class="form-control" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_category" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
