<?php
session_start();
require "config.php";

// Inicializo variablën e gabimit
$error = "";

// Procesi i login
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Merr përdoruesin nga databaza
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Login i suksesshëm
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = strtolower(trim($user['role']));

        // Redirect bazuar në rol
        if ($_SESSION['role'] === 'admin') {
            header("Location: dashboard.php");
            exit();
        } else {
            header("Location: home.php");
            exit();
        }
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Barber Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-dark">

<div class="container d-flex justify-content-center align-items-center" style="height:100vh;">
    <div class="card p-4 shadow" style="width:400px; background-color:#2c2c2c; color:#fff;">
        <h3 class="text-center mb-3"><i class="fas fa-cut"></i> Login</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
            <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
            <button type="submit" name="login" class="btn btn-warning w-100"><i class="fas fa-right-to-bracket"></i> Login</button>
        </form>

        <a href="signup.php" class="d-block text-center mt-3 text-white">Create account</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
