<?php
session_start();
include "../db.php";
if ($_SESSION['role'] != 'municipality') die("Access denied");

$stmt = $conn->query("
    SELECT 
        r.*,
        CONCAT(u.first_name,' ',u.last_name) AS resident
    FROM reports r
    JOIN users u ON u.id = r.user_id
    ORDER BY r.created_at DESC
");

$reports = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<title>Reports / Complaints</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-100">

<h2 class="text-2xl font-bold mb-4">⚠️ Reports / Complaints</h2>

<table class="w-full bg-white shadow rounded">
<tr class="bg-gray-200">
    <th class="p-2 text-left">Resident</th>
    <th>Message</th>
    <th>Status</th>
    <th>Date</th>
</tr>

<?php if (count($reports) == 0): ?>
<tr>
    <td colspan="4" class="p-4 text-center text-gray-500">
        No reports found.
    </td>
</tr>
<?php endif; ?>

<?php foreach ($reports as $r): ?>
<tr class="border-t">
    <td class="p-2"><?= htmlspecialchars($r['resident']) ?></td>

    <!-- PALITAN ITO KUNG ANONG COLUMN ANG MERON -->
    <td>
        <?= htmlspecialchars(
            $r['message'] 
            ?? $r['details'] 
            ?? $r['content'] 
            ?? '—'
        ) ?>
    </td>

    <td><?= strtoupper($r['status'] ?? 'pending') ?></td>
    <td><?= date('M d, Y', strtotime($r['created_at'])) ?></td>
</tr>
<?php endforeach; ?>
</table>

<a href="view-only.php" class="text-blue-600 mt-4 inline-block">⬅ Back</a>

</body>
</html>
