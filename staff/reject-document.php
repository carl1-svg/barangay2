<?php
session_start();
include "../db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    die("Access denied");
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request");
}

$id = (int) $_GET['id'];

/* Kunin muna ang document para makuha ang user_id */
$stmt = $conn->prepare("
    SELECT user_id 
    FROM document_requests 
    WHERE id=?
");
$stmt->execute([$id]);
$doc = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doc) {
    die("Document not found");
}

/* Update status to rejected */
$update = $conn->prepare("
    UPDATE document_requests 
    SET status='rejected' 
    WHERE id=?
");
$update->execute([$id]);

/* Send notification sa resident */
$message = "❌ Your document request has been REJECTED. Please visit the barangay office for clarification.";

$notif = $conn->prepare("
    INSERT INTO notifications (user_id, message, created_at)
    VALUES (?, ?, NOW())
");
$notif->execute([$doc['user_id'], $message]);

header("Location: documents.php?status=rejected");
exit;
