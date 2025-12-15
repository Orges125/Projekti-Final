<?php
session_start();
require "config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'user') {
    header("Location: login.php");
    exit();
}

// Rezervim i ri
$msg = "";
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
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Barber Shop - Home</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
    body {
        background: #1c1c1c;
        color: #fff;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .navbar-custom {
        background-color: #2c2c2c;
        padding: 10px 20px;
    }
    .navbar-custom .navbar-brand {
        font-weight: bold;
        color: #f0a500;
    }
    .navbar-custom .nav-link {
        color: #fff !important;
        margin-left: 15px;
    }
    .container-home {
        padding: 50px 15px;
    }
    .card-custom {
        background-color: #2a2a2a;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 0 15px rgba(0,0,0,0.5);
        margin-bottom: 40px;
        color: #fff;
    }
    input, select, option {
        background-color: #1c1c1c !important;
        color: #fff !important;
        border: 1px solid #f0a500 !important;
    }
    input::placeholder {
        color: rgba(255,255,255,0.8) !important;
    }
    button.btn-success {
        background-color: #f0a500;
        border: none;
    }
    button.btn-success:hover {
        background-color: #e09400;
    }
    .barber-card {
        background-color: #2c2c2c;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        text-align: center;
        transition: transform 0.3s;
        color: #fff;
    }
    .barber-card:hover {
        transform: scale(1.05);
    }
    .barber-card i {
        font-size: 40px;
        color: #f0a500;
        margin-bottom: 10px;
    }
    .section-title {
        color: #f0a500;
        margin-bottom: 30px;
        text-align: center;
    }
    .service-card {
        background-color: #2a2a2a;
        padding: 20px;
        border-radius: 15px;
        margin-bottom: 20px;
        text-align: center;
        transition: transform 0.3s;
        color: #fff;
    }
    .service-card:hover {
        transform: scale(1.05);
    }
    footer {
        background-color: #2c2c2c;
        padding: 30px 15px;
        text-align: center;
        margin-top: 50px;
        color: #fff;
    }

    /* Për të gjithë tekstin faqes */
    body, p, h1, h2, h3, h4, h5, h6, span, small, a, li {
        color: #fff !important;
    }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom">
  <div class="container">
    <a class="navbar-brand" href="#"><i class="fas fa-cut"></i> Barber Shop</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item"><span class="nav-link">Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</span></li>
        <li class="nav-item"><a href="logout.php" class="btn btn-danger ms-2">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container container-home">

    <!-- Booking Section -->
    <div class="card card-custom mx-auto" style="max-width: 800px;">
        <h3 class="mb-4 text-center"><i class="fas fa-calendar-check"></i> Book a Barber</h3>
        <?php if($msg): ?>
            <div class="alert alert-info text-center"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>
        <form method="POST" class="row g-3">
            <div class="col-md-6">
                <select name="barber_id" class="form-select" required>
                    <option value="">Select Barber</option>
                    <?php while($barber = $barbers->fetch_assoc()): ?>
                        <option value="<?php echo $barber['id']; ?>">
                            <?php echo htmlspecialchars($barber['name']); ?> (<?php echo $barber['experience']; ?> yrs)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6">
                <input type="datetime-local" name="booking_date" class="form-control" required>
            </div>
            <div class="col-12">
                <button type="submit" name="book" class="btn btn-success w-100"><i class="fas fa-check"></i> Book Now</button>
            </div>
        </form>
    </div>

    <!-- About Us Section -->
    <div class="card card-custom mx-auto">
        <h3 class="section-title">About Us</h3>
        <p class="text-center">Welcome to our Barber Shop! We provide professional haircuts and grooming services with experienced barbers who care about your style and confidence.</p>
    </div>

    <!-- Services Section -->
    <h3 class="section-title">Our Services</h3>
    <div class="row">
        <div class="col-md-4">
            <div class="service-card">
                <i class="fas fa-cut fa-2x"></i>
                <h5>Haircut</h5>
                <p>Precision haircuts tailored to your style.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="service-card">
                <i class="fas fa-beard fa-2x"></i>
                <h5>Beard Styling</h5>
                <p>Professional beard trims and grooming.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="service-card">
                <i class="fas fa-spa fa-2x"></i>
                <h5>Shaves & Treatments</h5>
                <p>Relaxing shaves and skin treatments.</p>
            </div>
        </div>
    </div>

    <!-- Barbers Section -->
    <h3 class="section-title mt-5">Meet Our Barbers</h3>
    <div class="row">
        <?php
        $barbers_list = $conn->query("SELECT * FROM barbers");
        while($barber = $barbers_list->fetch_assoc()):
        ?>
        <div class="col-md-4">
            <div class="barber-card">
                <i class="fas fa-user-tie"></i>
                <h5><?php echo htmlspecialchars($barber['name']); ?></h5>
                <p><?php echo $barber['experience']; ?> years experience</p>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <!-- Testimonials Section -->
    <h3 class="section-title mt-5">Testimonials</h3>
    <div class="row">
        <div class="col-md-6">
            <div class="card card-custom">
                <p>"Great service! My haircut was perfect and the barbers are very professional."</p>
                <small>- John Doe</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-custom">
                <p>"I love the atmosphere and the attention to detail. Highly recommended!"</p>
                <small>- Michael Smith</small>
            </div>
        </div>
    </div>

    <!-- Contact Section -->
    <h3 class="section-title mt-5">Contact Us</h3>
    <div class="card card-custom mx-auto" style="max-width: 600px; text-align:center;">
        <p><i class="fas fa-map-marker-alt"></i> 123 Main Street, City</p>
        <p><i class="fas fa-phone"></i> +1 234 567 890</p>
        <p><i class="fas fa-envelope"></i> info@barbershop.com</p>
    </div>
</div>

<!-- Footer -->
<footer>
    <p>&copy; <?php echo date("Y"); ?> Barber Shop. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
