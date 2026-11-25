
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "config/database.php";

$search = trim($_GET["search"] ?? "");

$stmt = $conn->prepare(
    "SELECT * FROM skills
     WHERE skill_name LIKE :search
     OR description LIKE :search
     ORDER BY skill_name ASC"
);

$stmt->execute([
    "search" => "%" . $search . "%"
]);

$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Skills | SkillSwap</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f5ff;
            margin: 0;
            padding: 30px;
        }

        h1 {
            color: #6258df;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        .skills {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .skill-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 10px #ddd;
        }

        .skill-card h3 {
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

    <h1>Explore Skills</h1>
    <p>Discover skills offered by fellow students.</p>

<form method="GET" action="skills.php">
    <input
        type="text"
        name="search"
        placeholder="Search for a skill..."
        value="<?= htmlspecialchars($search) ?>"
        style="padding: 12px; width: 70%; max-width: 400px; border: 1px solid #ccc; border-radius: 8px;"
    >

    <button
        type="submit"
        style="padding: 12px 20px; background: #6258df; color: white; border: none; border-radius: 8px; cursor: pointer;"
    >
        Search
    </button>
</form>

    <p><a href="dashboard.php">← Back to Dashboard</a></p>

    <div class="skills">
        <?php foreach ($skills as $skill): ?>
            <div class="skill-card">
                <h3>
                    <?= htmlspecialchars($skill["skill_name"]) ?>
                </h3>

                <p>
                    <?= htmlspecialchars($skill["description"] ?? "Learn something new with SkillSwap!") ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>

</div>
</body>
</html>
