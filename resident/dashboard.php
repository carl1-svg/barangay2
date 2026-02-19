 <?php
session_start();
require "../db.php";

/* =========================
   SECURITY CHECK
========================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'resident') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'] ?? 0;

/* =========================
   FETCH USER STATUS
========================= */
$stmt = $conn->prepare("SELECT status FROM users WHERE id=? LIMIT 1");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$status = $user['status'] ?? 'pending_verification';

/* =========================
   FETCH COUNTS
========================= */
$stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0");
$stmt->execute([$user_id]);
$unread = (int)$stmt->fetchColumn();

$stmt = $conn->prepare("SELECT COUNT(*) FROM document_requests WHERE user_id=?");
$stmt->execute([$user_id]);
$totalDocs = (int)$stmt->fetchColumn();

$stmt = $conn->prepare("SELECT COUNT(*) FROM reports WHERE user_id=?");
$stmt->execute([$user_id]);
$totalReports = (int)$stmt->fetchColumn();

$totalActivity = $totalDocs + $totalReports;

/* =========================
   FETCH ANNOUNCEMENTS
========================= */
$stmt = $conn->prepare("SELECT * FROM announcements ORDER BY created_at DESC");
$stmt->execute();
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Resident Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<body class="bg-gray-100 text-gray-800">

<!-- TOP NAVBAR -->
<div class="bg-white shadow-sm px-6 py-3 flex items-center justify-between border-b">

    <div class="text-xl font-bold text-indigo-600">
        Barangay System
    </div>

    <div class="flex items-center space-x-4">

        <!-- Notifications -->
        <a href="notifications.php" class="relative">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M15 17h5l-1.405-1.405A2.032 
                    2.032 0 0118 14.158V11a6.002 
                    6.002 0 00-4-5.659V5a2 
                    2 0 10-4 0v.341C7.67 
                    6.165 6 8.388 6 11v3.159c0 
                    .538-.214 1.055-.595 
                    1.436L4 17h5m6 0v1a3 
                    3 0 11-6 0v-1m6 0H9"/>
            </svg>

            <?php if ($unread > 0): ?>
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">
                    <?= $unread ?>
                </span>
            <?php endif; ?>
        </a>

        <!-- User Dropdown -->
        <div class="relative">
            <button onclick="toggleDropdown()" class="flex items-center focus:outline-none">
                <div class="w-9 h-9 bg-indigo-500 text-white flex items-center justify-center rounded-full font-semibold">
                    <?= strtoupper(substr($_SESSION['role'],0,1)) ?>
                </div>
            </button>

            <div id="userDropdown" class="hidden absolute right-0 mt-3 w-44 bg-white rounded-xl shadow-lg border">
                <a href="profile.php" class="block px-4 py-3 text-sm hover:bg-gray-100">
                    My Profile
               <a href="../auth/logout.php" onclick="confirmLogout(event)"
   class="block px-4 py-3 text-sm text-red-600 hover:bg-red-50 rounded-b-xl">
    Logout
</a>

            </div>
        </div>

    </div>
</div>


<div class="flex min-h-screen">

<!-- SIDEBAR -->
<aside class="w-64 bg-white shadow-md hidden md:block">
    <div class="p-6 text-lg font-bold text-indigo-600 border-b">
        Dashboard
    </div>

    <nav class="p-4 space-y-2 text-sm">
        <a href="dashboard.php" class="block px-4 py-2 rounded-lg bg-indigo-100 text-indigo-600 font-semibold">
            🏠 Dashboard
        </a>
        <a href="profile.php" class="block px-4 py-2 rounded-lg hover:bg-gray-100">
            👤 My Profile
        </a>
        <a href="announcements.php" class="block px-4 py-2 rounded-lg hover:bg-gray-100">
            📢 Announcements
        </a>
        <a href="request-document.php" class="block px-4 py-2 rounded-lg hover:bg-gray-100">
            📄 Request Document
        </a>
        <a href="my-documents.php" class="block px-4 py-2 rounded-lg hover:bg-gray-100">
            📂 My Documents
        </a>
        <a href="report.php" class="block px-4 py-2 rounded-lg hover:bg-gray-100">
            ⚠️ Submit Report
        </a>
        <a href="my-reports.php" class="block px-4 py-2 rounded-lg hover:bg-gray-100">
            📑 My Reports
        </a>
    </nav>
