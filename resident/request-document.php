<?php
session_start();
include "../db.php";
if ($_SESSION['role']!='resident') die("Access denied");

if (isset($_POST['submit'])) {
    $stmt = $conn->prepare("
        INSERT INTO document_requests (user_id, document_type, purpose)
        VALUES (?,?,?)
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        $_POST['document_type'],
        $_POST['purpose']
    ]);
    echo "✅ Request submitted!";
}
?>

<h2>Request Barangay Document</h2>

<form method="POST">
    <select name="document_type" required>
        <option value="">Select Document</option>
        <option>Barangay Clearance</option>
        <option>Certificate of Indigency</option>
        <option>Certificate of Residency</option>
        <option>Barangay ID</option>
    </select><br><br>

    <textarea name="purpose" placeholder="Purpose" required></textarea><br><br>

    <button name="submit">Submit Request</button>
</form>

<a href="dashboard.php">⬅ Back</a>
