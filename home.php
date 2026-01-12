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

    // Fut rezervimin me status pending
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, barber_id, booking_date, status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("iis", $user_id, $barber_id, $booking_date);
    if ($stmt->execute()) {
        $msg = "Booking successful!";
    } else {
        $msg = "Error: " . $stmt->error;
    }
}

// Marrim listën e berbers
$barbers = $conn->query("SELECT * FROM barbers");

// Marrim rezervimet e përdoruesit
$reservations = $conn->query("
    SELECT b.id, b.booking_date, b.status, br.name AS barber_name
    FROM bookings b
    JOIN barbers br ON b.barber_id = br.id
    WHERE b.user_id = " . intval($_SESSION['user_id']) . "
    ORDER BY b.booking_date DESC
");

// Anulo rezervim
if (isset($_POST['cancel'])) {
    $booking_id = $_POST['booking_id'];
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
    $stmt->execute();
    header("Location: home.php");
    exit();
}

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
body { background: #1c1c1c; color: #fff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.navbar-custom { background-color: #2c2c2c; padding: 10px 20px; }
.navbar-custom .navbar-brand { font-weight: bold; color: #f0a500; }
.navbar-custom .nav-link { color: #fff !important; margin-left: 15px; }
.container-home { padding: 50px 15px; }
.card-custom { background-color: #2a2a2a; border-radius: 15px; padding: 30px; box-shadow: 0 0 15px rgba(0,0,0,0.5); margin-bottom: 40px; color: #fff; }
input, select, option { background-color: #1c1c1c !important; color: #fff !important; border: 1px solid #f0a500 !important; }
input::placeholder { color: rgba(255,255,255,0.8) !important; }
button.btn-success { background-color: #f0a500; border: none; }
button.btn-success:hover { background-color: #e09400; }
.table td, .table th { color: #fff !important; }
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

    <!-- Rezervimet e mia -->
    <div class="card card-custom mx-auto" style="max-width: 900px;">
        <h3 class="mb-4 text-center"><i class="fas fa-list"></i> My Reservations</h3>
        <table class="table table-bordered text-white">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Barber</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $reservations->fetch_assoc()): ?>
                    <?php $dt = new DateTime($row['booking_date']); ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['barber_name']); ?></td>
                        <td><?= $dt->format('Y-m-d H:i'); ?></td>
                        <td><?= ucfirst($row['status']); ?></td>
                        <td>
                            <?php if($row['status'] === 'pending'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="booking_id" value="<?= $row['id']; ?>">
                                    <button type="submit" name="cancel" class="btn btn-danger btn-sm">Cancel</button>
                                </form>
                            <?php else: ?>
                                <em>N/A</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<footer class="text-center mt-5 mb-3 text-white">
    &copy; <?php echo date("Y"); ?> Barber Shop. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
