<?php
session_start();
include "../db.php";
if ($_SESSION['role'] != 'staff') die("Access denied");

$id = $_GET['id'] ?? null;
if (!$id) die("Invalid request");

$conn->prepare("
    UPDATE document_requests 
    SET status='released' 
    WHERE id=?
")->execute([$id]);

header("Location: documents.php");
exit;
