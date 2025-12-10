<?php
session_start();
require "config.php";

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1){
        $user = $result->fetch_assoc();

        // verify hashed password
        if(password_verify($password, $user['password'])){
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // redirect bazuar në role
            if($user['role'] === 'admin'){
                header("Location: dashboard.php");
            } else {
                header("Location: home.php");
            }
            exit();
        } else {
            $error = "Password incorrect!";
        }
    } else {
        $error = "User not found!";
    }
}


?>



<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: url('images/barber.jpg') no-repeat center center/cover;
            height: 100vh;
            position: relative;
        }

        /* Dark transparent layer */
        .overlay {
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(3px);
        }

        .login-card {
            position: relative;
            z-index: 10;
            width: 380px;
            border-radius: 15px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(12px);
            padding: 30px;
            color: white;
            box-shadow: 0px 5px 25px rgba(0,0,0,0.4);
        }

        .form-control {
            border-radius: 10px;
            background: rgba(255,255,255,0.3);
            border: none;
            color: white;
        }

        .form-control::placeholder {
            color: #eee;
        }

        .btn-custom {
            background: #0d6efd;
            border-radius: 10px;
        }
    </style>
</head>

<body>

<div class="overlay"></div>

<div class="container d-flex justify-content-center align-items-center" style="height:100vh;">

    <div class="login-card">

        <h3 class="text-center mb-3">
            <i class="bi bi-person-circle"></i> Login
        </h3>

        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <form method="POST">

            <div class="mb-3">
                <label><i class="bi bi-person"></i> Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter username" required>
            </div>

            <div class="mb-3">
                <label><i class="bi bi-lock"></i> Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
            </div>

            <button class="btn btn-custom w-100 py-2">Login</button>
        </form>

        <p class="text-center mt-3">
            <a href="signup.php" class="text-light text-decoration-none">Create an account</a>
        </p>

    </div>
</div>

</body>
</html>
