
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "config/database.php";

$user_id = $_SESSION["user_id"];

$sql = "SELECT
            f.rating,
            f.comment,
            f.created_at,
            s.skill_name
        FROM feedback f
        JOIN session_requests sr
            ON f.request_id = sr.request_id
        JOIN mentor_skills ms
            ON sr.mentor_skill_id = ms.mentor_skill_id
        JOIN skills s
            ON ms.skill_id = s.skill_id
        WHERE sr.student_id = ?
        ORDER BY f.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback | SkillSwap</title>

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

        .feedback-card {
            background: white;
            padding: 22px;
            margin-bottom: 18px;
            border-radius: 12px;
            box-shadow: 0 3px 10px #ddd;
        }

        .rating {
            color: #e5a000;
            font-size: 20px;
        }

        a {
            color: #6258df;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>

<body>
<div class="container">

    <h1>⭐ My Feedback</h1>
    <p>View the feedback you have shared about your learning sessions.</p>

    <p><a href="dashboard.php">← Back to Dashboard</a></p>

    <?php if (count($feedbacks) > 0): ?>

        <?php foreach ($feedbacks as $item): ?>
            <div class="feedback-card">

                <h3><?= htmlspecialchars($item["skill_name"]) ?></h3>

                <p class="rating">
                    <?= str_repeat("★", (int)$item["rating"]) ?>
                    <?= str_repeat("☆", 5 - (int)$item["rating"]) ?>
                </p>

                <p>
                    <?= htmlspecialchars($item["comment"] ?? "No comment") ?>
                </p>

                <small>
                    Submitted:
                    <?= htmlspecialchars($item["created_at"]) ?>
                </small>

            </div>
        <?php endforeach; ?>

    <?php else: ?>

        <div class="feedback-card">
            <h3>No feedback yet.</h3>
            <p>Your submitted feedback will appear here.</p>
        </div>

    <?php endif; ?>

</div>
</body>
</html>
