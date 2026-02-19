<?php
session_start();
include "../db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'municipality') {
    die("Access denied");
}

$stmt = $conn->query("
    SELECT 
        dr.id,
        dr.document_type,
        dr.status,
        dr.created_at,
        CONCAT(u.first_name,' ',u.last_name) AS resident
    FROM document_requests dr
    JOIN users u ON u.id = dr.user_id
    ORDER BY dr.created_at DESC
");
$docs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Municipality | Document Requests</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

<h1 class="text-2xl font-bold mb-6">📄 Document Requests (View Only)</h1>

<div class="bg-white shadow rounded-lg overflow-x-auto">
<table class="w-full text-sm">
    <thead class="bg-gray-200 text-left">
        <tr>
            <th class="p-3">Resident</th>
            <th class="p-3">Document</th>
            <th class="p-3">Status</th>
            <th class="p-3">Date</th>
            <th class="p-3 text-center">Action</th>
        </tr>
    </thead>
    <tbody>
    <?php if (count($docs) == 0): ?>
        <tr>
            <td colspan="5" class="p-4 text-center text-gray-500">
                No document requests found.
            </td>
        </tr>
    <?php endif; ?>

    <?php foreach ($docs as $d): ?>
        <tr class="border-t hover:bg-gray-50">
            <td class="p-3"><?= htmlspecialchars($d['resident']) ?></td>
            <td class="p-3"><?= htmlspecialchars($d['document_type']) ?></td>
            <td class="p-3">
                <?php if ($d['status'] == 'pending'): ?>
                    <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800">Pending</span>
                <?php elseif ($d['status'] == 'processing'): ?>
                    <span class="px-2 py-1 rounded bg-blue-100 text-blue-800">Processing</span>
                <?php elseif ($d['status'] == 'approved'): ?>
                    <span class="px-2 py-1 rounded bg-green-100 text-green-800">Approved</span>
                <?php elseif ($d['status'] == 'released'): ?>
                    <span class="px-2 py-1 rounded bg-indigo-100 text-indigo-800">Released</span>
                <?php else: ?>
                    <span class="px-2 py-1 rounded bg-red-100 text-red-800">Rejected</span>
                <?php endif; ?>
            </td>
            <td class="p-3">
                <?= date("M d, Y", strtotime($d['created_at'])) ?>
            </td>
            <td class="p-3 text-center">
                <a href="view-document.php?id=<?= $d['id'] ?>"
                   class="text-blue-600 hover:underline">
                   View
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<div class="mt-6">
    <a href="view-only.php"
       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
        ⬅ Back to Dashboard
    </a>
</div>

</body>
</html>
