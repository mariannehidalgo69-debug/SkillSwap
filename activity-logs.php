
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

$stmt = $conn->query("
    SELECT 
        activity_logs.log_id,
        users.full_name,
        activity_logs.action,
        activity_logs.details,
        activity_logs.created_at
    FROM activity_logs
    LEFT JOIN users ON activity_logs.user_id = users.user_id
    ORDER BY activity_logs.created_at DESC
");

$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs | SkillSwap</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f6ff;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        h1 {
            color: #635bdb;
        }

        .table-container {
            overflow-x: auto;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 12px #0001;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #635bdb;
            color: white;
        }

        a {
            color: #635bdb;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>

<body>
<div class="container">

    <h1>Activity Logs</h1>
    <p>Monitor activities performed by SkillSwap users.</p>

    <p><a href="admin-dashboard.php">← Back to Admin Dashboard</a></p>

    <div class="table-container">
        <table>
            <tr>
                <th>Log ID</th>
                <th>User</th>
                <th>Action</th>
                <th>Details</th>
                <th>Date and Time</th>
            </tr>

            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= htmlspecialchars($log["log_id"]) ?></td>
                    <td><?= htmlspecialchars($log["full_name"] ?? "Deleted User") ?></td>
                    <td><?= htmlspecialchars($log["action"]) ?></td>
                    <td><?= htmlspecialchars($log["details"] ?? "") ?></td>
                    <td><?= htmlspecialchars($log["created_at"]) ?></td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="5">No activity logs found.</td>
                </tr>
            <?php endif; ?>

        </table>
    </div>

</div>
</body>
</html>
