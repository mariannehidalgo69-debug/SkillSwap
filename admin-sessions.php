
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
        session_requests.request_id,
        student.full_name AS student_name,
        mentor.full_name AS mentor_name,
        skills.skill_name,
        session_requests.session_date,
        session_requests.status
    FROM session_requests
    JOIN users AS student
        ON session_requests.student_id = student.user_id
    JOIN mentor_skills
        ON session_requests.mentor_skill_id = mentor_skills.mentor_skill_id
    JOIN users AS mentor
        ON mentor_skills.mentor_id = mentor.user_id
    JOIN skills
        ON mentor_skills.skill_id = skills.skill_id
    ORDER BY session_requests.created_at DESC
");

$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Session Requests - SkillSwap</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h1>All Session Requests</h1>

<p>View all student and mentor session requests.</p>

<table border="1" cellpadding="10">
    <tr>
        <th>Request ID</th>
        <th>Student</th>
        <th>Mentor</th>
        <th>Skill</th>
        <th>Session Date</th>
        <th>Status</th>
    </tr>

    <?php foreach ($sessions as $session): ?>
        <tr>
            <td><?= htmlspecialchars($session["request_id"]) ?></td>
            <td><?= htmlspecialchars($session["student_name"]) ?></td>
            <td><?= htmlspecialchars($session["mentor_name"]) ?></td>
            <td><?= htmlspecialchars($session["skill_name"]) ?></td>
            <td><?= htmlspecialchars($session["session_date"]) ?></td>
            <td><?= htmlspecialchars($session["status"]) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<br>

<a href="admin-dashboard.php">Back to Admin Dashboard</a>

</body>
</html>
