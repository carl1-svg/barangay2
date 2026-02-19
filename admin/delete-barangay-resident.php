<?php
session_start();
include "../db.php";
if ($_SESSION['role'] != 'admin') die("Access denied");

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("DELETE FROM barangay_residents WHERE id=?");
$stmt->execute([$id]);

header("Location: barangay-residents.php");
exit;
