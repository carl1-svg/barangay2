<?php
session_start();
include "../db.php";
if ($_SESSION['role'] != 'admin') die("Access denied");

$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
</head>
<body>

<h2>Registered Users</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Verified</th>
    </tr>

    <?php foreach ($users as $u): ?>
    <tr>
        <td><?= $u['first_name']." ".$u['last_name'] ?></td>
        <td><?= $u['email'] ?></td>
        <td><?= $u['role'] ?></td>
        <td><?= $u['is_verified'] ? 'Yes' : 'No' ?></td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