</aside>


<!-- MAIN CONTENT -->
<main class="flex-1 p-6 md:p-8">

<h1 class="text-2xl font-bold mb-6">Welcome back 👋</h1>

<!-- STATUS ALERT -->
<?php if ($status !== 'approved'): ?>
<div class="mb-6 p-4 rounded-lg border 
    <?= $status === 'pending_verification'
        ? 'bg-yellow-50 text-yellow-700 border-yellow-300'
        : 'bg-orange-50 text-orange-700 border-orange-300' ?>">
    <?= $status === 'pending_verification'
        ? '⚠ Please verify your email to activate your account.'
        : '⏳ Your account is under admin review.' ?>
</div>
<?php endif; ?>

<!-- STAT CARDS -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">

    <div class="bg-white p-6 rounded-xl shadow-sm">
        <p class="text-gray-500 text-sm">Document Requests</p>
        <p class="text-3xl font-bold text-indigo-600"><?= $totalDocs ?></p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm">
        <p class="text-gray-500 text-sm">Reports Submitted</p>
        <p class="text-3xl font-bold text-red-500"><?= $totalReports ?></p>
    </div>

</div>

<!-- CHART SECTION -->
<?php if ($totalActivity > 0): ?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">

    <div class="bg-white p-6 rounded-xl shadow-sm">
        <h3 class="text-sm font-semibold mb-4 text-gray-600">
            Activity Overview
        </h3>
        <div class="relative h-64">
            <canvas id="circleChart"></canvas>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm">
        <h3 class="text-sm font-semibold mb-4 text-gray-600">
            Activity Comparison
        </h3>
        <canvas id="barChart"></canvas>
    </div>

</div>
<?php endif; ?>

<!-- ANNOUNCEMENTS -->
<div class="bg-white p-6 rounded-xl shadow-sm">
    <h2 class="text-lg font-semibold mb-4 border-b pb-2">
        Barangay Announcements
    </h2>

    <?php if (count($announcements) > 0): ?>
        <?php foreach ($announcements as $announce): ?>
            <div class="mb-5 border-b pb-3">
                <h3 class="font-semibold text-indigo-600">
                    <?= htmlspecialchars($announce['title']) ?>
                </h3>
                <p class="text-gray-600 mt-2 text-sm">
                    <?= nl2br(htmlspecialchars($announce['content'])) ?>
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    <?= date("F d, Y h:i A", strtotime($announce['created_at'])) ?>
                </p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-gray-500 text-sm">No announcements available.</p>
    <?php endif; ?>
</div>

</main>
</div>

<script>
function confirmLogout(e) {
    e.preventDefault();

    Swal.fire({
        title: 'Are you sure?',
        text: "You will be logged out of your account.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Yes, logout',
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

<script>
function toggleDropdown() {
    document.getElementById("userDropdown").classList.toggle("hidden");
}

const totalDocs = <?= $totalDocs ?>;
const totalReports = <?= $totalReports ?>;
const totalActivity = <?= $totalActivity ?>;

if(totalActivity > 0){

new Chart(document.getElementById('circleChart'), {
    type: 'doughnut',
    data: {
        labels: ['Documents', 'Reports'],
        datasets: [{
            data: [totalDocs, totalReports],
            backgroundColor: ['#3b82f6', '#ef4444']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: { legend: { position: 'bottom' } }
    }
});

new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: ['Documents', 'Reports'],
        datasets: [{
            data: [totalDocs, totalReports],
            backgroundColor: ['#3b82f6', '#ef4444'],
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } }
        }
    }
});

}
</script>

</body>
</html>
