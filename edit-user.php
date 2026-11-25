
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

$user_id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$user_id) {
    die("Invalid user ID.");
}

$stmt = $conn->prepare("
    SELECT user_id, full_name, email, role_id
    FROM users
    WHERE user_id = ?
");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_role = filter_input(INPUT_POST, "role_id", FILTER_VALIDATE_INT);

    if ($new_role && in_array($new_role, [1, 2, 3], true)) {
        $update = $conn->prepare("
            UPDATE users SET role_id = ?
            WHERE user_id = ?
        ");
        $update->execute([$new_role, $user_id]);

        $log = $conn->prepare("
            INSERT INTO activity_logs (user_id, action, details)
            VALUES (?, 'Role Updated', ?)
        ");
        $log->execute([
            $_SESSION["user_id"],
            "Changed the role of user ID " . $user_id
        ]);

        header("Location: manage-users.php");
        exit;
    } else {
        $error = "Please select a valid role.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User Role | SkillSwap</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f6ff;
            padding: 30px;
        }

        .container {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 12px #0001;
        }

        h1 {
            color: #635bdb;
        }

        select, button {
            width: 100%;
            padding: 12px;
            margin-top: 12px;
            border-radius: 8px;
        }

        button {
            background: #635bdb;
            color: white;
            border: none;
            cursor: pointer;
        }

        a {
            color: #635bdb;
            text-decoration: none;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
<div class="container">

    <h1>Edit User Role</h1>

    <p><strong>Name:</strong> <?= htmlspecialchars($user["full_name"]) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user["email"]) ?></p>

    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="role_id">Select Role:</label>

        <select name="role_id" id="role_id" required>
            <option value="1" <?= $user["role_id"] == 1 ? "selected" : "" ?>>Admin</option>
            <option value="2" <?= $user["role_id"] == 2 ? "selected" : "" ?>>Student</option>
            <option value="3" <?= $user["role_id"] == 3 ? "selected" : "" ?>>Mentor</option>
        </select>

        <button type="submit">Save Changes</button>
    </form>

    <p><a href="manage-users.php">← Back to Manage Users</a></p>

</div>
</body>
</html>
