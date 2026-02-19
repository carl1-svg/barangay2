<?php
session_start(); include "../db.php";
if ($_SESSION['role']!='resident') die("Access denied");

$logs = $conn->prepare("SELECT * FROM activity_logs WHERE user_id=? ORDER BY created_at DESC");
$logs->execute([$_SESSION['user_id']]);
?>

<h2>My Activity</h2>

<ul>
<?php foreach($logs as $l): ?>
    <li><?= $l['activity'] ?> – <?= $l['created_at'] ?></li>
<?php endforeach; ?>
</ul>
