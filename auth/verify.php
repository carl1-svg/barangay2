<?php
session_start();
include "../db.php";

$code = $_GET['code'] ?? '';

// no code = invalid link
if (empty($code)) {
    header("Location: login.php?error=invalid_link");
    exit;
}

// check verification code
$stmt = $conn->prepare("
    SELECT id, is_verified 
    FROM users 
    WHERE verify_code=?
");
$stmt->execute([$code]);
$user = $stmt->fetch();

if ($user) {

    // if not yet verified
    if ($user['is_verified'] == 0) {

        $update = $conn->prepare("
            UPDATE users 
            SET is_verified=1, verify_code=NULL 
            WHERE verify_code=?
        ");
        $update->execute([$code]);

        // verified but still needs admin approval
        header("Location: login.php?verified=1");
        exit;

    } else {
        // already verified
        header("Location: login.php?verified=1");
        exit;
    }

} else {
    // invalid or expired link
    header("Location: login.php?error=invalid_link");
    exit;
}
