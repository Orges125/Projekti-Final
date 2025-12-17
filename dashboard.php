<?php
require "config.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'admin') {
    header("Location: login.php");
    exit();
}

/* ================= BARBER CRUD ================= */

// ADD BARBER
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $experience = $_POST['experience'];

    $stmt = $conn->prepare("INSERT INTO barbers (name, experience) VALUES (?, ?)");
    $stmt->bind_param("si", $name, $experience);
    $stmt->execute();

    header("Location: dashboard.php");
    exit();
}

// DELETE BARBER
if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM barbers WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: dashboard.php");
    exit();
}

/* ================= RESERVATIONS ================= */

// ACCEPT
if (isset($_POST['accept'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("UPDATE reservations SET status='accepted' WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: dashboard.php");
    exit();
}

// REJECT
if (isset($_POST['reject'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("UPDATE reservations SET status='rejected' WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: dashboard.php");
    exit();
}

/* ================= FETCH DATA ================= */

$barbers = $conn->query("SELECT * FROM barbers");

/*
  🔑 IMPORTANT JOIN:
  reservations.user_id → users.id
*/
$reservations = $conn->query("
    SELECT 
        r.id,
        r.reservation_datetime,
        r.status,
        u.username AS customer_name,
        b.name AS barber_name
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    JOIN barbers b ON r.barber_id = b.id
    ORDER BY r.reservation_datetime DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h3 class="mb-4">Welcome, <?= htmlspecialchars($_SESSION["username"]); ?> 👋</h3>

    <!-- ADD BARBER -->
    <div class="card p-3 mb-4">
        <h5>Add New Barber</h5>
        <form method="POST" class="row g-2">
            <div class="col-md-5">
                <input type="text" name="name" class="form-control" placeholder="Barber name" required>
            </div>
            <div class="col-md-3">
                <input type="number" name="experience" class="form-control" placeholder="Years exp." required>
            </div>
            <div class="col-md-4">
                <button name="add" class="btn btn-success w-100">Add Barber</button>
            </div>
        </form>
    </div>

    <!-- BARBERS LIST -->
    <div class="card p-3 mb-4">
        <h5>Barbers List</h5>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Experience</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $barbers->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td><?= $row['experience']; ?> yrs</td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                            <button name="delete"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Delete this barber?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- RESERVATIONS -->
    <div class="card p-3">
        <h5>Reservations</h5>
        <table class="table table-bordered align-middle">
            <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Barber</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $reservations->fetch_assoc()): ?>
                <?php
                    $dt = new DateTime($row['reservation_datetime']);
                ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['customer_name']); ?></td>
                    <td><?= htmlspecialchars($row['barber_name']); ?></td>
                    <td><?= $dt->format('Y-m-d'); ?></td>
                    <td><?= $dt->format('H:i'); ?></td>
                    <td><?= ucfirst($row['status']); ?></td>
                    <td>
                        <?php if ($row['status'] === 'pending'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                <button name="accept" class="btn btn-success btn-sm">Accept</button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                <button name="reject" class="btn btn-danger btn-sm">Reject</button>
                            </form>
                        <?php else: ?>
                            <em>No action</em>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
