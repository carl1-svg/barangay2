<?php
session_start();
include "../db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'resident') {
    die("Access denied");
}

$user_id = $_SESSION['user_id'];

/* DELETE (SOFT DELETE ONLY SELECTED) */
if (isset($_POST['delete_selected']) && !empty($_POST['notif_ids'])) {

    $ids = $_POST['notif_ids'];

    // Save deleted IDs for undo
    $_SESSION['last_deleted'] = $ids;

    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $conn->prepare("
        UPDATE notifications 
        SET deleted_at = NOW() 
        WHERE id IN ($placeholders) AND user_id=?
    ");

    $ids[] = $user_id;
    $stmt->execute($ids);
}

/* UNDO DELETE (ONLY LAST DELETED) */
if (isset($_POST['undo_delete']) && isset($_SESSION['last_deleted'])) {

    $ids = $_SESSION['last_deleted'];
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $conn->prepare("
        UPDATE notifications 
        SET deleted_at = NULL 
        WHERE id IN ($placeholders) AND user_id=?
    ");

    $ids[] = $user_id;
    $stmt->execute($ids);

    unset($_SESSION['last_deleted']);
}

/* Mark unread as read */
$conn->prepare("
    UPDATE notifications 
    SET is_read=1 
    WHERE user_id=? AND is_read=0
")->execute([$user_id]);

/* FETCH NOT DELETED ONLY */
$stmt = $conn->prepare("
    SELECT * FROM notifications 
    WHERE user_id=? AND deleted_at IS NULL
    ORDER BY created_at DESC
");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<title>My Notifications</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-100">

<h1 class="text-2xl font-bold mb-4">🔔 Notifications</h1>

<?php if (isset($_SESSION['last_deleted'])): ?>
<div class="bg-yellow-100 border border-yellow-400 p-4 rounded mb-4 flex justify-between items-center">
    <span>Notifications deleted.</span>
    <form method="POST">
        <button name="undo_delete"
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-1 rounded text-sm">
            Undo
        </button>
    </form>
</div>
<?php endif; ?>

<form method="POST">

<div class="mb-4 flex items-center gap-4">
    <label class="flex items-center gap-2 cursor-pointer">
        <input type="checkbox" id="selectAll">
        <span class="text-sm font-medium">Select All</span>
    </label>

    <button 
        type="submit" 
        name="delete_selected"
        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm">
        Delete Selected
    </button>
</div>

<div class="space-y-3">

<?php if (count($notifications) > 0): ?>
    
    <?php foreach ($notifications as $n): ?>
        <div class="bg-white p-4 rounded shadow flex gap-3 items-start">
            
            <input type="checkbox" 
                   name="notif_ids[]" 
                   value="<?= $n['id'] ?>" 
                   class="mt-1 notifCheckbox">

            <div>
                <p><?= htmlspecialchars($n['message']) ?></p>
                <span class="text-xs text-gray-500">
                    <?= htmlspecialchars($n['created_at']) ?>
                </span>
            </div>

        </div>
    <?php endforeach; ?>

<?php else: ?>

    <div class="bg-white p-4 rounded shadow text-gray-500">
        No notifications found.
    </div>

<?php endif; ?>

</div>
</form>

<script>
// Select All
document.getElementById('selectAll').addEventListener('click', function() {
    const checkboxes = document.querySelectorAll('.notifCheckbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>

</body>
</html>
