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
    <h2>Point of Sale (POS) System</h2>

    <div class="categories">
        <?php $categories = getCategories($con); ?>
        <button class="category-btn active" onclick="filterCategory('All')">All</button>
        <?php while ($cat = $categories->fetch_assoc()) : ?>
            <button class="category-btn" onclick="filterCategory('<?= $cat['name']; ?>')"><?= $cat['name']; ?></button>
        <?php endwhile; ?>
    </div>
    
    <div class="menu" id="menuContainer">
        <?php $products = getProducts($con); ?>
        <?php while ($row = $products->fetch_assoc()) : ?>
        <div class="menu-item" data-category="<?= $row['category_name']; ?>" onclick="addToOrder('<?= $row['name']; ?>', <?= $row['price']; ?>)">
            <p><?= $row['name']; ?> - ₱<?= $row['price']; ?></p>
            <p>Stock: <?= $row['stock_quantity']; ?></p>
        </div>
        <?php endwhile; ?>
    </div>
    
    <div class="orders">
        <h3>Order Summary</h3>
        <table>
            <thead>
                <tr>
                    <th>QTY</th>
                    <th>Menu</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody id="orderList"></tbody>
        </table>
        <div>
            <p>Grand Total: <span id="grandTotal">0.00</span></p>
            <button onclick="placeOrder()">Place Order</button>
        </div>
    </div>
</div>

<script>
let orders = [];
let grandTotal = 0;

function addToOrder(itemName, itemPrice) {
    const orderIndex = orders.findIndex(order => order.name === itemName);
    if (orderIndex > -1) {
        orders[orderIndex].quantity++;
    } else {
        orders.push({ name: itemName, price: itemPrice, quantity: 1 });
    }
    updateOrderList();
}

function updateOrderList() {
    const orderList = document.getElementById('orderList');
    orderList.innerHTML = '';
    grandTotal = 0;
    orders.forEach(order => {
        grandTotal += order.price * order.quantity;
        const row = `<tr><td>${order.quantity}</td><td>${order.name}</td><td>${(order.price * order.quantity).toFixed(2)}</td></tr>`;
        orderList.innerHTML += row;
    });
    document.getElementById('grandTotal').textContent = grandTotal.toFixed(2);
}

function placeOrder() {
    if (orders.length === 0) {
        alert('No items in the order!');
        return;
    }

    fetch('./function-php/place_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ orders: orders })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Order placed successfully!');
            orders = [];
            updateOrderList();
            fetchOrders(); // Refresh the order list
        } else {
            alert('Error placing order: ' + data.error);
        }
    });
}


function filterCategory(category) {
    const items = document.querySelectorAll('.menu-item');
    items.forEach(item => {
        if (category === 'All' || item.dataset.category === category) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>

    


<style>
.categories { display: flex; gap: 10px; }
.menu { display: flex; flex-wrap: wrap; gap: 10px; }
.menu-item { border: 1px solid #ddd; padding: 10px; cursor: pointer; }
.orders { margin-top: 20px; }
</style>