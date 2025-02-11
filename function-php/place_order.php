<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include "../includes/connection.php";
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['orders']) || empty($data['orders'])) {
        echo json_encode(["success" => false, "error" => "No orders received"]);
        exit;
    }

    $totalPrice = 0;
    $orderedItems = [];

    // Calculate total price and prepare items
    foreach ($data['orders'] as $order) {
        $totalPrice += $order['price'] * $order['quantity'];
    }

    // Insert into orders table
    $insertOrder = $con->query("INSERT INTO orders (total_price, date_created) VALUES ('$totalPrice', NOW())");

    if (!$insertOrder) {
        echo json_encode(["success" => false, "error" => "Failed to create order"]);
        exit;
    }

    $orderId = $con->insert_id; // Get the newly inserted order ID

    // Process each order item
    foreach ($data['orders'] as $order) {
        $productName = $con->real_escape_string($order['name']);
        $quantity = (int) $order['quantity'];

        // Get Product ID & Current Stock
        $productQuery = $con->query("SELECT p.id, p.name, p.price, p.description, COALESCE(i.stock_quantity, 0) AS stock_quantity 
                                     FROM products p 
                                     LEFT JOIN inventory i ON p.id = i.product_id 
                                     WHERE p.name = '$productName'");

        if ($productQuery->num_rows === 0) {
            echo json_encode(["success" => false, "error" => "Product not found: $productName"]);
            exit;
        }

        $product = $productQuery->fetch_assoc();
        $productId = $product['id'];
        $currentStock = (int) $product['stock_quantity']; 

        if ($currentStock < $quantity) {
            echo json_encode(["success" => false, "error" => "Not enough stock for $productName (Available: $currentStock, Needed: $quantity)"]);
            exit;
        }

        // Update Inventory
        $newStock = $currentStock - $quantity;
        $updateStock = $con->query("UPDATE inventory SET stock_quantity = '$newStock', stock_out = stock_out + '$quantity' WHERE product_id = '$productId'");

        if (!$updateStock) {
            echo json_encode(["success" => false, "error" => "Failed to update stock"]);
            exit;
        }

        // Insert into order_items table
        $insertOrderItem = $con->query("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ('$orderId', '$productId', '$quantity', '{$order['price']}')");

        if (!$insertOrderItem) {
            echo json_encode(["success" => false, "error" => "Failed to insert order items"]);
            exit;
        }

        // Store order details to send back
        $orderedItems[] = [
            "name" => $product['name'],
            "description" => $product['description'],
            "price" => $product['price'],
            "quantity" => $quantity,
            "total" => $product['price'] * $quantity
        ];
    }

    // Send order details back to POS
    echo json_encode([
        "success" => true,
        "order_id" => $orderId,
        "total_price" => $totalPrice,
        "items" => $orderedItems
    ]);
}
?>
