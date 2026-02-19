<?php
session_start();
include "../db.php";
include "../mail.php";

if (!isset($_POST['register'])) {
    header("Location: register.php");
    exit;
}

// sanitize / assign
$first_name   = trim($_POST['first_name']);
$middle_name  = trim($_POST['middle_name']);
$last_name    = trim($_POST['last_name']);
$gender       = $_POST['gender'];
$birthdate    = $_POST['birthdate'];
$civil_status = $_POST['civil_status'];
$purok        = trim($_POST['purok']);
$barangay     = trim($_POST['barangay']);
$municipality = trim($_POST['municipality']);
$province     = trim($_POST['province']);
$email        = trim($_POST['email']);
$password     = password_hash($_POST['password'], PASSWORD_DEFAULT);

$code = bin2hex(random_bytes(32));

// check duplicate email
$check = $conn->prepare("SELECT id FROM users WHERE email=?");
$check->execute([$email]);

if ($check->rowCount() > 0) {
    $_SESSION['msg'] = "❌ Email already exists.";
    header("Location: register.php");
    exit;
}

// insert user (PENDING by default)
$stmt = $conn->prepare("
    INSERT INTO users (
        first_name, middle_name, last_name,
        gender, birthdate, civil_status,
        purok, barangay, municipality, province,
        email, password,
        role, is_verified, status, verify_code
    ) VALUES (
        ?,?,?,?,?,?,
        ?,?,?,?,
        ?,?,
        'resident', 0, 'pending', ?
    )
");

$stmt->execute([
    $first_name,
    $middle_name,
    $last_name,
    $gender,
    $birthdate,
    $civil_status,
    $purok,
    $barangay,
    $municipality,
    $province,
    $email,
    $password,
    $code
]);

// send verification email
$link = "http://localhost/barangay/auth/verify.php?code=$code";

$mail->clearAddresses();
$mail->addAddress($email);
$mail->Subject = "Barangay System - Email Verification";
$mail->isHTML(true);
$mail->Body = "
    <h3>Email Verification</h3>
    <p>Please click the link below to verify your email address:</p>
    <a href='$link'>$link</a>
    <br><br>
    <p><b>Note:</b> After verification, your account will still need admin approval.</p>
";

try {
    $mail->send();
} catch (Exception $e) {
    $_SESSION['msg'] = "❌ Registration saved but email failed. Contact admin.";
    header("Location: register.php");
    exit;
}

$_SESSION['msg'] = "✅ Registered successfully! Please check your email for verification. After that, wait for admin approval.";
header("Location: register.php");
exit;
