<?php
require "config.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'admin') {
    header("Location: login.php");
    exit();
}

$edit_id = isset($_POST['edit']) ? $_POST['id'] : null;

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

// UPDATE BARBER (SAVE)
if (isset($_POST['save'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $experience = $_POST['experience'];

    $stmt = $conn->prepare("UPDATE barbers SET name=?, experience=? WHERE id=?");
    $stmt->bind_param("sii", $name, $experience, $id);
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

// ACCEPT RESERVATION
if (isset($_POST['accept'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("UPDATE bookings SET status='accepted' WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit();
}

// REJECT RESERVATION
if (isset($_POST['reject'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("UPDATE bookings SET status='rejected' WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit();
}

/* ================= FETCH DATA ================= */

// Barbers list
$barbers = $conn->query("SELECT * FROM barbers");

// Reservations list
$reservations = $conn->query("
    SELECT 
        b.id,
        b.booking_date,
        b.status,
        u.username AS customer_name,
        br.name AS barber_name
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN barbers br ON b.barber_id = br.id
    ORDER BY b.booking_date DESC
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
    <h3 class="mb-4">Admin Dashboard</h3>

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
        <h5>Barbers</h5>
        <table class="table table-bordered align-middle">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Experience</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $barbers->fetch_assoc()): ?>
                <tr>
                    <form method="POST">
                        <td><?= $row['id']; ?></td>
                        <td>
                            <?php if ($edit_id == $row['id']): ?>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name']); ?>" required>
                            <?php else: ?>
                                <?= htmlspecialchars($row['name']); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($edit_id == $row['id']): ?>
                                <input type="number" name="experience" class="form-control" value="<?= $row['experience']; ?>" required>
                            <?php else: ?>
                                <?= $row['experience']; ?> yrs
                            <?php endif; ?>
                        </td>
                        <td class="d-flex gap-2">
                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                            <?php if ($edit_id == $row['id']): ?>
                                <button name="save" class="btn btn-success btn-sm">Save</button>
                                <a href="dashboard.php" class="btn btn-secondary btn-sm">Cancel</a>
                            <?php else: ?>
                                <button name="edit" class="btn btn-primary btn-sm">Edit</button>
                                <button name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Delete this barber?')">Delete</button>
                            <?php endif; ?>
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- RESERVATIONS -->
    <div class="card p-3 mb-4">
        <h5>Reservations</h5>
        <table class="table table-bordered align-middle">
            <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Barber</th>
                <th>Date & Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $reservations->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['customer_name']); ?></td>
                    <td><?= htmlspecialchars($row['barber_name']); ?></td>
                    <td><?= date("Y-m-d H:i", strtotime($row['booking_date'])); ?></td>
                    <td><?= ucfirst($row['status']); ?></td>
                    <td class="d-flex gap-2">
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
