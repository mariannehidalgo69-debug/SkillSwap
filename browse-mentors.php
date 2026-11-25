
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
    $mentor_skill_id = (int)($_POST["mentor_skill_id"] ?? 0);
    $session_date = $_POST["session_date"] ?? "";
    $request_message = trim($_POST["message"] ?? "");

    if ($mentor_skill_id <= 0 || $session_date === "" || $request_message === "") {
        $message = "Please complete all fields.";
    } else {
        $check = $conn->prepare(
            "SELECT mentor_skill_id, mentor_id
             FROM mentor_skills
             WHERE mentor_skill_id = ?"
        );
        $check->execute([$mentor_skill_id]);
        $mentorSkill = $check->fetch(PDO::FETCH_ASSOC);

        if (!$mentorSkill || $mentorSkill["mentor_id"] == $user_id) {
            $message = "Invalid skill selection.";
        } elseif (strtotime($session_date) === false || $session_date < date("Y-m-d")) {
            $message = "Please choose a valid future date.";
        } else {
            $insert = $conn->prepare(
                "INSERT INTO session_requests
                (student_id, mentor_skill_id, session_date, message, status)
                VALUES (?, ?, ?, ?, 'pending')"
            );

            $insert->execute([
                $user_id,
                $mentor_skill_id,
                $session_date,
                $request_message
            ]);

            $log = $conn->prepare(
                "INSERT INTO activity_logs (user_id, action, details)
                 VALUES (?, ?, ?)"
            );

            $log->execute([
                $user_id,
                "Requested Session",
                "Submitted a new learning session request."
            ]);

            header("Location: browse-mentors.php?requested=1");
            exit;
        }
    }
}

if (isset($_GET["requested"])) {
    $message = "Your session request was submitted successfully!";
}

$sql = "SELECT
            ms.mentor_skill_id,
            ms.experience_level,
            ms.description,
            s.skill_name,
            u.full_name AS mentor_name
        FROM mentor_skills ms
        JOIN skills s ON ms.skill_id = s.skill_id
        JOIN users u ON ms.mentor_id = u.user_id
        WHERE ms.mentor_id != ?
        ORDER BY s.skill_name";

$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$mentors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Mentors | SkillSwap</title>

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

        .mentor-card {
            background: white;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 10px #ddd;
        }

        input, textarea {
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

        .experience {
            color: #6258df;
            font-weight: bold;
        }
    </style>
</head>

<body>
<div class="container">

    <h1>Browse Mentors</h1>
    <p>Find a skill you want to learn and request a session.</p>

    <p><a href="dashboard.php">← Back to Dashboard</a></p>

    <?php if ($message !== ""): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if (count($mentors) > 0): ?>

        <?php foreach ($mentors as $mentor): ?>

            <div class="mentor-card">

                <h2><?= htmlspecialchars($mentor["skill_name"]) ?></h2>

                <p>
                    <strong>Mentor:</strong>
                    <?= htmlspecialchars($mentor["mentor_name"]) ?>
                </p>

                <p class="experience">
                    <?= htmlspecialchars($mentor["experience_level"]) ?>
                </p>

                <p><?= htmlspecialchars($mentor["description"]) ?></p>

                <form method="POST">

                    <input
                        type="hidden"
                        name="mentor_skill_id"
                        value="<?= (int)$mentor["mentor_skill_id"] ?>"
                    >

                    <label>Preferred Session Date</label>
                    <input
                        type="date"
                        name="session_date"
                        min="<?= date("Y-m-d") ?>"
                        required
                    >

                    <label>Your Message</label>
                    <textarea
                        name="message"
                        rows="3"
                        placeholder="Tell the mentor what you want to learn..."
                        required
                    ></textarea>

                    <button type="submit">Request Session</button>

                </form>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="mentor-card">
            <h3>No mentors available yet.</h3>
            <p>Other students' offered skills will appear here.</p>
        </div>

    <?php endif; ?>

</div>
</body>
</html>
