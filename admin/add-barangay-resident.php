<?php
session_start();
include "../db.php";
if ($_SESSION['role'] != 'admin') die("Access denied");

if (isset($_POST['save'])) {

    $stmt = $conn->prepare("
        INSERT INTO barangay_residents
        (first_name, middle_name, last_name, birthdate, gender, civil_status,
         purok, barangay, municipality, province)
        VALUES (?,?,?,?,?,?,?,?,?,?)
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
        $_POST['province']
    ]);

    header("Location: barangay-residents.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Add Barangay Resident</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

<h1 class="text-2xl font-bold mb-6">➕ Add Barangay Resident</h1>

<form method="POST" class="bg-white p-6 rounded shadow max-w-xl space-y-4">

<input required name="first_name" placeholder="First Name"
class="w-full border px-3 py-2 rounded">

<input name="middle_name" placeholder="Middle Name"
class="w-full border px-3 py-2 rounded">

<input required name="last_name" placeholder="Last Name"
class="w-full border px-3 py-2 rounded">

<input required type="date" name="birthdate"
class="w-full border px-3 py-2 rounded">

<select name="gender" class="w-full border px-3 py-2 rounded">
    <option>Male</option>
    <option>Female</option>
</select>

<select name="civil_status" class="w-full border px-3 py-2 rounded">
    <option>Single</option>
    <option>Married</option>
    <option>Widowed</option>
</select>

<input required name="purok" placeholder="Purok"
class="w-full border px-3 py-2 rounded">

<input required name="barangay" placeholder="Barangay"
class="w-full border px-3 py-2 rounded">

<input required name="municipality" placeholder="Municipality"
class="w-full border px-3 py-2 rounded">

<input required name="province" placeholder="Province"
class="w-full border px-3 py-2 rounded">

<div class="flex gap-2">
    <button name="save"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
        Save
    </button>

    <a href="barangay-residents.php"
       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
       Cancel
    </a>
</div>

</form>
</body>
</html>
