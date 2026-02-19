<?php
session_start(); include "../db.php";
if ($_SESSION['role']!='admin') die("Access denied");

if (isset($_GET['resolve'])) {
    $conn->prepare("UPDATE reports SET status='resolved' WHERE id=?")
         ->execute([$_GET['resolve']]);
}

$reports = $conn->query("
    SELECT r.*, u.first_name, u.last_name
    FROM reports r
    JOIN users u ON u.id=r.user_id
    ORDER BY r.created_at DESC
")->fetchAll();
?>

<h2>Reports & Complaints</h2>

<table border="1" cellpadding="8">
<tr>
    <th>Resident</th>
    <th>Subject</th>
    <th>Message</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php foreach($reports as $r): ?>
<tr>
    <td><?= $r['first_name']." ".$r['last_name'] ?></td>
    <td><?= $r['subject'] ?></td>
    <td><?= $r['message'] ?></td>
    <td><?= $r['status'] ?></td>
    <td>
        <?php if($r['status']=='pending'): ?>
            <a href="?resolve=<?= $r['id'] ?>">Mark as Resolved</a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>
