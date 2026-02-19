<?php
session_start();
include "../db.php";

/* ================= SECURITY ================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
}

/* ================= HANDLE ACTION ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'] ?? null;
    $action = $_POST['action'] ?? null;

    if ($id && in_array($action, ['approved', 'rejected'])) {

        $stmt = $conn->prepare("
            UPDATE document_requests 
            SET status = ? 
            WHERE id = ?
        ");
        $stmt->execute([$action, $id]);
    }

    // Redirect para maiwasan duplicate submit
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

/* ================= FETCH DATA ================= */
$docs = $conn->query("
    SELECT dr.*, u.first_name, u.last_name 
    FROM document_requests dr 
    JOIN users u ON u.id = dr.user_id
    ORDER BY dr.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin - Document Requests</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-6xl mx-auto">

<h2 class="text-2xl font-bold mb-6 text-gray-800">
    📄 Document Requests
</h2>

<div class="bg-white shadow rounded-lg overflow-hidden">
<table class="w-full text-sm text-left">
<thead class="bg-gray-200 text-gray-700 uppercase text-xs">
<tr>
    <th class="p-4">Resident</th>
    <th>Document</th>
    <th>Purpose</th>
    <th>Status</th>
    <th class="text-center">Action</th>
</tr>
</thead>
<tbody class="divide-y">

<?php if (count($docs) === 0): ?>
<tr>
    <td colspan="5" class="p-6 text-center text-gray-500">
        No document requests found.
    </td>
</tr>
<?php endif; ?>

<?php foreach($docs as $d): ?>
<tr class="hover:bg-gray-50">

    <td class="p-4 font-medium">
        <?= htmlspecialchars($d['first_name']." ".$d['last_name']) ?>
    </td>

    <td><?= htmlspecialchars($d['document_type']) ?></td>
    <td><?= htmlspecialchars($d['purpose']) ?></td>

    <td>
        <?php
        $colors = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'released' => 'bg-indigo-100 text-indigo-800',
            'rejected' => 'bg-red-100 text-red-800'
        ];
        $color = $colors[$d['status']] ?? 'bg-gray-100 text-gray-800';
        ?>
        <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $color ?>">
            <?= ucfirst($d['status']) ?>
        </span>
    </td>

    <td class="text-center space-x-2">

        <?php if(strtolower(trim($d['status'])) === 'pending'): ?>

            <!-- APPROVE -->
            <form method="POST" class="inline">
                <input type="hidden" name="id" value="<?= $d['id'] ?>">
                <input type="hidden" name="action" value="approved">
                <button type="submit"
                        onclick="return confirm('Approve this request?')"
                        class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700">
                    Approve
                </button>
            </form>

            <!-- REJECT -->
            <form method="POST" class="inline">
                <input type="hidden" name="id" value="<?= $d['id'] ?>">
                <input type="hidden" name="action" value="rejected">
                <button type="submit"
                        onclick="return confirm('Reject this request?')"
                        class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700">
                    Reject
                </button>
            </form>

        <?php else: ?>
            <span class="text-gray-500 text-xs">No action</span>
        <?php endif; ?>

    </td>
</tr>
<?php endforeach; ?>

</tbody>
</table>
</div>

</div>
</body>
</html>