
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "config/database.php";

$user_id = $_SESSION["user_id"];

$sql = "SELECT
            pu.progress_note,
            pu.progress_date,
            sr.session_date,
            sr.status,
            s.skill_name
        FROM progress_updates pu
        JOIN session_requests sr
            ON pu.request_id = sr.request_id
        JOIN mentor_skills ms
            ON sr.mentor_skill_id = ms.mentor_skill_id
        JOIN skills s
            ON ms.skill_id = s.skill_id
        WHERE sr.student_id = ?
        ORDER BY pu.progress_date DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$progress = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Progress | SkillSwap</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f5ff;
            margin: 0;
            padding: 30px;
            color: #252943;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        h1 {
            color: #6258df;
        }

        .progress-card {
            background: white;
            padding: 22px;
            margin-bottom: 18px;
            border-radius: 12px;
            box-shadow: 0 3px 10px #ddd;
        }

        a {
            color: #6258df;
            text-decoration: none;
            font-weight: bold;
        }

        .status {
            color: #6258df;
            font-weight: bold;
            text-transform: capitalize;
        }
    </style>
</head>

<body>
<div class="container">

    <h1>My Progress 📈</h1>
    <p>Track your learning journey and achievements.</p>

    <p><a href="dashboard.php">← Back to Dashboard</a></p>

    <?php if (count($progress) > 0): ?>

        <?php foreach ($progress as $item): ?>
            <div class="progress-card">

                <h3>
                    <?= htmlspecialchars($item["skill_name"]) ?>
                </h3>

                <p>
                    <strong>Progress Update:</strong>
                    <?= htmlspecialchars($item["progress_note"]) ?>
                </p>

                <p>
                    <strong>Progress Date:</strong>
                    <?= htmlspecialchars($item["progress_date"]) ?>
                </p>

                <p>
                    <strong>Session Status:</strong>
                    <span class="status">
                        <?= htmlspecialchars($item["status"]) ?>
                    </span>
                </p>

            </div>
        <?php endforeach; ?>

    <?php else: ?>

        <div class="progress-card">
            <h3>No progress updates yet.</h3>
            <p>Your learning progress will appear here once updates are added.</p>
        </div>

    <?php endif; ?>

</div>
</body>
</html>
