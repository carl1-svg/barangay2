<?php
session_start();
include "../db.php";
if ($_SESSION['role'] != 'staff') die("Access denied");

$totalDocs = $conn->query("SELECT COUNT(*) FROM document_requests")->fetchColumn();
$pendingDocs = $conn->query("SELECT COUNT(*) FROM document_requests WHERE status='pending'")->fetchColumn();
$totalReports = $conn->query("SELECT COUNT(*) FROM reports")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body class="bg-gray-100">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-blue-900 text-white flex flex-col">
        <div class="p-6 text-xl font-bold border-b border-blue-700">
            Barangay Staff
        </div>

        <nav class="flex-1 p-4 space-y-2">
            <a href="dashboard.php" class="block px-4 py-2 rounded bg-blue-700">
                🏠 Dashboard
            </a>
            <a href="documents.php" class="block px-4 py-2 rounded hover:bg-blue-700">
                📄 Document Requests
            </a>
            <a href="reports.php" class="block px-4 py-2 rounded hover:bg-blue-700">
                ⚠️ Reports
            </a>
        </nav>

        <div class="p-4 border-t border-blue-700">
           <a href="../auth/logout.php" onclick="confirmLogout(event)"
   class="block text-center bg-red-500 hover:bg-red-600 px-4 py-2 rounded">
    🚪 Logout
</a>

        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 p-8">
        <h1 class="text-3xl font-bold mb-6">
            Staff Dashboard 👋
        </h1>

        <!-- STATS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-gray-600">Total Document Requests</h2>
                <p class="text-4xl font-bold text-blue-700">
                    <?= $totalDocs ?>
                </p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-gray-600">Pending Requests</h2>
                <p class="text-4xl font-bold text-orange-600">
                    <?= $pendingDocs ?>
                </p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-gray-600">Reports</h2>
                <p class="text-4xl font-bold text-red-600">
                    <?= $totalReports ?>
                </p>
            </div>

        </div>
    </main>

    <script>
function confirmLogout(e) {
    e.preventDefault();

    Swal.fire({
        title: 'Logout Confirmation',
        text: "Are you sure you want to logout?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Logout',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {

            Swal.fire({
                title: 'Logging out...',
                text: 'Please wait',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            setTimeout(() => {
                window.location.href = "../auth/logout.php";
            }, 1200);
        }
    });
}
</script>

</div>

</body>
</html>
