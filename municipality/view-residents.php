<?php
session_start();
include "../db.php";
if ($_SESSION['role'] != 'municipality') die("Access denied");

$stmt = $conn->query("
    SELECT 
        CONCAT(first_name,' ',last_name) AS name,
        email,
        barangay,
        status
    FROM users
    WHERE role='resident'
    ORDER BY last_name
");
$residents = $stmt->fetchAll();
?>

<h2>👥 Residents</h2>

<table border="1" cellpadding="8">
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Barangay</th>
    <th>Status</th>
</tr>

<?php foreach($residents as $r): ?>
<tr>
    <td><?= htmlspecialchars($r['name']) ?></td>
    <td><?= $r['email'] ?></td>
    <td><?= $r['barangay'] ?></td>
    <td><?= strtoupper($r['status']) ?></td>
</tr>
<?php endforeach; ?>
</table>

<a href="view-only.php">⬅ Back</a>
