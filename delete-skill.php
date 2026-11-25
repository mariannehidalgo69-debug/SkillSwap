
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
    header("Location: manage-skills.php");
    exit;
}

$skill_id = filter_input(INPUT_POST, "skill_id", FILTER_VALIDATE_INT);

if (!$skill_id) {
    header("Location: manage-skills.php");
    exit;
}

try {
    $stmt = $conn->prepare(
        "SELECT skill_name FROM skills WHERE skill_id = ?"
    );
    $stmt->execute([$skill_id]);
    $skill = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$skill) {
        header("Location: manage-skills.php");
        exit;
    }

    $delete = $conn->prepare(
        "DELETE FROM skills WHERE skill_id = ?"
    );
    $delete->execute([$skill_id]);

    $log = $conn->prepare("
        INSERT INTO activity_logs (user_id, action, details)
        VALUES (?, 'Skill Deleted', ?)
    ");

    $log->execute([
        $_SESSION["user_id"],
        "Deleted skill: " . $skill["skill_name"]
    ]);

    header("Location: manage-skills.php?deleted=success");
    exit;

} catch (PDOException $e) {
    header("Location: manage-skills.php?deleted=failed");
    exit;
}
