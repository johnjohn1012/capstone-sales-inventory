<?php
include "includes/connection.php";

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['orders']) && count($data['orders']) > 0) {
    $total_amount = 0;

    // Insert Order
    $sql = "INSERT INTO orders (total_amount, order_date) VALUES (0, NOW())";
    if (!$con->query($sql)) {
        echo json_encode(["success" => false, "error" => $con->error]);
        exit;
    }

    $order_id = $con->insert_id;

    foreach ($data['orders'] as $order) {
        $product_name = $order['name'];
        $quantity = $order['quantity'];
        $price = $order['price'];
        $total = $price * $quantity;

        $total_amount += $total;

        // Fetch product ID
        $product_res = $con->query("SELECT id FROM products WHERE name = '$product_name'");
        $product = $product_res->fetch_assoc();
        $product_id = $product['id'];

        // Insert order item
        $sql_item = "INSERT INTO order_items (order_id, product_id, quantity, price, total) 
                     VALUES ('$order_id', '$product_id', '$quantity', '$price', '$total')";
        if (!$con->query($sql_item)) {
            echo json_encode(["success" => false, "error" => $con->error]);
            exit;
        }

        // Update inventory stock
        $con->query("UPDATE inventory SET stock_quantity = stock_quantity - $quantity WHERE product_id = '$product_id'");
    }

    // Update total amount in orders table
    $con->query("UPDATE orders SET total_amount = '$total_amount' WHERE id = '$order_id'");

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Invalid order data"]);
}
?>
