<?php
session_start();
include "../db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Access denied");
}

$users = $conn->query("
    SELECT 
        id,
        CONCAT(
            first_name, ' ',
            IFNULL(middle_name, ''), ' ',
            last_name
        ) AS name,
        email
    FROM users 
    WHERE role='resident' 
    AND status='pending'
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pending Residents</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-100">

<h1 class="text-2xl font-bold mb-4">Pending Resident Approvals</h1>

<table class="w-full bg-white shadow rounded">
    <tr class="bg-gray-200">
        <th class="p-2 text-left">Name</th>
        <th class="p-2 text-left">Email</th>
        <th class="p-2">Action</th>
    </tr>

<?php while ($u = $users->fetch()): ?>
<tr class="border-t">
    <td class="p-2">
        <?= htmlspecialchars(trim($u['name'])) ?>
    </td>
    <td class="p-2">
        <?= htmlspecialchars($u['email']) ?>
    </td>
    <td class="p-2 space-x-2 text-center">
        <a href="approve-user.php?id=<?= $u['id'] ?>" 
           class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">
           Approve
        </a>
        <a href="reject-user.php?id=<?= $u['id'] ?>" 
           class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
           Reject
        </a>
    </td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>
