<?php
// Database Connection
include "includes/connection.php";

// Fetch Orders Function with Product Names
function getOrders($con) {
    $sql = "SELECT 
                o.id AS order_id, 
                o.total_price, 
                o.date_created, 
                p.name AS product_name, 
                oi.quantity 
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN products p ON oi.product_id = p.id
            ORDER BY o.date_created DESC";
    
    $result = $con->query($sql);
    if (!$result) {
        die("Query failed: " . $con->error);
    }
    return $result;
}

// Fetch Orders
$orders = getOrders($con);
?>

<div class="container mt-4">
    <h2>Order List</h2>
    <table>
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
            <?php while ($row = $orders->fetch_assoc()) : ?>
            <tr>
                <td><?= htmlspecialchars($row['order_id']); ?></td>
                <td><?= htmlspecialchars($row['product_name']); ?></td>
                <td><?= htmlspecialchars($row['quantity']); ?></td>
                <td>₱<?= number_format($row['total_price'], 2); ?></td>
                <td><?= htmlspecialchars($row['date_created']); ?></td>
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
