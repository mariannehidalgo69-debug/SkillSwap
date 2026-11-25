
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

$skill_id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$skill_id) {
    header("Location: manage-skills.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM skills WHERE skill_id = ?");
$stmt->execute([$skill_id]);
$skill = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$skill) {
    header("Location: manage-skills.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $skill_name = trim($_POST["skill_name"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if ($skill_name === "" || $description === "") {
        $message = "Please fill in all fields.";
    } else {
        $check = $conn->prepare(
            "SELECT skill_id FROM skills
             WHERE skill_name = ? AND skill_id != ?"
        );
        $check->execute([$skill_name, $skill_id]);

        if ($check->fetch()) {
            $message = "That skill name already exists.";
        } else {
            $update = $conn->prepare(
                "UPDATE skills
                 SET skill_name = ?, description = ?
                 WHERE skill_id = ?"
            );
            $update->execute([
                $skill_name,
                $description,
                $skill_id
            ]);

            $log = $conn->prepare("
                INSERT INTO activity_logs (user_id, action, details)
                VALUES (?, 'Skill Updated', ?)
            ");
            $log->execute([
                $_SESSION["user_id"],
                "Updated skill: " . $skill_name
            ]);

            header("Location: manage-skills.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Skill - SkillSwap</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h1>Edit Skill</h1>

<?php if ($message !== ""): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form method="POST">

    <label>Skill Name</label><br>
    <input
        type="text"
        name="skill_name"
        value="<?= htmlspecialchars($skill["skill_name"]) ?>"
        required
    >

    <br><br>

    <label>Description</label><br>
    <textarea name="description" required><?= htmlspecialchars($skill["description"]) ?></textarea>

    <br><br>

    <button type="submit">Save Changes</button>

</form>

<br>

<a href="manage-skills.php">Back to Manage Skills</a>

</body>
</html>
