<?php
session_start();
include "../db.php";
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'resident') {
    die("Access denied");
}

$stmt = $conn->prepare("
    SELECT * FROM document_requests 
    WHERE user_id=? 
    ORDER BY created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$docs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Document Requests</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="p-8">

<h1 class="text-2xl font-bold mb-6 text-gray-800">
    📄 My Document Requests
</h1>

<div class="bg-white rounded-xl shadow-md overflow-hidden">

    <!-- TABLE HEADER TITLE -->
    <div class="px-6 py-4 border-b bg-gray-50">
        <h2 class="text-lg font-semibold text-gray-700">
            Document Request List
        </h2>
    </div>

    <!-- TABLE -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">

            <!-- DARK HEADER -->
            <thead class="bg-gray-800 text-white uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-4">Document</th>
                    <th class="px-6 py-4">Purpose</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4 text-center">Action</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 bg-white">

<?php if (count($docs) == 0): ?>
    <tr>
        <td colspan="5" class="px-6 py-6 text-center text-gray-500">
            No document requests yet.
        </td>
    </tr>
<?php endif; ?>

<?php foreach ($docs as $d): ?>
    <tr class="hover:bg-gray-50 transition">

        <td class="px-6 py-4 font-medium text-gray-800">
            <?= htmlspecialchars($d['document_type']) ?>
        </td>

        <td class="px-6 py-4 text-gray-600">
            <?= htmlspecialchars($d['purpose']) ?>
        </td>

        <!-- STATUS BADGE (Sneat Style) -->
        <td class="px-6 py-4">
            <?php if ($d['status'] == 'pending'): ?>
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                    Pending
                </span>
            <?php elseif ($d['status'] == 'processing'): ?>
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                    Processing
                </span>
            <?php elseif ($d['status'] == 'approved'): ?>
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                    Approved
                </span>
            <?php elseif ($d['status'] == 'released'): ?>
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-700">
                    Released
                </span>
            <?php else: ?>
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                    Rejected
                </span>
            <?php endif; ?>
        </td>

        <td class="px-6 py-4 text-gray-600">
            <?= date("M d, Y", strtotime($d['created_at'])) ?>
        </td>

        <td class="px-6 py-4 text-center">
            <?php if ($d['status'] == 'pending'): ?>
                <a href="cancel-document.php?id=<?= $d['id'] ?>"
                   onclick="return confirm('Cancel this document request?')"
                   class="inline-block px-3 py-1 text-xs font-semibold text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition">
                   Cancel
                </a>
            <?php else: ?>
                <span class="text-gray-400 text-lg">—</span>
            <?php endif; ?>
        </td>

    </tr>
<?php endforeach; ?>

            </tbody>
        </table>
    </div>
</div>

<br>

<a href="dashboard.php" 
   class="inline-block text-indigo-600 hover:underline text-sm">
    ⬅ Back to Dashboard
</a>

</div>

</body>


</body>
</html>
