<?php
session_start();
include "../db.php";
if ($_SESSION['role'] != 'admin') die("Access denied");

if (isset($_POST['post'])) {
    $stmt = $conn->prepare("INSERT INTO announcements (title,message) VALUES (?,?)");
    $stmt->execute([$_POST['title'], $_POST['message']]);
}

$ann = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Announcements</title>
</head>
<body>

<h2>Post Announcement</h2>

<form method="POST">
    <input name="title" placeholder="Title" required><br><br>
    <textarea name="message" placeholder="Message" required></textarea><br><br>
    <button name="post">Post</button>
</form>

<hr>

<h2>All Announcements</h2>

<?php foreach ($ann as $a): ?>
    <h3><?= $a['title'] ?></h3>
    <p><?= $a['message'] ?></p>
    <small><?= $a['created_at'] ?></small>
    <hr>
<?php endforeach; ?>

</body>
</html>
