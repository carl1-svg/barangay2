<?php
session_start();
include "../db.php";

// message after email verification
if (isset($_GET['verified'])) {
    $success = "✅ Email verified successfully! Please wait for admin approval.";
}

if (isset($_POST['login'])) {

    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // invalid credentials
    if (!$user || !password_verify($password, $user['password'])) {
        $error = "Invalid email or password.";
    }
    // email not verified
    elseif ($user['is_verified'] == 0) {
        $error = "Please verify your email address first.";
    }
    // waiting for admin approval
    elseif ($user['status'] == 'pending') {
        $error = "Your account is pending admin approval.";
    }
    // rejected account
    elseif ($user['status'] == 'rejected') {
        $error = "Your account has been rejected. Please contact the barangay office.";
    }
    // success login
    else {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role'];

        switch ($user['role']) {
            case 'admin':
                header("Location: ../admin/dashboard.php");
                break;

            case 'staff':
                header("Location: ../staff/dashboard.php");
                break;

            case 'resident':
                header("Location: ../resident/dashboard.php");
                break;

            case 'municipality':
                header("Location: ../municipality/view-only.php");
                break;

            default:
                session_destroy();
                die("Invalid role.");
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barangay Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4 relative">

<!-- 🔄 LOADING OVERLAY -->
<div id="loadingOverlay" 
     class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    
    <div class="bg-white p-8 rounded-2xl shadow-xl text-center">
        <div class="animate-spin rounded-full h-14 w-14 border-4 border-blue-600 border-t-transparent mx-auto"></div>
        <p class="mt-4 text-gray-700 font-medium">Logging in...</p>
    </div>

</div>

<div class="w-full max-w-5xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row">

    <!-- LEFT SIDE -->
    <div class="md:w-1/2 relative min-h-[450px]">
        <img src="../images/page.png"
             class="absolute inset-0 w-full h-full object-cover"
             alt="Background">
    </div>

    <!-- RIGHT SIDE -->
    <div class="md:w-1/2 p-10 flex items-center">
        <div class="w-full max-w-md mx-auto">

            <h2 class="text-2xl font-bold text-gray-800 mb-2">Welcome Back</h2>
            <p class="text-sm text-gray-500 mb-6">Login to your account</p>

            <?php if (isset($success)): ?>
                <div class="mb-4 p-3 bg-green-100 text-green-800 text-sm rounded-lg">
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="mb-4 p-3 bg-red-100 text-red-800 text-sm rounded-lg">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4" onsubmit="showLoader()">

                <!-- EMAIL -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address
                    </label>
                    <input type="email" name="email" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2
                                  focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>

                <!-- PASSWORD -->
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>

                    <input type="password" id="password" name="password" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10
                                  focus:outline-none focus:ring-2 focus:ring-blue-600">

                    <!-- 👁 Eye Button -->
                    <button type="button" onclick="togglePassword()" 
                            class="absolute right-3 top-9 text-gray-500 hover:text-gray-700">
                        
                    </button>
                </div>

                <!-- LOGIN BUTTON -->
                <button type="submit" name="login"
                        class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-3 rounded-lg transition">
                    Login
                </button>

            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 mb-2">New resident?</p>
                <a href="register.php"
                   class="inline-block px-6 py-2 rounded bg-blue-700 text-white font-semibold
                          hover:bg-blue-800 transition">
                    Register Account
                </a>
            </div>

        </div>
    </div>

</div>

<!-- 🔥 JAVASCRIPT -->
<script>

function showLoader() {
    document.getElementById("loadingOverlay").classList.remove("hidden");
}

function togglePassword() {
    const password = document.getElementById("password");

    if (password.type === "password") {
        password.type = "text";
    } else {
        password.type = "password";
    }
}

</script>

</body>
</html>


</body>
</html>


</body>
</html>
