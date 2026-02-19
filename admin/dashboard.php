<?php
session_start();
include "../db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Access denied");
}

// counts
$totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalResidents = $conn->query("SELECT COUNT(*) FROM users WHERE role='resident'")->fetchColumn();
$totalDocs = $conn->query("SELECT COUNT(*) FROM document_requests")->fetchColumn();
$totalReports = $conn->query("SELECT COUNT(*) FROM reports")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body class="bg-gray-100">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-gray-900 text-white flex flex-col">
        <div class="p-6 text-xl font-bold border-b border-gray-700">
            Barangay Admin
        </div>
<nav class="flex-1 p-4 space-y-2">

    <a href="dashboard.php" class="block px-4 py-2 rounded bg-gray-700">
        🏠 Dashboard
    </a>

    <a href="users.php" class="block px-4 py-2 rounded hover:bg-gray-700">
        👥 User Management
    </a>

    <a href="documents.php" class="block px-4 py-2 rounded hover:bg-gray-700">
        📄 Document Requests
    </a>

    <a href="barangay-residents.php" class="block px-4 py-2 rounded hover:bg-gray-700">
        👥 Residents List
    </a>

    <a href="reports.php" class="block px-4 py-2 rounded hover:bg-gray-700">
        ⚠️ Reports & Complaints
    </a>

    <a href="announcements.php" class="block px-4 py-2 rounded hover:bg-gray-700">
        📢 Announcements
    </a>

    <a href="pending-residents.php" class="block px-4 py-2 rounded hover:bg-gray-700">
        ⏳ Pending Residents
    </a>

    <a href="edit-role.php" class="block px-4 py-2 rounded hover:bg-gray-700">
        🔑 Edit User Roles
    </a>

</nav>

        <div class="p-4 border-t border-gray-700">
<a href="../auth/logout.php" onclick="confirmLogout(event)"
   class="block px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 text-center">
    🚪 Logout
</a>

</a>

        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 p-8">
        <h1 class="text-3xl font-bold mb-6">
            Admin Dashboard 👋
        </h1>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-gray-600">Total Users</h2>
                <p class="text-4xl font-bold text-blue-700">
                    <?= $totalUsers ?>
                </p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-gray-600">Residents</h2>
                <p class="text-4xl font-bold text-green-600">
                    <?= $totalResidents ?>
                </p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-gray-600">Document Requests</h2>
                <p class="text-4xl font-bold text-indigo-600">
                    <?= $totalDocs ?>
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
        background: '#1f2937',
        color: '#fff',
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
                background: '#1f2937',
                color: '#fff',
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
