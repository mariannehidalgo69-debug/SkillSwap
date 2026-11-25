
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "config/database.php";

$student_id = $_SESSION["user_id"];
$message = "";

// Submit feedback
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $request_id = $_POST["request_id"] ?? 0;
    $rating = $_POST["rating"] ?? 0;
    $comment = trim($_POST["comment"] ?? "");

    if ($rating >= 1 && $rating <= 5 && $comment !== "") {

        $check = $conn->prepare("
            SELECT request_id
            FROM session_requests
            WHERE request_id = ?
            AND student_id = ?
            AND status = 'completed'
        ");
        $check->execute([$request_id, $student_id]);

        if ($check->fetch()) {
            try {
                $insert = $conn->prepare("
                    INSERT INTO feedback
                    (request_id, rating, comment)
                    VALUES (?, ?, ?)
                ");
                $insert->execute([$request_id, $rating, $comment]);

                $log = $conn->prepare("
                    INSERT INTO activity_logs (user_id, action, details)
                    VALUES (?, ?, ?)
                ");
                $log->execute([
                    $student_id,
                    "Feedback submitted",
                    "Request ID: " . $request_id
                ]);

                $message = "Feedback submitted successfully!";

            } catch (PDOException $e) {
                $message = "Feedback has already been submitted.";
            }
        } else {
            $message = "Invalid or incomplete session.";
        }

    } else {
        $message = "Please select a rating and enter a comment.";
    }
}

// Get completed sessions without feedback
$stmt = $conn->prepare("
    SELECT
        sr.request_id,
        sr.session_date,
        s.skill_name,
        u.full_name AS mentor_name
    FROM session_requests sr
    JOIN mentor_skills ms
        ON sr.mentor_skill_id = ms.mentor_skill_id
    JOIN skills s
        ON ms.skill_id = s.skill_id
    JOIN users u
        ON ms.mentor_id = u.user_id
    LEFT JOIN feedback f
        ON sr.request_id = f.request_id
    WHERE sr.student_id = ?
    AND sr.status = 'completed'
    AND f.request_id IS NULL
    ORDER BY sr.session_date DESC
");
$stmt->execute([$student_id]);
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback - SkillSwap</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6fb;
            padding: 30px;
        }
        .container {
            max-width: 750px;
            margin: auto;
        }
        .session {
            background: white;
            padding: 20px;
            margin-bottom: 18px;
            border-radius: 10px;
            box-shadow: 0 2px 8px #ddd;
        }
        select, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            box-sizing: border-box;
        }
        textarea {
            min-height: 90px;
        }
        button {
            background: #3157c8;
            color: white;
            padding: 10px 16px;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
        }
        a { color: #3157c8; }
    </style>
</head>
<body>

<div class="container">
    <h1>Submit Feedback</h1>
    <p><a href="dashboard.php">Back to Dashboard</a></p>

    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if (count($sessions) > 0): ?>
        <?php foreach ($sessions as $session): ?>
            <div class="session">
                <h3><?php echo htmlspecialchars($session["skill_name"]); ?></h3>

                <p>
                    <strong>Mentor:</strong>
                    <?php echo htmlspecialchars($session["mentor_name"]); ?>
                </p>

                <p>
                    <strong>Session Date:</strong>
                    <?php echo htmlspecialchars($session["session_date"]); ?>
                </p>

                <form method="POST">
                    <input type="hidden" name="request_id"
                           value="<?php echo $session["request_id"]; ?>">

                    <label><strong>Rating:</strong></label>
                    <select name="rating" required>
                        <option value="">Choose a rating</option>
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Very Good</option>
                        <option value="3">3 - Good</option>
                        <option value="2">2 - Fair</option>
                        <option value="1">1 - Poor</option>
                    </select>

                    <label><strong>Comment:</strong></label>
                    <textarea name="comment"
                        placeholder="Share your learning experience..."
                        required></textarea>

                    <button type="submit">Submit Feedback</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No completed sessions waiting for feedback.</p>
    <?php endif; ?>
</div>

</body>
</html>
