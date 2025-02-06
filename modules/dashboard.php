<style>
    /* General text styling for consistency */
    .card h5.card-title, 
    .card p.card-text {
        color: white; /* Text readability on gradients */
        font-weight: bold;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8); /* Black shadow for better visibility */
    }

    /* Card styling with gradient backgrounds */
    .card.categories {
        background: linear-gradient(135deg, rgb(180, 54, 79), rgb(148, 33, 104));
        color: white;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.2);
    }

    .card.menu {
        background: linear-gradient(135deg, #6A11CB, #2575FC);
        color: white;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.2);
    }

    .card.sales {
        background: linear-gradient(135deg, #1D976C, rgb(28, 114, 59));
        color: white;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.2);
    }

    .card.queued-orders {
        background: linear-gradient(135deg, #FF7E5F, rgb(180, 99, 37));
        color: white;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.2);
    }

    /* Hover effects for cards */
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease, opacity 0.3s ease;
        opacity: 0.95;
    }

    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        opacity: 1;
    }

    /* Pulse animation for cards */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .card.pulse {
        animation: pulse 2s infinite;
    }

    /* Welcome banner styling */
    .welcome-banner {
        background-image: url('assets/images/banner.jpg');
        background-size: cover;
        padding: 40px;
        color: white;
        text-align: center;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .welcome-banner h1 {
        text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        font-size: 2.5rem;
    }

    /* Custom scrollbar for low-stock table */
    .low-stock-section .card-body {
        max-height: 300px;
        overflow-y: auto;
    }

    .low-stock-section .card-body::-webkit-scrollbar {
        width: 8px;
    }

    .low-stock-section .card-body::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #6A11CB, #2575FC);
        border-radius: 10px;
    }

    .low-stock-section .card-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    /* Loading animation for low-stock products */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }


</style>
<br>
<br>
<br>
<div class="dashboard-container">
    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <h1>Welcome, <?php echo $username ?? 'Admin'; ?>!</h1>
    </div>

    <!-- Stats Overview -->
    <div class="stats-overview">
        <div class="row">
            <!-- Categories Card -->
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card text-center categories pulse">
                    <div class="card-body">
                        <i class="mdi mdi-folder-outline" style="font-size: 30px;"></i>
                        <h5 class="card-title">Categories</h5>
                        <p class="card-text"><?php echo $categories_count ?? 0; ?></p>
                    </div>
                </div>
            </div>

            <!-- Menu Card -->
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card text-center menu pulse">
                    <div class="card-body">
                        <i class="mdi mdi-food" style="font-size: 30px;"></i>
                        <h5 class="card-title">Menu</h5>
                        <p class="card-text"><?php echo $menu_count ?? 0; ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Sales Today Card -->
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card text-center sales pulse">
                    <div class="card-body">
                        <i class="mdi mdi-cash" style="font-size: 30px;"></i>
                        <h5 class="card-title">Total Sales Today</h5>
                        <p class="card-text">₱<?php echo number_format($sales_today ?? 0, 2); ?></p>
                    </div>
                </div>
            </div>

            <!-- Queued Orders Card -->
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card text-center queued-orders pulse">
                    <div class="card-body">
                        <i class="mdi mdi-cart" style="font-size: 30px;"></i>
                        <h5 class="card-title">Queued Orders</h5>
                        <p class="card-text"><?php echo $queued_orders ?? 0; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Products Section -->
    <div class="low-stock-section mt-4">
        <div class="card">
            <div class="card-header">
                <h5>Low Stock Products</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($low_stock_products)): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($low_stock_products as $product): ?>
                                <tr>
                                    <td><?php echo $product['name']; ?></td>
                                    <td>
                                        <span class="badge bg-danger">Stock: <?php echo $product['stock']; ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="text-center">No low-stock products.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>