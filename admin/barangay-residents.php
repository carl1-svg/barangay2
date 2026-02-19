<?php
session_start();
include "../db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Access denied");
}

$residents = $conn->query("
    SELECT * FROM barangay_residents
    ORDER BY last_name, first_name
")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<title>Barangay Resident Masterlist</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

<?php if (isset($_GET['imported'])): ?>
<div class="mb-4 bg-green-100 text-green-800 p-3 rounded">
    ✅ <?= htmlspecialchars($_GET['imported']) ?> residents imported successfully
</div>
<?php endif; ?>

<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">📋 Barangay Resident Masterlist</h1>

    <a href="add-barangay-resident.php"
       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
       ➕ Add Resident
    </a>
</div>

<!-- 🔍 SEARCH BAR -->
<div class="mb-4">
    <input
        type="text"
        id="searchInput"
        placeholder="🔍 Search resident name..."
        class="w-full md:w-1/3 px-4 py-2 border rounded focus:outline-none focus:ring"
        onkeyup="filterResidents()"
    >
</div>

<div class="bg-white shadow rounded overflow-x-auto">
<table class="w-full text-sm" id="residentTable">
    <thead class="bg-gray-200">
        <tr>
            <th class="p-2 text-left">Name</th>
            <th class="p-2 text-left">Birthdate</th>
            <th class="p-2 text-left">Gender</th>
            <th class="p-2 text-left">Civil Status</th>
            <th class="p-2 text-left">Purok</th>
            <th class="p-2 text-center">Action</th>
        </tr>
    </thead>
    <tbody>

    <?php if (count($residents) == 0): ?>
        <tr>
            <td colspan="6" class="p-4 text-center text-gray-500">
                No records yet.
            </td>
        </tr>
    <?php endif; ?>

    <?php foreach ($residents as $r): ?>
        <tr class="border-t hover:bg-gray-50 resident-row">
            <td class="p-2 resident-name">
                <?= htmlspecialchars(
                    $r['last_name'].", ".$r['first_name']." ".$r['middle_name']
                ) ?>
            </td>
            <td class="p-2"><?= htmlspecialchars($r['birthdate']) ?></td>
            <td class="p-2"><?= htmlspecialchars($r['gender']) ?></td>
            <td class="p-2"><?= htmlspecialchars($r['civil_status']) ?></td>
            <td class="p-2"><?= htmlspecialchars($r['purok']) ?></td>
            <td class="p-2 text-center space-x-2">
                <a href="edit-barangay-resident.php?id=<?= $r['id'] ?>"
                   class="text-blue-600 hover:underline">
                   ✏ Edit
                </a>
                <a href="delete-barangay-resident.php?id=<?= $r['id'] ?>"
                   onclick="return confirm('Delete this resident?')"
                   class="text-red-600 hover:underline">
                   🗑 Delete
                </a>
            </td>
        </tr>
    <?php endforeach; ?>

    </tbody>
</table>
</div>

<a href="dashboard.php"
   class="inline-block mt-6 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
   ⬅ Back to Dashboard
</a>

<!-- 🔥 SEARCH SCRIPT -->
<script>
function filterResidents() {
    const input = document.getElementById("searchInput").value.toLowerCase();
    const rows = document.querySelectorAll(".resident-row");

    rows.forEach(row => {
        const name = row.querySelector(".resident-name").innerText.toLowerCase();
        row.style.display = name.includes(input) ? "" : "none";
    });
}
</script>

</body>
</html>
