<?php
include 'includes/connection.php';

session_start(); // Start session

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in first'); window.location.href='includes/index.php';</script>";
    exit();
}

// Fetch logged-in user's details
$email = $_SESSION['email'];
$query = "SELECT * FROM users WHERE email = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    // Handle the case where the user is not found
    echo "<script>alert('User not found. Please log in again.'); window.location.href='includes/logout.php';</script>";
    exit();
}

?>


<?php
include 'includes/header.php';
?>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <div>


        <div class="brand-logo d-flex align-items-center justify-content-center" data-simpler style="background: linear-gradient(135deg,rgb(246, 249, 251),rgb(191, 214, 235)); color: white;">

          <a href="index_admin.php?page=dashboard" class="text-nowrap logo-img">
            <img src="images/logos.png" width="120" height="100" style="display: block; margin: 0 auto;" alt="Logo" />
          </a>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
          </div>
        </div>



        <!-- Sidebar navigation-->
<nav class="sidebar-nav scroll-sidebar" data-simplebar style="background: linear-gradient(135deg,rgb(235, 241, 247),rgb(206, 228, 249)); color: white;">

  <ul id="sidebarnav">
    <li class="nav-small-cap">
      <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
      <span class="hide-menu">Home</span>
    </li>

    <li class="sidebar-item">
      <a class="sidebar-link" href="index_admin.php?page=dashboard">
        <i class="ti ti-layout-dashboard"></i>
        <span class="hide-menu">Dashboard</span>
      </a>
    </li>

    <li class="sidebar-item">
      <a class="sidebar-link" href="index_admin.php?page=pos">
        <i class="ti ti-cash"></i>
        <span class="hide-menu">POS</span>
      </a>
    </li>

    <li class="sidebar-item">
      <a class="sidebar-link" href="index_admin.php?page=order_list">
        <i class="ti ti-list-check"></i>
        <span class="hide-menu">Order List</span>
      </a>
    </li>

    <li class="nav-small-cap">
      <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
      <span class="hide-menu">Master List</span>
    </li>

    <li class="sidebar-item">
      <a class="sidebar-link" href="index_admin.php?page=category_list">
        <i class="ti ti-tags"></i>
        <span class="hide-menu">Category List</span>
      </a>
    </li>

    <li class="sidebar-item">
      <a class="sidebar-link" href="index_admin.php?page=inventory_list">
        <i class="ti ti-package"></i>
        <span class="hide-menu">Inventory List</span>
      </a>
    </li>

    <li class="sidebar-item">
      <a class="sidebar-link" href="index_admin.php?page=product_list">
        <i class="ti ti-box"></i>
        <span class="hide-menu">Product List</span>
      </a>
    </li>

    <li class="nav-small-cap">
      <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
      <span class="hide-menu">Reports</span>
    </li>

    <li class="sidebar-item">
      <a class="sidebar-link" href="index_admin.php?page=sales_report">
        <i class="ti ti-chart-bar"></i>
        <span class="hide-menu">Sales Report</span>
      </a>
    </li>

    <li class="sidebar-item">
      <a class="sidebar-link" href="index_admin.php?page=analytics_report">
        <i class="ti ti-chart-pie"></i>
        <span class="hide-menu">Analytics Report</span>
      </a>
    </li>

    <li class="sidebar-item">
      <a class="sidebar-link" href="index_admin.php?page=historic_reports">
        <i class="ti ti-history"></i>
        <span class="hide-menu">Historic Reports</span>
      </a>
    </li>

    <li class="nav-small-cap">
      <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
      <span class="hide-menu">Maintenance</span>
    </li>

    <li class="sidebar-item">
      <a class="sidebar-link" href="index_admin.php?page=user_list">
        <i class="ti ti-users"></i>
        <span class="hide-menu">User List</span>
      </a>
    </li>

    <br>


    <li class="sidebar-item">
    <a class="sidebar-link" style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 2px; 
        color: black; font-family: Arial, sans-serif; font-size: 18px; font-weight: bold; 
        text-shadow: 1px 1px 3px white, 0 0 5px white;">
        
        <span id="clock"></span>
        <span id="date"></span>
      
    </a>
    </li>

    





  </ul>





</nav>
<!-- End Sidebar navigation -->



        <!-- End Sidebar navigation -->


        
      </div>
    </aside>
    <!-- Sidebar End -->

    <div class="body-wrapper">
      <?php include 'includes/nav.php'; ?>
      <div class="container-fluid">
        <!-- Content Area -->
        <?php
          $allowed_pages = [
            'dashboard' => 'modules/dashboard.php',
            'pos' => 'modules/pos.php',
            'order_list' => 'modules/order_list.php',
            'category_list' => 'modules/category_list.php',
            'inventory_list' => 'modules/inventory_list.php',
            'product_list' => 'modules/product_list.php',
            'sales_report' => 'modules/sales_report.php',
            'analytics_report' => 'modules/analytics_report.php',
            'historic_reports' => 'modules/historic_reports.php',
            'user_list' => 'modules/user_list.php',
            'system_information' => 'modules/system_information.php',
          ];

          $page = $_GET['page'] ?? 'dashboard';
          if (array_key_exists($page, $allowed_pages)) {
            include $allowed_pages[$page];
          } else {
            echo "<h4>Page Not Found</h4>";
          }
        ?>
      </div>
      <?php include 'includes/footer.php'; ?>
    </div>

  </div>

  <script src="assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/sidebarmenu.js"></script>
  <script src="assets/js/app.min.js"></script>
  <script src="assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="assets/js/dashboard.js"></script>
  <script>
          function updateDateTime() {
              let now = new Date().toLocaleString("en-US", { timeZone: "Asia/Manila" });
              let time = new Date(now);

              // Format time
              let hours = time.getHours();
              let minutes = time.getMinutes();
              let seconds = time.getSeconds();
              let ampm = hours >= 12 ? "PM" : "AM";

              hours = hours % 12 || 12; // Convert to 12-hour format
              minutes = minutes.toString().padStart(2, "0");
              seconds = seconds.toString().padStart(2, "0");

              let formattedTime = `${hours}:${minutes}:${seconds} ${ampm}`;

              // Format date
              let options = { year: "numeric", month: "long", day: "numeric", weekday: "long" };
              let formattedDate = time.toLocaleDateString("en-US", options);

              // Update HTML
              document.getElementById("clock").innerText = formattedTime;
              document.getElementById("date").innerText = formattedDate;
          }

          setInterval(updateDateTime, 1000);
          updateDateTime(); // Initial call to prevent delay
      </script>
  
<body>

<style>
body{

font-family: ;
font-size: 17px;
}
</style>

