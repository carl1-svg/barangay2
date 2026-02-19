<?php
session_start();
include "../db.php";
if ($_SESSION['role'] != 'municipality') die("Access denied");

$stmt = $conn->query("
    SELECT * 
    FROM announcements
    ORDER BY created_at DESC
");

$announcements = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<title>Announcements</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-100">

<h2 class="text-2xl font-bold mb-4">📢 Announcements</h2>

<table class="w-full bg-white shadow rounded">
<tr class="bg-gray-200">
    <th class="p-2 text-left">Title</th>
    <th>Message</th>
    <th>Date</th>
</tr>

<?php if (count($announcements) == 0): ?>
<tr>
    <td colspan="3" class="p-4 text-center text-gray-500">
        No announcements found.
    </td>
</tr>
<?php endif; ?>

<?php foreach ($announcements as $a): ?>
<tr class="border-t">
    <td class="p-2 font-semibold">
        <?= htmlspecialchars($a['title'] ?? '—') ?>
    </td>

    <td>
        <?= htmlspecialchars(
            $a['message']
            ?? $a['description']
            ?? $a['details']
            ?? $a['announcement']
            ?? '—'
        ) ?>
    </td>

    <td><?= date('M d, Y', strtotime($a['created_at'])) ?></td>
</tr>
<?php endforeach; ?>
</table>

<a href="view-only.php" class="text-blue-600 mt-4 inline-block">⬅ Back</a>

</body>
</html>
