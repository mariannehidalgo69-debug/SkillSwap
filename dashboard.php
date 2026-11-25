
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$name = htmlspecialchars($_SESSION["full_name"]);
$role = $_SESSION["role_name"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard | SkillSwap</title>

<style>
* {
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    margin: 0;
    background: #f5f6ff;
    color: #252943;
}

header {
    background: white;
    padding: 20px 8%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 10px #0001;
}

.logo {
    color: #635bdb;
    font-size: 25px;
    font-weight: bold;
}

header a {
    color: #635bdb;
    text-decoration: none;
    font-weight: bold;
}

.container {
    padding: 40px 8%;
}

.welcome {
    background: linear-gradient(135deg, #635bdb, #9188f5);
    color: white;
    padding: 35px;
    border-radius: 15px;
}

.welcome h1 {
    margin-top: 0;
}

.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.card {
    display: block;
    text-decoration: none;
    color: inherit;
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 3px 12px #0001;
}

.card h3 {
    color: #635bdb;
}

.card p {
    color: #777;
}

@media(max-width: 600px) {
    header {
        padding: 18px;
    }

    .container {
        padding: 25px 18px;
    }
}
</style>
</head>

<body>

<header>
    <div class="logo">SkillSwap.</div>
    <a href="logout.php">Logout</a>
</header>

<div class="container">

    <section class="welcome">
        <h1>Welcome, <?= $name ?>!</h1>
        <p>You are logged in as: <strong><?= htmlspecialchars($role) ?></strong></p>
        <p>Learn new skills, share your knowledge, and grow together!</p>
    </section>

    <h2>Your Dashboard</h2>

<div class="cards">

    <a href="my-skills.php" class="card">
        <h3>🎯 My Skills</h3>
        <p>Manage the skills you can teach.</p>
    </a>

    <a href="sessions.php" class="card">
        <h3>📅 My Sessions</h3>
        <p>View and manage your learning sessions.</p>
    </a>

    <a href="progress.php" class="card">
        <h3>📈 My Progress</h3>
        <p>Track your learning journey.</p>
    </a>

    <a href="feedback.php" class="card">
        <h3>⭐ Feedback</h3>
        <p>View your learning feedback.</p>
    </a>

    <a href="mentor-requests.php" class="card">
        <h3>📨 Incoming Requests</h3>
        <p>View and approve student session requests.</p>
    </a>

    <a href="mentor-progress.php" class="card">
        <h3>📚 Manage Student Progress</h3>
        <p>Record progress and complete sessions.</p>
    </a>
<a href="browse-mentors.php" class="card">
    <h3>Browse Mentors</h3>
    <p>Find students offering skills and request learning sessions.</p>
</a>
    <a href="browse-students.php" class="card">
        <h3>Browse Students</h3>
        <p>Find students offering skills and request learning sessions.</p>
    </a>
</div>

</body>
</html>
