<?php
// Database Connection
include "includes/connection.php";

// Fetch Orders Function
function getOrders($con) {
    $sql = "SELECT orders.id, orders.total_price, orders.date_created, order_items.product_name, order_items.quantity 
            FROM orders 
            JOIN order_items ON orders.id = order_items.order_id 
            ORDER BY orders.date_created DESC";
    $result = $con->query($sql);
    if (!$result) {
        die("Query failed: " . $con->error);
    }
    return $result;
}

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
?>

<div class="container mt-4">
    <h2>Order List</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php $orders = getOrders($con); ?>
            <?php while ($row = $orders->fetch_assoc()) : ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= $row['product_name']; ?></td>
                <td><?= $row['quantity']; ?></td>
                <td>₱<?= number_format($row['total_price'], 2); ?></td>
                <td><?= $row['date_created']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<style>
table { width: 100%; border-collapse: collapse; }
th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
th { background: #f4f4f4; }
</style>
