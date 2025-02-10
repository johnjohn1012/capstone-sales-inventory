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

// Fetch categories with search and pagination
$sql = "SELECT * FROM categories";
if (!empty($search)) {
    $sql .= " WHERE name LIKE ? OR description LIKE ?";
}
$sql .= " ORDER BY date_created DESC LIMIT ?, ?";

$stmt = $con->prepare($sql);

if (!empty($search)) {
    $stmt->bind_param("ssii", $search_param, $search_param, $offset, $limit);
} else {
    $stmt->bind_param("ii", $offset, $limit);
}

$stmt->execute();
$categories = $stmt->get_result();

// Function to get categories (for external calls)
function getCategories($con, $search = '') {
    $sql = "SELECT * FROM categories";
    if (!empty($search)) {
        $sql .= " WHERE name LIKE ? OR description LIKE ?";
    }
    $sql .= " ORDER BY date_created DESC";

    $stmt = $con->prepare($sql);
    if (!empty($search)) {
        $search_param = "%$search%";
        $stmt->bind_param("ss", $search_param, $search_param);
    }
    $stmt->execute();
    return $stmt->get_result();
}

// Handle Category Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        if (!empty($name) && !empty($description)) {
            $status = 'Active';
            $sql = "INSERT INTO categories (name, description, status, date_created) VALUES (?, ?, ?, NOW())";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("sss", $name, $description, $status);
            if ($stmt->execute()) {
                echo "Category added successfully!";
            } else {
                error_log("SQL Error: " . $stmt->error);
                echo "Error adding category.";
            }
        } else {
            echo "Category name and description are required.";
        }
    }

    if (isset($_POST['update_category'])) {
        $id = intval($_POST['id']);
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        if (!empty($name) && !empty($description)) {
            $sql = "UPDATE categories SET name = ?, description = ? WHERE id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ssi", $name, $description, $id);
            if ($stmt->execute()) {
                echo "Category updated successfully!";
            } else {
                error_log("SQL Error: " . $stmt->error);
                echo "Error updating category.";
            }
        } else {
            echo "Category name and description cannot be empty.";
        }
    }

    if (isset($_POST['delete_category'])) {
        $id = intval($_POST['id']);
        $sql = "DELETE FROM categories WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "Category deleted successfully!";
        } else {
            error_log("SQL Error: " . $stmt->error);
            echo "Error deleting category.";
        }
    }

    if (isset($_POST['toggle_status'])) {
        $id = intval($_POST['id']);
        $current_status = $_POST['current_status'];
        $new_status = ($current_status === 'Active') ? 'Inactive' : 'Active';
        $sql = "UPDATE categories SET status = ? WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("si", $new_status, $id);
        if ($stmt->execute()) {
            echo "Status updated successfully!";
        } else {
            error_log("SQL Error: " . $stmt->error);
            echo "Error updating status.";
        }
    }
}
?>

<br>


        <h1>Category List</h1>

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
                    <button class="btn btn-primary" style="padding: 10px; width: 200px;" data-bs-toggle="modal" data-bs-target="#addModal">
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
