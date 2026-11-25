
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

$totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalSkills = $conn->query("SELECT COUNT(*) FROM skills")->fetchColumn();
$totalSessions = $conn->query("SELECT COUNT(*) FROM session_requests")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | SkillSwap</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f6ff;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        h1 {
            color: #635bdb;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px #0001;
        }

        .number {
            font-size: 32px;
            font-weight: bold;
            color: #635bdb;
        }

        a {
            color: #635bdb;
            text-decoration: none;
        }
    </style>
</head>

<body>
<div class="container">

    <h1>SkillSwap Admin Dashboard</h1>

    <p>Welcome, <?= htmlspecialchars($_SESSION["full_name"]) ?>!</p>

<div class="cards">

    <div class="card">
        <h3>Total Users</h3>
        <p class="number"><?= $totalUsers ?></p>
    </div>

    <div class="card">
        <h3>Total Skills</h3>
        <p class="number"><?= $totalSkills ?></p>
    </div>

    <div class="card">
        <h3>Total Sessions</h3>
        <p class="number"><?= $totalSessions ?></p>
    </div>

    <a href="admin-sessions.php" class="card">
        <h3>All Session Requests</h3>
        <p>View all student and mentor session requests.</p>
    </a>

    <a href="manage-skills.php" class="card">
        <h3>Manage Skills</h3>
        <p>Add, edit, delete, and view available skills.</p>
    </a>

    <a href="manage-users.php" class="card">
        <h3>👥 Manage Users</h3>
        <p>View all registered Student, Mentor, and Admin accounts.</p>
    </a>

    <a href="activity-logs.php" class="card">
    <h3>📋 Activity Logs</h3>
    <p>Monitor activities performed by SkillSwap users.</p>

</a>
</div>
    
<p><a href="dashboard.php">← Back to Dashboard</a></p>
    <p><a href="logout.php">Logout</a></p>

</div>
</body>
</html>
