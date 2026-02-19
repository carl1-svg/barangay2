<?php
session_start();
include "../db.php";

/* =========================
   SECURITY CHECK
========================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    die("Access denied");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: documents.php");
    exit;
}

$id = $_POST['id'] ?? null;
$status = $_POST['status'] ?? null;

if (!$id || !$status) {
    header("Location: documents.php");
    exit;
}

/* =========================
   UPDATE DOCUMENT STATUS
========================= */
$stmt = $conn->prepare("
    UPDATE document_requests 
    SET status = ?, updated_at = NOW()
    WHERE id = ?
");
$stmt->execute([$status, $id]);

/* =========================
   GET DOCUMENT OWNER
========================= */
$q = $conn->prepare("
    SELECT user_id, document_type 
    FROM document_requests 
    WHERE id = ?
");
$q->execute([$id]);
$doc = $q->fetch(PDO::FETCH_ASSOC);

if (!$doc) {
    header("Location: documents.php");
    exit;
}

/* =========================
   SEND NOTIFICATIONS
========================= */

if ($status === 'approved') {

    $message = "📄 Your {$doc['document_type']} request has been APPROVED. Please wait for release confirmation.";

    $notify = $conn->prepare("
        INSERT INTO notifications (user_id, message, is_read, created_at)
        VALUES (?, ?, 0, NOW())
    ");
    $notify->execute([$doc['user_id'], $message]);
}

if ($status === 'released') {

    $msgResident = "📄 Your {$doc['document_type']} is READY FOR RELEASE. Please visit the barangay office to claim it.";

    $notifyResident = $conn->prepare("
        INSERT INTO notifications (user_id, message, is_read, created_at)
        VALUES (?, ?, 0, NOW())
    ");
    $notifyResident->execute([$doc['user_id'], $msgResident]);

    /* notify all admins instead of fixed id=1 */
    $admins = $conn->query("SELECT id FROM users WHERE role = 'admin'");
    foreach ($admins as $admin) {
        $msgAdmin = "📢 A {$doc['document_type']} has been marked as RELEASED.";

        $notifyAdmin = $conn->prepare("
            INSERT INTO notifications (user_id, message, is_read, created_at)
            VALUES (?, ?, 0, NOW())
        ");
        $notifyAdmin->execute([$admin['id'], $msgAdmin]);
    }
}

header("Location: documents.php");
exit;