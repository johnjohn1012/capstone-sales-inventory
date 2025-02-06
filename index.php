<?php include('includes/connection.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" type="image/png" href="images/logo.png" />
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"> <!-- Bootstrap CSS -->
    <style>
        /* Custom CSS for Glowing Effects */
        body {
            background: url("https://th.bing.com/th/id/R.e9ea962e02082e7e2c7f3815a71a8364?rik=j34ZRvwSEhWP6A&riu=http%3a%2f%2fwww.pixelstalk.net%2fwp-content%2fuploads%2f2016%2f07%2fFree-4k-Backgrounds-Screen-Download.jpg&ehk=z%2f7Q4nlcVzNOT4%2f8vtJUjBFs3p%2fzurkacBEpNuTsIqM%3d&risl=&pid=ImgRaw&r=0") no-repeat center center fixed;
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
    </style>
</head>
<body>

<?php 
if (isset($_POST['submit'])) {
    $user = $_POST['email'];
    $password = md5($_POST['password']); // Encrypt password

    $sql = "SELECT * FROM users WHERE email = '$user' AND password = '$password'";
    $run = mysqli_query($con, $sql);
    $check = mysqli_num_rows($run);

    if ($check == 1) {
        session_start();
        $_SESSION['email'] = $user;
        echo "<script>window.open('index_admin.php?page=dashboard', '_self');</script>";
    } else {
        echo "<script>alert('Invalid Email or Password'); window.open('index.php', '_self');</script>";
    }
}
?>


    <!-- Header -->
    <header id="main-header" class="bg-danger py-2 text-white">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center"> <!-- Center content -->
                    <h1><i class="fa fa-user"></i> Admin Login</h1>
                </div>
            </div>
        </div>
    </header>

    <!-- Login Section -->
    <section id="post" class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="card p-4">
                        <div class="card-header bg-light">
                            <h5>Login Panel</h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="Enter your password" required />
                                </div>
                                <button type="submit" name="submit" class="btn btn-success w-100">Log In</button>
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
    
</body>
</html>