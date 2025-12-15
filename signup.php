<?php
require "config.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($username == "" || $email == "" || $password == "") {
        $error = "Mos i len fushat bosh!";
    } else {

        $check = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Username ose Email ekziston!";
        } else {

            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $role = "user";

            $stmt = $conn->prepare(
                "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("ssss", $username, $email, $hashed, $role);
            $stmt->execute();

            $success = "Account u krijua! Shko te Login.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="height:100vh;">
<div class="card p-4 shadow" style="width:400px;">

<h3 class="text-center mb-3">Sign Up</h3>

<?php if($error): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<?php if($success): ?>
<div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<form method="POST">
    <input type="text" name="username" class="form-control mb-3" placeholder="Username">
    <input type="email" name="email" class="form-control mb-3" placeholder="Email">
    <input type="password" name="password" class="form-control mb-3" placeholder="Password">
    <button class="btn btn-success w-100">Create Account</button>
</form>

<a href="login.php" class="d-block text-center mt-3">Login</a>

</div>
</div>

</body>
</html>
