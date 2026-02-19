<?php
session_start(); include "../db.php";
if ($_SESSION['role']!='resident') die("Access denied");

$conn->prepare("
    DELETE FROM document_requests 
    WHERE id=? AND user_id=? AND status='pending'
")->execute([$_GET['id'], $_SESSION['user_id']]);

header("Location: my-documents.php");
