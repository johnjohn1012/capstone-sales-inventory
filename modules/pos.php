<div class="pos-container">
  <!-- Header -->
  <header class="pos-header">
    <h1>Order</h1>
  </header>

  <!-- Categories -->
  <div class="categories">
    <button class="category-btn active">All Day Meals</button>
    <button class="category-btn">Beverages</button>
    <button class="category-btn">Burgers</button>
    <button class="category-btn">Chicken Selection</button>
  </div>

  <!-- Menu -->
  <div class="menu">
    <div class="menu-item" onclick="addToOrder('Big Breakfast 1', 275)">
      <img src="breakfast1.jpg" alt="Big Breakfast 1">
      <p>0102 - Big Breakfast 1</p>
    </div>
    <div class="menu-item" onclick="addToOrder('Big Breakfast 2', 195)">
      <img src="breakfast2.jpg" alt="Big Breakfast 2">
      <p>0103 - Big Breakfast 2</p>
    </div>
    <div class="menu-item" onclick="addToOrder('Big Breakfast 1', 275)">
      <img src="breakfast1.jpg" alt="Big Breakfast 1">
      <p>0102 - Big Breakfast 1</p>
    </div>
    <div class="menu-item" onclick="addToOrder('Big Breakfast 2', 195)">
      <img src="breakfast2.jpg" alt="Big Breakfast 2">
      <p>0103 - Big Breakfast 2</p>
    </div>
    <div class="menu-item" onclick="addToOrder('Big Breakfast 1', 275)">
      <img src="breakfast1.jpg" alt="Big Breakfast 1">
      <p>0102 - Big Breakfast 1</p>
    </div>
    <div class="menu-item" onclick="addToOrder('Big Breakfast 2', 195)">
      <img src="breakfast2.jpg" alt="Big Breakfast 2">
      <p>0103 - Big Breakfast 2</p>
    </div>
    <div class="menu-item" onclick="addToOrder('Big Breakfast 1', 275)">
      <img src="breakfast1.jpg" alt="Big Breakfast 1">
      <p>0102 - Big Breakfast 1</p>
    </div>
    <div class="menu-item" onclick="addToOrder('Big Breakfast 2', 195)">
      <img src="breakfast2.jpg" alt="Big Breakfast 2">
      <p>0103 - Big Breakfast 2</p>
    </div>
    <!-- Add more menu items -->
  </div>

  <!-- Orders -->
  <div class="orders">
    <table>
      <thead>
        <tr>
          <th>QTY</th>
          <th>Menu</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody id="orderList">
        <!-- Orders will be added dynamically -->
      </tbody>
    </table>
  </div>

  <!-- Summary -->
  <div class="summary">
    <div class="summary-row">
      <label>Grand Total</label>
      <span id="grandTotal">0.00</span>
    </div>
    <div class="summary-row">
      <label>Tendered</label>
      <input type="number" id="tendered" oninput="calculateChange()">
    </div>
    <div class="summary-row">
      <label>Change</label>
      <span id="change">0.00</span>
    </div>
    <div class="summary-row">
      <label>Order Type</label>
      <select id="orderType">
        <option value="Dine-in">Dine-in</option>
        <option value="Takeaway">Takeaway</option>
      </select>
    </div>
    <button class="place-order-btn" onclick="placeOrder()">Place Order</button>
  </div>
</div>


<!-- Modal -->
<div id="orderModal" class="modal">
  <div class="modal-content">
    <h2>Confirm Your Order</h2>
    <table class="modal-table">
      <thead>
        <tr>
          <th>Quantity</th>
          <th>Menu Item</th>
          <th>Price</th>
        </tr>
      </thead>
      <tbody id="modalOrderList">
        <!-- Modal order list will be added dynamically -->
      </tbody>
    </table>
    <div class="modal-summary">
      <p>Grand Total: <span id="modalGrandTotal">0.00</span></p>
      <p>Tendered: <span id="modalTendered">0.00</span></p>
      <p>Change: <span id="modalChange">0.00</span></p>
    </div>
    <div class="modal-actions">
      <button onclick="closeModal()">Edit Order</button>
      <button onclick="confirmOrder()">Confirm and Print</button>
    </div>
  </div>
</div>



<style>
.pos-container {
  font-family: Arial, sans-serif;
  border: 1px solid #ddd;
}

.pos-header {
  background-color: #4A148C;
  color: white;
  padding: 10px;
  text-align: center;
}

