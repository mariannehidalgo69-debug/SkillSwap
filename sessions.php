
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "config/database.php";

$user_id = $_SESSION["user_id"];

$sql = "SELECT
            sr.request_id,
            sr.session_date,
            sr.message,
            sr.status,
            s.skill_name,
            u.full_name AS mentor_name
        FROM session_requests sr
        JOIN mentor_skills ms
            ON sr.mentor_skill_id = ms.mentor_skill_id
        JOIN skills s
            ON ms.skill_id = s.skill_id
        JOIN users u
            ON ms.mentor_id = u.user_id
        WHERE sr.student_id = ?
        ORDER BY sr.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Sessions | SkillSwap</title>

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

        .session-card {
            background: white;
            padding: 22px;
            margin-bottom: 18px;
            border-radius: 12px;
            box-shadow: 0 3px 10px #ddd;
        }

        .status {
            font-weight: bold;
            text-transform: capitalize;
            color: #6258df;
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

    <h1>My Sessions</h1>
    <p>View your learning session requests.</p>

    <p><a href="dashboard.php">← Back to Dashboard</a></p>

    <?php if (count($sessions) > 0): ?>

        <?php foreach ($sessions as $session): ?>
            <div class="session-card">
                <h3>
                    <?= htmlspecialchars($session["skill_name"]) ?>
                </h3>

                <p>
                    <strong>Mentor:</strong>
                    <?= htmlspecialchars($session["mentor_name"]) ?>
                </p>

                <p>
                    <strong>Date:</strong>
                    <?= htmlspecialchars($session["session_date"]) ?>
                </p>

                <p>
                    <strong>Message:</strong>
                    <?= htmlspecialchars($session["message"] ?? "No message") ?>
                </p>

                <p>
                    <strong>Status:</strong>
                    <span class="status">
                        <?= htmlspecialchars($session["status"]) ?>
                    </span>
                </p>
            </div>
        <?php endforeach; ?>

    <?php else: ?>

        <div class="session-card">
            <h3>No sessions yet.</h3>
            <p>Your session requests will appear here.</p>
        </div>

    <?php endif; ?>

</div>
</body>
</html>
