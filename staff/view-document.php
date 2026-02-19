<?php
session_start();
include "../db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    die("Access denied");
}

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("Invalid request ID");
}

$stmt = $conn->prepare("
    SELECT d.*, 
           u.first_name, u.middle_name, u.last_name,
           u.gender, u.birthdate, u.civil_status,
           u.purok, u.barangay, u.municipality, u.province,
           u.email
    FROM document_requests d
    INNER JOIN users u ON d.user_id = u.id
    WHERE d.id = ?
");
$stmt->execute([$id]);
$doc = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doc) {
    die("Document not found.");
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Document Details</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen p-8">

<div class="max-w-4xl mx-auto">

<h1 class="text-2xl font-bold text-gray-800 mb-6">
    Document Request Details
</h1>

<div class="grid md:grid-cols-2 gap-6">

<div class="bg-white shadow rounded-lg p-6">
<h2 class="font-semibold text-gray-700 mb-4">Document Information</h2>

<p><strong>Type:</strong> <?= htmlspecialchars($doc['document_type']) ?></p>
<p><strong>Purpose:</strong> <?= htmlspecialchars($doc['purpose']) ?></p>
<p><strong>Status:</strong> <?= ucfirst($doc['status']) ?></p>
<p><strong>Date Requested:</strong>
<?= date("F d, Y h:i A", strtotime($doc['created_at'])) ?>
</p>
</div>

<div class="bg-white shadow rounded-lg p-6">
<h2 class="font-semibold text-gray-700 mb-4">Resident Information</h2>

<p><strong>Name:</strong>
<?= htmlspecialchars($doc['first_name']." ".$doc['middle_name']." ".$doc['last_name']) ?>
</p>
<p><strong>Email:</strong> <?= htmlspecialchars($doc['email']) ?></p>
<p><strong>Gender:</strong> <?= htmlspecialchars($doc['gender']) ?></p>
<p><strong>Birthdate:</strong> <?= htmlspecialchars($doc['birthdate']) ?></p>
<p><strong>Civil Status:</strong> <?= htmlspecialchars($doc['civil_status']) ?></p>

<p class="mt-2"><strong>Address:</strong><br>
Purok <?= htmlspecialchars($doc['purok']) ?>,
<?= htmlspecialchars($doc['barangay']) ?>,
<?= htmlspecialchars($doc['municipality']) ?>,
<?= htmlspecialchars($doc['province']) ?>
</p>
</div>

</div>

<div class="mt-6 flex gap-3">
<a href="documents.php"
   class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
   Back
</a>

<?php if ($doc['status'] === 'approved'): ?>
<a href="quick-release.php?id=<?= $doc['id'] ?>"
   onclick="return confirm('Mark this document as released?')"
   class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
   Mark as Released
</a>
<?php endif; ?>

<?php if ($doc['status'] !== 'rejected'): ?>
<a href="reject-document.php?id=<?= $doc['id'] ?>"
   onclick="return confirm('Reject this request?')"
   class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
   Reject
</a>
<?php endif; ?>

</div>

</div>
</body>
</html>