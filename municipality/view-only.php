<?php
session_start();
include "../db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'municipality') {
    die("Access denied");
}

$residents = $conn->query("SELECT COUNT(*) FROM users WHERE role='resident'")->fetchColumn();
$docs      = $conn->query("SELECT COUNT(*) FROM document_requests")->fetchColumn();
$reports   = $conn->query("SELECT COUNT(*) FROM reports")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Municipality Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body class="bg-gray-100">
<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-blue-900 text-white flex flex-col">
        <div class="p-6 text-xl font-bold border-b border-blue-700">
            🏛 Municipality
        </div>

        <nav class="flex-1 p-4 space-y-2">
            <a href="view-only.php"
               class="block px-4 py-2 rounded bg-blue-700">
               📊 Dashboard
            </a>

            <a href="view-residents.php"
               class="block px-4 py-2 rounded hover:bg-blue-700">
               👥 Residents
            </a>

            <a href="view-documents.php"
               class="block px-4 py-2 rounded hover:bg-blue-700">
               📄 Documents
            </a>

            <a href="view-reports.php"
               class="block px-4 py-2 rounded hover:bg-blue-700">
               ⚠️ Reports
            </a>

            <a href="view-announcements.php"
               class="block px-4 py-2 rounded hover:bg-blue-700">
               📢 Announcements
            </a>
        </nav>

        <!-- LOGOUT -->
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
            Municipality Overview
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 shadow rounded">
                <p class="text-gray-600">Residents</p>
                <h2 class="text-3xl font-bold text-blue-700">
                    <?= $residents ?>
                </h2>
            </div>

            <div class="bg-white p-6 shadow rounded">
                <p class="text-gray-600">Document Requests</p>
                <h2 class="text-3xl font-bold text-indigo-600">
                    <?= $docs ?>
                </h2>
            </div>

            <div class="bg-white p-6 shadow rounded">
                <p class="text-gray-600">Reports</p>
                <h2 class="text-3xl font-bold text-red-600">
                    <?= $reports ?>
                </h2>
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