.categories {
  display: flex;
  justify-content: space-around;
  margin: 10px 0;
}

.category-btn {
  background-color: #FFC107;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
}

.category-btn.active {
  background-color: #FF8F00;
}

.menu {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  padding: 10px;
}

.menu-item {
  border: 1px solid #ddd;
  border-radius: 5px;
  padding: 10px;
  text-align: center;
  cursor: pointer;
  width: 150px;
}

.menu-item img {
  width: 100%;
  border-radius: 5px;
}

.orders {
  margin-top: 20px;
}

.orders table {
  width: 100%;
  border-collapse: collapse;
}

.orders th,
.orders td {
  border: 1px solid #ddd;
  text-align: left;
  padding: 8px;
}

.summary {
  margin-top: 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
}

.place-order-btn {
  background-color: #E91E63;
  color: white;
  border: none;
  padding: 10px;
  border-radius: 5px;
  cursor: pointer;

}

.modal {
  display: none; /* Hidden by default */
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  justify-content: center;
  align-items: center;
}

.modal-content {
  background-color: white;
  padding: 20px;
  border-radius: 5px;
  width: 50%;
  text-align: center;
}

.modal-table {
  width: 100%;
  border-collapse: collapse;
  margin: 20px 0;
}

.modal-table th,
.modal-table td {
  border: 1px solid #ddd;
  padding: 8px;
}

.modal-summary {
  margin: 20px 0;
}

.modal-actions button {
  margin: 10px;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.modal-actions button:first-child {
  background-color: #ccc;
}

.modal-actions button:last-child {
  background-color: #007bff;
  color: white;
}
#orderModal {
  display: none;
}

</style>


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
  if (!orderList) {
    console.error("Error: orderList element not found.");
    return;
  }
  orderList.innerHTML = '';
  grandTotal = 0;

  orders.forEach(order => {
    grandTotal += order.price * order.quantity;

    const row = `
      <tr>
        <td>
          <button onclick="changeQuantity('${order.name}', -1)">-</button>
          ${order.quantity}
          <button onclick="changeQuantity('${order.name}', 1)">+</button>
        </td>
        <td>${order.name}</td>
        <td>${(order.price * order.quantity).toFixed(2)}</td>
      </tr>
    `;
    orderList.innerHTML += row;
  });

  document.getElementById('grandTotal').textContent = grandTotal.toFixed(2);
}

function changeQuantity(itemName, change) {
  const orderIndex = orders.findIndex(order => order.name === itemName);

  if (orderIndex > -1) {
    orders[orderIndex].quantity += change;

    if (orders[orderIndex].quantity <= 0) {
      orders.splice(orderIndex, 1);
    }

    updateOrderList();
  }
}

function calculateChange() {
  const tendered = parseFloat(document.getElementById('tendered').value) || 0;
  const change = tendered - grandTotal;

  document.getElementById('change').textContent = change.toFixed(2);
}

function placeOrder() {
  openModal(); // Trigger the modal when "Place Order" is clicked
}

function openModal() {
  const modal = document.getElementById('orderModal');
  if (!modal) {
    console.error("Error: orderModal element not found.");
    return;
  }

  // Populate modal table with order details
  const modalOrderList = document.getElementById('modalOrderList');
  if (!modalOrderList) {
    console.error("Error: modalOrderList element not found.");
    return;
  }
  modalOrderList.innerHTML = '';
  orders.forEach(order => {
    const row = `
      <tr>
        <td>${order.quantity}</td>
        <td>${order.name}</td>
        <td>${(order.price * order.quantity).toFixed(2)}</td>
      </tr>
    `;
    modalOrderList.innerHTML += row;
  });

  // Populate modal totals
  document.getElementById('modalGrandTotal').textContent = grandTotal.toFixed(2);
  const tendered = parseFloat(document.getElementById('tendered').value) || 0;
  document.getElementById('modalTendered').textContent = tendered.toFixed(2);
  document.getElementById('modalChange').textContent = (tendered - grandTotal).toFixed(2);

  // Show modal
  modal.style.display = 'flex';
}

function closeModal() {
  const modal = document.getElementById('orderModal');
  if (!modal) {
    console.error("Error: orderModal element not found.");
    return;
  }
  modal.style.display = 'none';
}

function confirmOrder() {
  alert('Order confirmed and sent to print!');
  closeModal();
}

</script>