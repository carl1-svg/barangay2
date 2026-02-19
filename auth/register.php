<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resident Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

<!-- MAIN CONTAINER -->
<div class="w-full max-w-6xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row">

    <!-- LEFT SIDE -->
    <div class="md:w-1/2 relative min-h-[500px]">

        <!-- Background Image -->
        <img src="../images/page.png"
             class="absolute inset-0 w-full h-full object-cover"
             alt="Background">



    </div>


    <!-- RIGHT SIDE -->
    <div class="md:w-1/2 p-8 flex items-center">

        <div class="w-full">

            <h2 class="text-2xl font-bold text-gray-800 mb-2">Sign Up</h2>
            <p class="text-sm text-gray-500 mb-6">Fill in your details below</p>

            <?php if (isset($_SESSION['msg'])): ?>
                <div class="mb-4 p-3 text-sm bg-green-100 text-green-800 rounded">
                    <?= $_SESSION['msg']; unset($_SESSION['msg']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="register_process.php"
                  class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <input type="text" name="first_name" placeholder="First Name"
                       class="border p-2 rounded-lg w-full" required>

                <input type="text" name="middle_name" placeholder="Middle Name"
                       class="border p-2 rounded-lg w-full">

                <input type="text" name="last_name" placeholder="Last Name"
                       class="border p-2 rounded-lg w-full" required>

                <select name="gender" required
                        class="border p-2 rounded-lg w-full">
                    <option value="">Select Gender</option>
                    <option>Male</option>
                    <option>Female</option>
                </select>

                <input type="date" name="birthdate"
                       class="border p-2 rounded-lg w-full" required>

                <select name="civil_status" required
                        class="border p-2 rounded-lg w-full">
                    <option value="">Civil Status</option>
                    <option>Single</option>
                    <option>Married</option>
                    <option>Widowed</option>
                    <option>Separated</option>
                </select>

                <input type="text" name="purok" placeholder="Purok"
                       class="border p-2 rounded-lg w-full" required>

                <input type="text" name="barangay" placeholder="Barangay"
                       class="border p-2 rounded-lg w-full" required>

                <input type="text" name="municipality" placeholder="Municipality"
                       class="border p-2 rounded-lg w-full" required>

                <input type="text" name="province" placeholder="Province"
                       class="border p-2 rounded-lg w-full" required>

                <input type="email" name="email" placeholder="Email Address"
                       class="border p-2 rounded-lg w-full" required>

                <input type="password" name="password" placeholder="Password"
                       class="border p-2 rounded-lg w-full" required>

                <div class="md:col-span-2 mt-4">
                    <button type="submit" name="register"
                            class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-3 rounded-lg transition">
                        Register Now
                    </button>
                </div>

            </form>

            <!-- Login Button (separate from form para hindi mag submit) -->
            <div class="mt-4 text-center">
                <a href="login.php"
                   class="inline-block px-6 py-2 rounded bg-blue-700 text-white font-semibold
                          hover:bg-blue-800 transition">
                    Login Now
                </a>
            </div>

        </div>
    </div>

</div>

</body>
</html>
