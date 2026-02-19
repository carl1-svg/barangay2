<?php
session_start();
include "../db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'municipality') {
    die("Access denied");
}

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Invalid request");
}

// kunin document + resident full profile
$stmt = $conn->prepare("
    SELECT 
        d.document_type,
        d.purpose,
        d.status,
        d.created_at,

        u.first_name,
        u.middle_name,
        u.last_name,
        u.gender,
        u.birthdate,
        u.civil_status,
        u.purok,
        u.barangay,
        u.municipality,
        u.province,
        u.email
    FROM document_requests d
    JOIN users u ON u.id = d.user_id
    WHERE d.id = ?
");
$stmt->execute([$id]);
$doc = $stmt->fetch();

if (!$doc) {
    die("Document not found");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Municipality | View Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

<h1 class="text-2xl font-bold mb-6">
    📄 Document Request Details (View Only)
</h1>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- DOCUMENT INFO -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Document Information</h2>

        <p><strong>Document Type:</strong>
            <?= htmlspecialchars($doc['document_type']) ?>
        </p>

        <p><strong>Purpose:</strong>
            <?= htmlspecialchars($doc['purpose']) ?>
        </p>

        <p class="mt-2"><strong>Status:</strong>
            <?php if ($doc['status'] == 'pending'): ?>
                <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800">Pending</span>
            <?php elseif ($doc['status'] == 'processing'): ?>
                <span class="px-2 py-1 rounded bg-blue-100 text-blue-800">Processing</span>
            <?php elseif ($doc['status'] == 'approved'): ?>
                <span class="px-2 py-1 rounded bg-green-100 text-green-800">Approved</span>
            <?php elseif ($doc['status'] == 'released'): ?>
                <span class="px-2 py-1 rounded bg-indigo-100 text-indigo-800">Released</span>
            <?php else: ?>
                <span class="px-2 py-1 rounded bg-red-100 text-red-800">Rejected</span>
            <?php endif; ?>
        </p>

        <p class="mt-2">
            <strong>Date Requested:</strong><br>
            <?= date("F d, Y h:i A", strtotime($doc['created_at'])) ?>
        </p>
    </div>

    <!-- RESIDENT PROFILE -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Resident Profile</h2>

        <p><strong>Name:</strong>
            <?= htmlspecialchars(
                $doc['first_name']." ".
                $doc['middle_name']." ".
                $doc['last_name']
            ) ?>
        </p>

        <p><strong>Email:</strong>
            <?= htmlspecialchars($doc['email']) ?>
        </p>

        <p><strong>Gender:</strong>
            <?= htmlspecialchars($doc['gender']) ?>
        </p>

        <p><strong>Birthdate:</strong>
            <?= htmlspecialchars($doc['birthdate']) ?>
        </p>

        <p><strong>Civil Status:</strong>
            <?= htmlspecialchars($doc['civil_status']) ?>
        </p>

        <p class="mt-2">
            <strong>Address:</strong><br>
            Purok <?= htmlspecialchars($doc['purok']) ?>,<br>
            <?= htmlspecialchars($doc['barangay']) ?>,<br>
            <?= htmlspecialchars($doc['municipality']) ?>,
            <?= htmlspecialchars($doc['province']) ?>
        </p>
    </div>

</div>

<!-- BACK BUTTON -->
<div class="mt-6">
    <a href="view-documents.php"
       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
        ⬅ Back to Document List
    </a>
</div>

</body>
</html>
