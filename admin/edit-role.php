<?php
session_start(); include "../db.php";
if ($_SESSION['role']!='admin') die("Access denied");

if (isset($_POST['update'])) {
    $conn->prepare("UPDATE users SET role=? WHERE id=?")
         ->execute([$_POST['role'], $_POST['id']]);
}

$users = $conn->query("SELECT * FROM users")->fetchAll();
?>

<h2>Edit User Roles</h2>

<table border="1" cellpadding="8">
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Action</th>
</tr>

<?php foreach($users as $u): ?>
<tr>
<form method="POST">
    <td><?= $u['first_name']." ".$u['last_name'] ?></td>
    <td><?= $u['email'] ?></td>
    <td>
        <select name="role">
            <option <?= $u['role']=='admin'?'selected':'' ?>>admin</option>
            <option <?= $u['role']=='staff'?'selected':'' ?>>staff</option>
            <option <?= $u['role']=='resident'?'selected':'' ?>>resident</option>
            <option <?= $u['role']=='municipality'?'selected':'' ?>>municipality</option>
        </select>
    </td>
    <td>
        <input type="hidden" name="id" value="<?= $u['id'] ?>">
        <button name="update">Save</button>
    </td>
</form>
</tr>
<?php endforeach; ?>
</table>
