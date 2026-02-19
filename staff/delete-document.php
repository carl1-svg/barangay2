<?php
session_start();
include "../db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    die("Access denied");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    if ($id) {
        $stmt = $conn->prepare("DELETE FROM document_requests WHERE id = ?");
        $stmt->execute([$id]);
    }
}

header("Location: documents.php");
exit;