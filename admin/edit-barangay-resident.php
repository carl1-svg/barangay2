<?php
session_start();
include "../db.php";
if ($_SESSION['role'] != 'admin') die("Access denied");

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM barangay_residents WHERE id=?");
$stmt->execute([$id]);
$resident = $stmt->fetch();

if (!$resident) die("Resident not found");

if (isset($_POST['update'])) {

    $stmt = $conn->prepare("
        UPDATE barangay_residents SET
        first_name=?,
        middle_name=?,
        last_name=?,
        birthdate=?,
        gender=?,
        civil_status=?,
        purok=?,
        barangay=?,
        municipality=?,
        province=?
        WHERE id=?
    ");

    $stmt->execute([
        $_POST['first_name'],
        $_POST['middle_name'],
        $_POST['last_name'],
        $_POST['birthdate'],
        $_POST['gender'],
        $_POST['civil_status'],
        $_POST['purok'],
        $_POST['barangay'],
        $_POST['municipality'],
        $_POST['province'],
        $id
    ]);

    header("Location: barangay-residents.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Barangay Resident</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

<h1 class="text-2xl font-bold mb-6">✏ Edit Resident</h1>

<form method="POST" class="bg-white p-6 rounded shadow max-w-xl space-y-4">

<input name="first_name" value="<?= $resident['first_name'] ?>" class="w-full border px-3 py-2 rounded">
<input name="middle_name" value="<?= $resident['middle_name'] ?>" class="w-full border px-3 py-2 rounded">
<input name="last_name" value="<?= $resident['last_name'] ?>" class="w-full border px-3 py-2 rounded">
<input type="date" name="birthdate" value="<?= $resident['birthdate'] ?>" class="w-full border px-3 py-2 rounded">

<select name="gender" class="w-full border px-3 py-2 rounded">
    <option <?= $resident['gender']=='Male'?'selected':'' ?>>Male</option>
    <option <?= $resident['gender']=='Female'?'selected':'' ?>>Female</option>
</select>

<select name="civil_status" class="w-full border px-3 py-2 rounded">
    <option <?= $resident['civil_status']=='Single'?'selected':'' ?>>Single</option>
    <option <?= $resident['civil_status']=='Married'?'selected':'' ?>>Married</option>
    <option <?= $resident['civil_status']=='Widowed'?'selected':'' ?>>Widowed</option>
</select>

<input name="purok" value="<?= $resident['purok'] ?>" class="w-full border px-3 py-2 rounded">
<input name="barangay" value="<?= $resident['barangay'] ?>" class="w-full border px-3 py-2 rounded">
<input name="municipality" value="<?= $resident['municipality'] ?>" class="w-full border px-3 py-2 rounded">
<input name="province" value="<?= $resident['province'] ?>" class="w-full border px-3 py-2 rounded">

<div class="flex gap-2">
    <button name="update" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
        Update
    </button>
    <a href="barangay-residents.php"
       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
       Cancel
    </a>
</div>

</form>
</body>
</html>
