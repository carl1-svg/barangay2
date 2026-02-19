<?php
session_start();
include "../db.php";
if ($_SESSION['role'] != 'staff') die("Access denied");

$reports = $conn->query("
    SELECT r.*, u.first_name, u.last_name
    FROM reports r
    JOIN users u ON r.user_id = u.id
    ORDER BY r.created_at DESC
");
?>

<h2 class="text-2xl font-bold mb-4">Reports & Complaints</h2>

<?php foreach($reports as $r): ?>
<div style="border:1px solid #ccc;padding:10px;margin-bottom:10px">
    <strong><?= $r['first_name']." ".$r['last_name'] ?></strong><br>
    <?= $r['message'] ?><br>
    <small><?= $r['created_at'] ?></small>
</div>
<?php endforeach; ?>
