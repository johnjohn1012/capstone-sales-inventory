<?php include('includes/connection.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" type="image/png" href="images/logos.png" />
    <title>Harah Rubina Del Dios </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"> <!-- Bootstrap CSS -->
    <!-- Add SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Add SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Custom CSS for Glowing Effects */
        body {
            background: url("images/image.png") no-repeat center center fixed;
        background-size: cover;

            font-family: Arial, sans-serif;
            color: white;
            overflow: hidden; /* To prevent scrollbars due to animation */
            height: 100vh; /* Ensures the background covers the full viewport height */
            animation: glowingBackground 5s infinite alternate;
        }


    

        .card {
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }

        .form-control {
            border-radius: 30px;
            box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s, transform 0.3s;
        }

        .form-control:focus {
            outline: none;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.8);
            transform: scale(1.02);
        }

        .btn {
            border-radius: 30px;
            transition: box-shadow 0.3s, background-color 0.3s;
        }

        .btn:hover {
            box-shadow: 0 0 15px rgba(0, 123, 255, 0.8);
            background-color: #0056b3;
        }

        h5 {
            text-align: center;
            font-weight: bold;
            color: #333;
        }

        #main-header h1 {
            text-align: center; /* Center the text */
            margin: 0 auto;     /* Center the container */
        }

        /* Add these new styles */
        .logo-circle {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.5);
        }

        .logo-circle img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }
        
        .captcha-container {
            margin-bottom: 15px;
            text-align: center;
        }
        
        .captcha-code {
            background-color: #f0f0f0;
            padding: 10px 20px;
            font-size: 24px;
            font-family: 'Courier New', monospace;
            letter-spacing: 5px;
            border-radius: 5px;
            color: #333;
            user-select: none;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        .refresh-captcha {
            color: #0d6efd;
            cursor: pointer;
            margin-left: 10px;
            text-decoration: none;
        }
        
        .refresh-captcha:hover {
            text-decoration: underline;
        }
    </style>
</head>


<body>

<?php 
session_start();

// Generate captcha code
function generateCaptcha($length = 6) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $captcha = '';
    for ($i = 0; $i < $length; $i++) {
        $captcha .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $captcha;
}

// If captcha is not set, generate it
if (!isset($_SESSION['captcha'])) {
    $_SESSION['captcha'] = generateCaptcha();
}

// Handle form submission
if (isset($_POST['submit'])) {
    $userCaptcha = isset($_POST['captcha']) ? strtoupper($_POST['captcha']) : '';
    
    if ($userCaptcha !== $_SESSION['captcha']) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Invalid Captcha',
                text: 'Please enter the correct captcha code',
                confirmButtonColor: '#d33'
            });
        </script>";
    } else {
        $email = $_POST['email'];
        $password = md5($_POST['password']);

        $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $email, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $userDetails = mysqli_fetch_assoc($result);

        if ($userDetails) {
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $userDetails['role'];
            $_SESSION['name'] = $userDetails['name'];
            
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Login Successful',
                    text: 'Welcome back, " . $userDetails['name'] . "!',
                    showConfirmButton: false,
                    timer: 1500
                }).then(function() {
                    window.location.href = '" . ($userDetails['role'] === 'admin' ? 'index_admin.php?page=dashboard' : 'dashboard.php') . "';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
                    text: 'Invalid Email or Password',
                    confirmButtonColor: '#d33'
                });
            </script>";
        }
    }
    // Generate new captcha after each submission
    $_SESSION['captcha'] = generateCaptcha();
}
?>


    <!-- Header -->
    <header id="main-header" class="bg-danger py-2 text-white">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1><i class="fa fa-user"></i>Harah Rubina Del Dios</h1>
                </div>
            </div>
        </div>
    </header>
    <br><br><br>    <br><br><br>
    <!-- Login Section -->
    <section id="post" class="py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-transparent border-0">
                            <div class="logo-circle">
                                <img src="images/logo.png" alt="Logo">
                            </div>
                            <h5 class="card-text text-center">Please Login Here</h5>
                          
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                    <label for="email">Email</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                    <label for="password">Password</label>
                                </div>
                                
                                <!-- Add captcha -->
                                <div class="captcha-container">
                                    <div class="captcha-code"><?php echo $_SESSION['captcha']; ?></div>
                                    <a href="javascript:void(0);" class="refresh-captcha" onclick="refreshCaptcha()">Refresh Captcha</a>
                                    <div class="form-floating mt-2">
                                        <input type="text" class="form-control" id="captcha" name="captcha" placeholder="Enter Captcha" required>
                                        <label for="captcha">Enter Captcha Code</label>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" name="submit" class="btn btn-primary">Login</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    

    <!-- Footer -->
  

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function refreshCaptcha() {
        window.location.reload();
    }
    </script>
</body>
</html>