<?php
session_start();
include "../db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'resident') {
    die("Access denied");
}

/* =========================
   FETCH USER DATA
========================= */
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

/* =========================
   UPDATE PROFILE INFO
========================= */
if (isset($_POST['update'])) {

    $conn->prepare("
        UPDATE users SET 
        first_name=?, last_name=?, purok=?, barangay=?, municipality=?, province=?
        WHERE id=?
    ")->execute([
        $_POST['first_name'], 
        $_POST['last_name'],
        $_POST['purok'], 
        $_POST['barangay'],
        $_POST['municipality'], 
        $_POST['province'],
        $_SESSION['user_id']
    ]);

    $success = "Profile updated successfully!";
}

/* =========================
   PROFILE PICTURE UPDATE
========================= */
if (isset($_POST['upload_photo']) && isset($_FILES['profile_photo'])) {

    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES["profile_photo"]["name"]);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $targetFile)) {

        $conn->prepare("UPDATE users SET profile_photo=? WHERE id=?")
             ->execute([$fileName, $_SESSION['user_id']]);

        $success = "Profile picture updated!";
    }
}

/* =========================
   CHANGE PASSWORD
========================= */
if (isset($_POST['change_password'])) {

    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (!password_verify($current, $user['password'])) {
        $error = "Current password is incorrect.";
    } elseif ($new !== $confirm) {
        $error = "New passwords do not match.";
    } else {

        $hashed = password_hash($new, PASSWORD_DEFAULT);

        $conn->prepare("UPDATE users SET password=? WHERE id=?")
             ->execute([$hashed, $_SESSION['user_id']]);

        $success = "Password changed successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>My Profile</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-indigo-700 text-white flex flex-col p-6">

        <h2 class="text-2xl font-bold mb-8">Resident Panel</h2>

        <nav class="flex flex-col space-y-4 text-sm">

            <a href="dashboard.php" class="hover:bg-indigo-600 p-2 rounded">
                🏠 Dashboard
            </a>

            <a href="request-document.php" class="hover:bg-indigo-600 p-2 rounded">
                📄 Request Document
            </a>

            <a href="my-documents.php" class="hover:bg-indigo-600 p-2 rounded">
                📑 My Requests
            </a>

            <a href="profile.php" class="bg-indigo-600 p-2 rounded">
                👤 My Profile
            </a>

            <a href="../auth/logout.php" class="mt-10 bg-red-500 hover:bg-red-600 p-2 rounded text-center">
                🚪 Logout
            </a>

        </nav>

    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 p-8">

        <div class="bg-white rounded-2xl shadow-md p-8 max-w-4xl mx-auto">

            <h1 class="text-2xl font-bold mb-6">My Profile</h1>

            <?php if (!empty($success)): ?>
                <div class="mb-6 p-4 bg-green-50 text-green-700 border border-green-200 rounded-lg">
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="mb-6 p-4 bg-red-50 text-red-700 border border-red-200 rounded-lg">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-8">

                <!-- PROFILE INFO -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <input name="first_name" value="<?= $user['first_name'] ?>" required
                    class="px-4 py-2 border rounded-lg" placeholder="First Name">

                    <input name="last_name" value="<?= $user['last_name'] ?>" required
                    class="px-4 py-2 border rounded-lg" placeholder="Last Name">

                    <input name="purok" value="<?= $user['purok'] ?>" required
                    class="px-4 py-2 border rounded-lg" placeholder="Purok">

                    <input name="barangay" value="<?= $user['barangay'] ?>" required
                    class="px-4 py-2 border rounded-lg" placeholder="Barangay">

                    <input name="municipality" value="<?= $user['municipality'] ?>" required
                    class="px-4 py-2 border rounded-lg" placeholder="Municipality">

                    <input name="province" value="<?= $user['province'] ?>" required
                    class="px-4 py-2 border rounded-lg" placeholder="Province">

                </div>

                <button name="update"
                class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                Save Profile Changes
                </button>

                <!-- PROFILE PICTURE -->
                <div class="border-t pt-8">

                    <h2 class="font-semibold mb-4">📷 Profile Picture</h2>

                    <div class="flex items-center space-x-6">

                        <?php if (!empty($user['profile_photo'])): ?>
                            <img src="../uploads/<?= $user['profile_photo'] ?>"
                            class="w-24 h-24 rounded-full object-cover border">
                        <?php else: ?>
                            <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center">
                                No Photo
                            </div>
                        <?php endif; ?>

                        <div>
                            <input type="file" name="profile_photo" class="block mb-2">
                            <button name="upload_photo"
                            class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                            Upload Photo
                            </button>
                        </div>

                    </div>
                </div>

                <!-- CHANGE PASSWORD -->
                <div class="border-t pt-8">

                    <h2 class="font-semibold mb-4">🔒 Change Password</h2>

                    <div class="grid md:grid-cols-3 gap-4">

                        <input type="password" name="current_password"
                        placeholder="Current Password"
                        class="px-4 py-2 border rounded-lg">

                        <input type="password" name="new_password"
                        placeholder="New Password"
                        class="px-4 py-2 border rounded-lg">

                        <input type="password" name="confirm_password"
                        placeholder="Confirm Password"
                        class="px-4 py-2 border rounded-lg">

                    </div>

                    <button name="change_password"
                    class="mt-4 bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700">
                    Update Password
                    </button>

                </div>

            </form>

        </div>

    </main>

</div>

</body>
</html>
