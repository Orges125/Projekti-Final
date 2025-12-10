<?php
session_start();
require "config.php";

session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'user') {
    header("Location: login.php");
    exit();
}


// Rezervim i ri
if (isset($_POST['book'])) {
    $user_id = $_SESSION['user_id'];
    $barber_id = $_POST['barber_id'];
    $booking_date = $_POST['booking_date'];

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, barber_id, booking_date) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $barber_id, $booking_date);
    if ($stmt->execute()) {
        $msg = "Booking successful!";
    } else {
        $msg = "Error: " . $stmt->error;
    }
}

// Marrim listën e berbers
$barbers = $conn->query("SELECT * FROM barbers");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Barber Shop - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('https://images.unsplash.com/photo-1600180758895-1c37e5b2a4c2?auto=format&fit=crop&w=1950&q=80') no-repeat center center/cover;
            min-height: 100vh;
            color: white;
        }
        .overlay {
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            top: 0;
            left: 0;
        }
        .container-home {
            position: relative;
            z-index: 10;
            padding-top: 50px;
            padding-bottom: 50px;
        }
        .card-custom {
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(5px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        input, select {
            background: rgba(255,255,255,0.1);
            color: white;
            border: 1px solid white;
        }
    </style>
</head>
<body>
<div class="overlay"></div>

<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand">Barber Shop</span>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</nav>

<div class="container container-home">

    <h3>Welcome, <?php echo $_SESSION["username"]; ?>!</h3>

    <?php if(isset($msg)): ?>
        <div class="alert alert-success mt-3"><?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="card card-custom">
        <h5>Book a Barber</h5>
        <form method="POST" class="mt-3 row g-3">
            <div class="col-md-4">
                <select name="barber_id" class="form-select" required>
                    <option value="">Select Barber</option>
                    <?php while($barber = $barbers->fetch_assoc()): ?>
                        <option value="<?php echo $barber['id']; ?>"><?php echo $barber['name']; ?> (<?php echo $barber['experience']; ?> yrs)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4">
                <input type="datetime-local" name="booking_date" class="form-control" required>
            </div>
            <div class="col-md-4">
                <button type="submit" name="book" class="btn btn-success w-100">Book Now</button>
            </div>
        </form>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
