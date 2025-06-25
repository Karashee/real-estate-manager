<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$users = $pdo->query("SELECT id, name, email, role FROM users ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Admin Panel - User Management</h2>
    <a href="logout.php" class="btn btn-sm btn-danger float-end mb-3">Logout</a>

    <!-- Add User Form -->
    <form id="addUserForm" class="card p-3 shadow-sm mb-4 bg-white">
        <h5>Add New User</h5>
        <div class="row g-2">
            <div class="col-md-3"><input type="text" name="name" class="form-control" placeholder="Full Name" required></div>
            <div class="col-md-3"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
            <div class="col-md-3"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
            <div class="col-md-2">
                <select name="role" class="form-select">
                    <option value="tenant">Tenant</option>
                    <option value="manager">Manager</option>
                </select>
            </div>
            <div class="col-md-1"><button class="btn btn-primary w-100">Add</button></div>
        </div>
    </form>

    <!-- Users Table -->
    <table class="table table-bordered bg-white shadow-sm" id="usersTable">
        <thead class="table-light">
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr data-id="<?= $u['id'] ?>">
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= $u['role'] ?></td>
                    <td>
                        <button class="btn btn-sm btn-danger deleteBtn">🗑 Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
// Handle Add User
document.getElementById('addUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('admin_user_actions.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) window.location.reload();
    });
});

// Handle Delete
document.querySelectorAll('.deleteBtn').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Delete this user?')) return;

        const row = this.closest('tr');
        const userId = row.dataset.id;

        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', userId);

        fetch('admin_user_actions.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) row.remove();
        });
    });
});
</script>
</body>
</html>
