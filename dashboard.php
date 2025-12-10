<?php
session_start();
require "config.php";

session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'admin') {
    header("Location: login.php");
    exit();
}



// ADD Barber
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $experience = $_POST['experience'];
    $stmt = $conn->prepare("INSERT INTO barbers (name, experience) VALUES (?, ?)");
    $stmt->bind_param("si", $name, $experience);
    $stmt->execute();
    header("Location: dashboard.php");
    exit();
}

// UPDATE Barber nga Modal
if (isset($_POST['update_modal'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $experience = $_POST['experience'];

    $stmt = $conn->prepare("UPDATE barbers SET name=?, experience=? WHERE id=?");
    $stmt->bind_param("sii", $name, $experience, $id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit();
}

// DELETE Barber
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM barbers WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit();
}

// Fetch all barbers
$barbers = $conn->query("SELECT * FROM barbers");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h3>Welcome, <?php echo $_SESSION["username"]; ?>!</h3>

    <!-- Add Barber Form -->
    <div class="card p-3 mb-4">
        <h5>Add New Barber</h5>
        <form method="POST" class="row g-2">
            <div class="col-md-5">
                <input type="text" name="name" class="form-control" placeholder="Barber Name" required>
            </div>
            <div class="col-md-3">
                <input type="number" name="experience" class="form-control" placeholder="Experience (years)" required>
            </div>
            <div class="col-md-4">
                <button type="submit" name="add" class="btn btn-success w-100">Add Barber</button>
            </div>
        </form>
    </div>

    <!-- Barbers List -->
    <div class="card p-3">
        <h5>Barbers List</h5>
        <table class="table table-bordered mt-2">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Experience</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $barbers->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['experience']; ?></td>
                    <td>
                        <!-- Update Modal Button -->
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $row['id']; ?>">Update</button>

                        <!-- Delete Form -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this barber?')">Delete</button>
                        </form>

                        <!-- Update Modal -->
                        <div class="modal fade" id="updateModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="updateModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <form method="POST">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="updateModalLabel<?php echo $row['id']; ?>">Update Barber</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body">
                                      <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                      <div class="mb-3">
                                          <label>Name</label>
                                          <input type="text" name="name" class="form-control" value="<?php echo $row['name']; ?>" required>
                                      </div>
                                      <div class="mb-3">
                                          <label>Experience</label>
                                          <input type="number" name="experience" class="form-control" value="<?php echo $row['experience']; ?>" required>
                                      </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="update_modal" class="btn btn-primary">Save Changes</button>
                                  </div>
                              </form>
                            </div>
                          </div>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
