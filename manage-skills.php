
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

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $skill_name = trim($_POST["skill_name"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if ($skill_name === "" || $description === "") {
        $message = "Please fill in all fields.";
    } else {
        $check = $conn->prepare(
            "SELECT skill_id FROM skills WHERE skill_name = ?"
        );
        $check->execute([$skill_name]);

        if ($check->fetch()) {
            $message = "This skill already exists.";
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO skills (skill_name, description)
                 VALUES (?, ?)"
            );
            $stmt->execute([$skill_name, $description]);

            $log = $conn->prepare("
                INSERT INTO activity_logs (user_id, action, details)
                VALUES (?, 'Skill Added', ?)
            ");
            $log->execute([
                $_SESSION["user_id"],
                "Added skill: " . $skill_name
            ]);

            header("Location: manage-skills.php?added=success");
            exit;
        }
    }
}

if (isset($_GET["added"])) {
    $message = "Skill added successfully!";
}

$stmt = $conn->query(
    "SELECT * FROM skills ORDER BY skill_name ASC"
);
$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Skills - SkillSwap</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h1>Manage Skills</h1>

<p>Add and view the skills available on SkillSwap.</p>

<?php if ($message !== ""): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<h2>Add New Skill</h2>

<form method="POST">
    <label>Skill Name</label><br>
    <input type="text" name="skill_name" required>
    <br><br>

    <label>Description</label><br>
    <textarea name="description" required></textarea>
    <br><br>

    <button type="submit">Add Skill</button>
</form>

<hr>

<h2>Available Skills</h2>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Skill Name</th>
        <th>Description</th>
        <th>Action</th>
    </tr>

    <?php foreach ($skills as $skill): ?>
        <tr>
            <td><?= htmlspecialchars($skill["skill_id"]) ?></td>
            <td><?= htmlspecialchars($skill["skill_name"]) ?></td>
            <td><?= htmlspecialchars($skill["description"]) ?></td>

<td>
    <a href="edit-skill.php?id=<?= $skill['skill_id'] ?>">
        Edit
    </a>

    <form action="delete-skill.php" method="POST"
          onsubmit="return confirm('Are you sure you want to delete this skill?');">

        <input type="hidden" name="skill_id"
               value="<?= $skill['skill_id'] ?>">

        <button type="submit">Delete</button>
    </form>
</td>
        </tr>
    <?php endforeach; ?>
</table>

<br>

<a href="admin-dashboard.php">Back to Admin Dashboard</a>

</body>
</html>
