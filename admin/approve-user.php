<?php
session_start();
include "../db.php";

if ($_SESSION['role'] != 'admin') die("Access denied");

$id = $_GET['id'] ?? null;
if (!$id) die("Invalid user");

$conn->prepare("UPDATE users SET status='approved' WHERE id=?")
     ->execute([$id]);

$conn->prepare("
    INSERT INTO notifications (user_id, message, is_read, created_at)
    VALUES (?, ?, 0, NOW())
")->execute([
    $id,
    "✅ Your account has been approved."
]);

header("Location: pending-residents.php");
exit;
