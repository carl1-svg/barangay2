<?php
session_start(); include "../db.php";
if ($_SESSION['role']!='resident') die("Access denied");

if (isset($_POST['change'])) {
    $stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!password_verify($_POST['old'], $user['password'])) {
        echo "❌ Wrong password";
    } else {
        $new = password_hash($_POST['new'], PASSWORD_DEFAULT);
        $conn->prepare("UPDATE users SET password=? WHERE id=?")
             ->execute([$new, $_SESSION['user_id']]);
        echo "✅ Password changed";
    }
}
?>

<form method="POST">
    <input type="password" name="old" placeholder="Old Password" required><br>
    <input type="password" name="new" placeholder="New Password" required><br>
    <button name="change">Change</button>
</form>
