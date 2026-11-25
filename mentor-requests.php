
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

// Approve or reject a session request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $request_id = $_POST["request_id"] ?? 0;
    $action = $_POST["action"] ?? "";

    if (in_array($action, ["approved", "rejected"])) {
        $check = $conn->prepare("
            SELECT sr.request_id
            FROM session_requests sr
            JOIN mentor_skills ms
                ON sr.mentor_skill_id = ms.mentor_skill_id
            WHERE sr.request_id = ?
            AND ms.mentor_id = ?
            AND sr.status = 'pending'
        ");
        $check->execute([$request_id, $mentor_id]);

        if ($check->fetch()) {
            $update = $conn->prepare("
                UPDATE session_requests
                SET status = ?
                WHERE request_id = ?
            ");
            $update->execute([$action, $request_id]);

            $log = $conn->prepare("
                INSERT INTO activity_logs (user_id, action, details)
                VALUES (?, ?, ?)
            ");
            $log->execute([
                $mentor_id,
                "Session request " . $action,
                "Request ID: " . $request_id
            ]);

            $message = "Request successfully " . $action . "!";
        }
    }
}

// Get requests sent to this mentor
$stmt = $conn->prepare("
    SELECT
        sr.request_id,
        sr.session_date,
        sr.message,
        sr.status,
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
    ORDER BY sr.created_at DESC
");
$stmt->execute([$mentor_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentor Requests - SkillSwap</title>
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
        .request {
            background: white;
            padding: 20px;
            margin-bottom: 18px;
            border-radius: 10px;
            box-shadow: 0 2px 8px #ddd;
        }
        button {
            padding: 10px 16px;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
            color: white;
            margin-right: 8px;
        }
        .approve { background: #198754; }
        .reject { background: #dc3545; }
        .status { font-weight: bold; }
        a { color: #3157c8; }
    </style>
</head>
<body>

<div class="container">
    <h1>Incoming Session Requests</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION["full_name"]); ?>!</p>
    <p><a href="dashboard.php">Back to Dashboard</a></p>

    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if (count($requests) > 0): ?>
        <?php foreach ($requests as $request): ?>
            <div class="request">
                <h3>
                    <?php echo htmlspecialchars($request["skill_name"]); ?>
                </h3>

                <p>
                    <strong>Student:</strong>
                    <?php echo htmlspecialchars($request["student_name"]); ?>
                </p>

                <p>
                    <strong>Session Date:</strong>
                    <?php echo htmlspecialchars($request["session_date"]); ?>
                </p>

                <p>
                    <strong>Message:</strong>
                    <?php echo htmlspecialchars($request["message"]); ?>
                </p>

                <p class="status">
                    Status: <?php echo htmlspecialchars($request["status"]); ?>
                </p>

                <?php if ($request["status"] === "pending"): ?>
                    <form method="POST">
                        <input type="hidden" name="request_id"
                               value="<?php echo $request["request_id"]; ?>">

                        <button class="approve" type="submit"
                                name="action" value="approved">
                            Approve
                        </button>

                        <button class="reject" type="submit"
                                name="action" value="rejected">
                            Reject
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No incoming session requests yet.</p>
    <?php endif; ?>
</div>

</body>
</html>
