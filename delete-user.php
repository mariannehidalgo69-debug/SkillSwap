
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["role_id"] != 1) {
    header("Location: dashboard.php");
    exit;
}

require_once "config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: manage-users.php");
    exit;
}

$user_id = filter_input(INPUT_POST, "user_id", FILTER_VALIDATE_INT);

// Prevent deleting your own Admin account
if (!$user_id || $user_id == $_SESSION["user_id"]) {
    header("Location: manage-users.php");
    exit;
}

try {
    $stmt = $conn->prepare(
        "DELETE FROM users WHERE user_id = ?"
    );
    $stmt->execute([$user_id]);

    $log = $conn->prepare("
        INSERT INTO activity_logs (user_id, action, details)
        VALUES (?, 'User Deleted', ?)
    ");

    $log->execute([
        $_SESSION["user_id"],
        "Deleted user ID " . $user_id
    ]);

    header("Location: manage-users.php?deleted=success");
    exit;

} catch (PDOException $e) {
    header("Location: manage-users.php?deleted=failed");
    exit;
}
