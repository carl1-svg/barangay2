<?php
session_start();
include "../db.php";
if ($_SESSION['role']!='resident') die("Access denied");

if (isset($_POST['send'])) {
    $stmt = $conn->prepare("
        INSERT INTO reports (user_id, subject, message)
        VALUES (?,?,?)
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        $_POST['subject'],
        $_POST['message']
    ]);
    echo "✅ Report submitted!";
}
?>

<h2>Submit Report / Complaint</h2>

<form method="POST">
    <input name="subject" placeholder="Subject" required><br><br>
    <textarea name="message" placeholder="Details" required></textarea><br><br>
    <button name="send">Submit</button>
</form>

<a href="dashboard.php">⬅ Back</a>
