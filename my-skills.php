
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "config/database.php";

$user_id = $_SESSION["user_id"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $skill_id = (int)($_POST["skill_id"] ?? 0);
    $experience = trim($_POST["experience_level"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if ($skill_id <= 0 || $experience === "" || $description === "") {
        $message = "Please complete all fields.";
    } else {
        $check = $conn->prepare(
            "SELECT skill_id FROM skills WHERE skill_id = ?"
        );
        $check->execute([$skill_id]);

        if (!$check->fetch()) {
            $message = "Please select a valid skill.";
        } else {
            $duplicate = $conn->prepare(
                "SELECT mentor_skill_id FROM mentor_skills
                 WHERE mentor_id = ? AND skill_id = ?"
            );
            $duplicate->execute([$user_id, $skill_id]);

            if ($duplicate->fetch()) {
                $message = "You have already added this skill.";
            } else {
                $insert = $conn->prepare(
                    "INSERT INTO mentor_skills
                    (mentor_id, skill_id, experience_level, description)
                    VALUES (?, ?, ?, ?)"
                );

                $insert->execute([
                    $user_id,
                    $skill_id,
                    $experience,
                    $description
                ]);

                $log = $conn->prepare(
                    "INSERT INTO activity_logs (user_id, action, details)
                     VALUES (?, ?, ?)"
                );
                $log->execute([
                    $user_id,
                    "Added Skill",
                    "Added a skill to their teaching profile."
                ]);

                header("Location: my-skills.php?added=1");
                exit;
            }
        }
    }
}

if (isset($_GET["added"])) {
    $message = "Your skill was added successfully!";
}

$skillList = $conn->query(
    "SELECT skill_id, skill_name FROM skills ORDER BY skill_name"
)->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare(
    "SELECT ms.experience_level, ms.description, s.skill_name
     FROM mentor_skills ms
     JOIN skills s ON ms.skill_id = s.skill_id
     WHERE ms.mentor_id = ?
     ORDER BY s.skill_name"
);
$stmt->execute([$user_id]);
$mySkills = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Skills | SkillSwap</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f5ff;
            margin: 0;
            padding: 30px;
            color: #252943;
        }

        .container {
            max-width: 850px;
            margin: auto;
        }

        h1 {
            color: #6258df;
        }

        .card {
            background: white;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 10px #ddd;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            margin: 8px 0 16px;
            border: 1px solid #ccc;
            border-radius: 7px;
            box-sizing: border-box;
        }

        button {
            background: #6258df;
            color: white;
            padding: 12px 22px;
            border: none;
            border-radius: 7px;
            cursor: pointer;
        }

        a {
            color: #6258df;
            text-decoration: none;
            font-weight: bold;
        }

        .message {
            background: #eeeaff;
            padding: 12px;
            border-radius: 7px;
        }
    </style>
</head>

<body>
<div class="container">

    <h1>My Skills</h1>
    <p>Add the skills you can teach to other students.</p>

    <p><a href="dashboard.php">← Back to Dashboard</a></p>

    <?php if ($message !== ""): ?>
        <p class="message">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <div class="card">
        <h2>Add a Skill</h2>

        <form method="POST">
            <label for="skill_id">Choose a skill</label>
            <select name="skill_id" id="skill_id" required>
                <option value="">Select a skill</option>

                <?php foreach ($skillList as $skill): ?>
                    <option value="<?= (int)$skill["skill_id"] ?>">
                        <?= htmlspecialchars($skill["skill_name"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="experience_level">Experience Level</label>
            <select name="experience_level" id="experience_level" required>
                <option value="">Select experience level</option>
                <option value="Beginner">Beginner</option>
                <option value="Intermediate">Intermediate</option>
                <option value="Advanced">Advanced</option>
                <option value="Expert">Expert</option>
            </select>

            <label for="description">Description</label>
            <textarea
                name="description"
                id="description"
                rows="4"
                placeholder="Describe what you can teach..."
                required
            ></textarea>

            <button type="submit">Add My Skill</button>
        </form>
    </div>

    <h2>Skills I Can Teach</h2>

    <?php if (count($mySkills) > 0): ?>

        <?php foreach ($mySkills as $item): ?>
            <div class="card">
                <h3><?= htmlspecialchars($item["skill_name"]) ?></h3>
                <p>
                    <strong>Experience:</strong>
                    <?= htmlspecialchars($item["experience_level"]) ?>
                </p>
                <p><?= htmlspecialchars($item["description"]) ?></p>
            </div>
        <?php endforeach; ?>

    <?php else: ?>

        <div class="card">
            <p>You haven't added any skills yet.</p>
        </div>

    <?php endif; ?>

</div>
</body>
</html>
