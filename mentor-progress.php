
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
$message = "";

// Save a progress update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $request_id = $_POST["request_id"] ?? 0;
    $progress_note = trim($_POST["progress_note"] ?? "");

    if ($progress_note !== "") {
        $check = $conn->prepare("
            SELECT sr.request_id
            FROM session_requests sr
            JOIN mentor_skills ms
                ON sr.mentor_skill_id = ms.mentor_skill_id
            WHERE sr.request_id = ?
            AND ms.mentor_id = ?
            AND sr.status = 'approved'
        ");
        $check->execute([$request_id, $mentor_id]);

        if ($check->fetch()) {
            $insert = $conn->prepare("
                INSERT INTO progress_updates
                (request_id, updated_by, progress_note, progress_date)
                VALUES (?, ?, ?, NOW())
            ");
            $insert->execute([
                $request_id,
                $mentor_id,
                $progress_note
            ]);

            $log = $conn->prepare("
                INSERT INTO activity_logs (user_id, action, details)
                VALUES (?, ?, ?)
            ");
            $log->execute([
                $mentor_id,
                "Progress update added",
                "Request ID: " . $request_id
            ]);

            $message = "Progress update saved successfully!";
        } else {
            $message = "Invalid or unapproved session.";
        }
    } else {
        $message = "Please enter a progress note.";
    }
}

// Get approved sessions assigned to this mentor
$stmt = $conn->prepare("
    SELECT
        sr.request_id,
        sr.session_date,
        u.full_name AS student_name,
        s.skill_name
    FROM session_requests sr
    JOIN mentor_skills ms
        ON sr.mentor_skill_id = ms.mentor_skill_id
    JOIN users u
        ON sr.student_id = u.user_id
    JOIN skills s
        ON ms.skill_id = s.skill_id
    WHERE ms.mentor_id = ?
    AND sr.status = 'approved'
    ORDER BY sr.session_date DESC
");
$stmt->execute([$mentor_id]);
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentor Progress - SkillSwap</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6fb;
            margin: 0;
            padding: 30px;
        }
        .container {
            max-width: 850px;
            margin: auto;
        }
        .session {
            background: white;
            padding: 20px;
            margin-bottom: 18px;
            border-radius: 10px;
            box-shadow: 0 2px 8px #ddd;
        }
        textarea {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            margin-bottom: 10px;
            min-height: 90px;
        }
        button {
            padding: 10px 16px;
            background: #3157c8;
            color: white;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
        }
        a { color: #3157c8; }
    </style>
</head>
<body>

<div class="container">
    <h1>Student Progress Tracking</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION["full_name"]); ?>!</p>
    <p><a href="dashboard.php">Back to Dashboard</a></p>

    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if (count($sessions) > 0): ?>
        <?php foreach ($sessions as $session): ?>
            <div class="session">
                <h3><?php echo htmlspecialchars($session["skill_name"]); ?></h3>

                <p>
                    <strong>Student:</strong>
                    <?php echo htmlspecialchars($session["student_name"]); ?>
                </p>

                <p>
                    <strong>Session Date:</strong>
                    <?php echo htmlspecialchars($session["session_date"]); ?>
                </p>

                <form method="POST">
                    <input type="hidden" name="request_id"
                           value="<?php echo $session["request_id"]; ?>">

                    <label><strong>Progress Note:</strong></label>
                    <textarea name="progress_note"
                        placeholder="Describe what the student learned..."
                        required></textarea>

                    <button type="submit">Save Progress</button>
</form>>

<form method="POST" action="complete-session.php">
    <input type="hidden" name="request_id"
           value="<?php echo $session["request_id"]; ?>">

    <button type="submit"
            onclick="return confirm('Mark this session as completed?')">
        Mark as Completed
    </button>
</form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No approved sessions available.</p>
    <?php endif; ?>
</div>

</body>
</html>
