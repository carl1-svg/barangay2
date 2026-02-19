<?php
session_start();
include "../db.php";
if ($_SESSION['role']!='resident') die("Access denied");

$ann = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC")->fetchAll();
?>

<h2>Barangay Announcements</h2>

<?php foreach($ann as $a): ?>
    <h3><?= $a['title'] ?></h3>
    <p><?= $a['message'] ?></p>
    <small><?= $a['created_at'] ?></small>
    <hr>
<?php endforeach; ?>

<a href="dashboard.php">⬅ Back</a>
