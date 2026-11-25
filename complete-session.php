
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["role_id"] != 3) {
    die("Access denied. Mentor accounts only.");
}

require_once "config/database.php";

$mentor_id = $_SESSION["user_id"];
$request_id = $_POST["request_id"] ?? 0;

$stmt = $conn->prepare("
    UPDATE session_requests sr
    JOIN mentor_skills ms
        ON sr.mentor_skill_id = ms.mentor_skill_id
    SET sr.status = 'completed'
    WHERE sr.request_id = ?
    AND ms.mentor_id = ?
    AND sr.status = 'approved'
");

$stmt->execute([$request_id, $mentor_id]);

if ($stmt->rowCount() > 0) {
    $log = $conn->prepare("
        INSERT INTO activity_logs (user_id, action, details)
        VALUES (?, ?, ?)
    ");

    $log->execute([
        $mentor_id,
        "Session completed",
        "Request ID: " . $request_id
    ]);

    header("Location: mentor-progress.php?completed=1");
    exit;
}

header("Location: mentor-progress.php?completed=0");
exit;
?>
