<?php
session_start();
include "../db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'staff') {
    die("Access denied");
}

$stmt = $conn->prepare("
    SELECT 
        dr.*,
        CONCAT(u.first_name, ' ', u.last_name) AS resident_name
    FROM document_requests dr
    JOIN users u ON u.id = dr.user_id
    ORDER BY 
        CASE 
            WHEN dr.status = 'pending' THEN 1
            WHEN dr.status = 'approved' THEN 2
            WHEN dr.status = 'released' THEN 3
            WHEN dr.status = 'rejected' THEN 4
        END,
        dr.created_at DESC
");
$stmt->execute();
$docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Document Requests</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

<h1 class="text-3xl font-bold mb-6 text-gray-800">
    📄 Document Requests Monitoring
</h1>

<table class="w-full bg-white shadow-lg rounded-lg overflow-hidden">
<thead class="bg-gray-200 text-gray-700 uppercase text-sm">
<tr>
    <th class="p-4 text-left">Resident</th>
    <th>Document</th>
    <th>Purpose</th>
    <th>Status</th>
    <th>Date</th>
    <th class="text-center">Actions</th>
</tr>
</thead>

<tbody class="divide-y">

<?php if (count($docs) == 0): ?>
<tr>
    <td colspan="6" class="p-6 text-center text-gray-500">
        No document requests.
    </td>
</tr>
<?php endif; ?>

<?php foreach ($docs as $d): ?>
<tr class="hover:bg-gray-50 transition">
    <td class="p-4 font-medium">
        <?= htmlspecialchars($d['resident_name']) ?>
    </td>

    <td><?= htmlspecialchars($d['document_type']) ?></td>
    <td><?= htmlspecialchars($d['purpose']) ?></td>

    <!-- STATUS DISPLAY -->
    <td>
        <?php
        $status = $d['status'];
        $statusClass = '';

        switch ($status) {
            case 'pending':
                $statusClass = 'bg-yellow-100 text-yellow-800';
                break;
            case 'approved':
                $statusClass = 'bg-green-100 text-green-800';
                break;
            case 'released':
                $statusClass = 'bg-indigo-100 text-indigo-800';
                break;
            case 'rejected':
                $statusClass = 'bg-red-100 text-red-800';
                break;
            default:
                $statusClass = 'bg-gray-100 text-gray-800';
        }
        ?>
        <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $statusClass ?>">
            <?= ucfirst($status) ?>
        </span>
    </td>

    <td><?= date('M d, Y', strtotime($d['created_at'])) ?></td>

    <td class="text-center space-x-2">

        <a href="view-document.php?id=<?= $d['id'] ?>"
           class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">
           View
        </a>

        <?php if ($status == 'pending'): ?>

            <form method="POST" action="update-document.php" class="inline">
                <input type="hidden" name="id" value="<?= $d['id'] ?>">
                <input type="hidden" name="status" value="approved">
                <button class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700">
                    Approve
                </button>
            </form>

            <a href="reject-document.php?id=<?= $d['id'] ?>"
               onclick="return confirm('Reject this request?')"
               class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700">
               Reject
            </a>

        <?php elseif ($status == 'approved'): ?>

            <form method="POST" action="update-document.php" class="inline">
                <input type="hidden" name="id" value="<?= $d['id'] ?>">
                <input type="hidden" name="status" value="released">
                <button class="bg-purple-600 text-white px-3 py-1 rounded text-xs hover:bg-purple-700">
                    Mark Released
                </button>
            </form>

        <?php elseif ($status == 'released'): ?>

            <span class="text-indigo-600 text-xs font-semibold">
                ✔ Already Released
            </span>

        <?php else: ?>

            <span class="text-red-600 text-xs font-semibold">
                ✖ Rejected
            </span>

        <?php endif; ?>

    </td>
</tr>
<?php endforeach; ?>

</tbody>
</table>

</body>
</html>