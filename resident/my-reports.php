<?php
session_start();
include "../db.php";
if ($_SESSION['role']!='resident') die("Access denied");

$stmt = $conn->prepare("
    SELECT * FROM reports 
    WHERE user_id=? 
    ORDER BY created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$reports = $stmt->fetchAll();
?>

<h2>My Reports</h2>

<table border="1" cellpadding="8">
<tr>
    <th>Subject</th>
    <th>Message</th>
    <th>Status</th>
    <th>Date</th>
</tr>

<?php foreach($reports as $r): ?>
<tr>
    <td><?= $r['subject'] ?></td>
    <td><?= $r['message'] ?></td>
    <td><?= strtoupper($r['status']) ?></td>
    <td><?= $r['created_at'] ?></td>
</tr>
<?php endforeach; ?>
</table>

<a href="dashboard.php">⬅ Back</a>
