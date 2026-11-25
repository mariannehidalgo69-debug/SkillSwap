
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
    SELECT users.user_id, users.full_name, users.email,
           roles.role_name, users.created_at
    FROM users
    JOIN roles ON users.role_id = roles.role_id
    ORDER BY users.user_id DESC
");

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | SkillSwap</title>

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

    <h1>Manage Users</h1>
    <p>View all registered SkillSwap accounts.</p>

    <p><a href="admin-dashboard.php">← Back to Admin Dashboard</a></p>

    <div class="table-container">
        <table>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Date Registered</th>
                <th>Action</th>
            </tr>

            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user["user_id"]) ?></td>
                    <td><?= htmlspecialchars($user["full_name"]) ?></td>
                    <td><?= htmlspecialchars($user["email"]) ?></td>
                    <td><?= htmlspecialchars($user["role_name"]) ?></td>
                    <td><?= htmlspecialchars($user["role_name"]) ?></td>
                    <td><?= htmlspecialchars($user["created_at"]) ?></td>
                    <td>
    <a href="edit-user.php?id=<?= $user['user_id'] ?>">Edit Role</a>

    <?php if ($user["user_id"] != $_SESSION["user_id"]): ?>
        <form action="delete-user.php" method="POST"
              onsubmit="return confirm('Are you sure you want to delete this user?');">
            <input type="hidden" name="user_id"
                   value="<?= $user['user_id'] ?>">
            <button type="submit">Delete</button>
        </form>
    <?php endif; ?>
</td>

            <?php endforeach; ?>

        </table>
    </div>

</div>
</body>
</html>
