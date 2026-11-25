
<?php
session_start();
require_once "config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare("
        SELECT users.*, roles.role_name
        FROM users
        JOIN roles ON users.role_id = roles.role_id
        WHERE users.email = ?
    ");

    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {

        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["full_name"] = $user["full_name"];
        $_SESSION["role_id"] = $user["role_id"];
        $_SESSION["role_name"] = $user["role_name"];

        $log = $conn->prepare("
            INSERT INTO activity_logs (user_id, action, details)
            VALUES (?, ?, ?)
        ");

        $log->execute([
            $user["user_id"],
            "Login",
            "User successfully logged in."
        ]);

        if ($_SESSION["role_id"] == 1) {
    header("Location: admin-dashboard.php");
} elseif ($_SESSION["role_id"] == 3) {
    header("Location: dashboard.php");
} else {
    header("Location: dashboard.php");
}
exit;

    } else {
        $message = "Incorrect email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | SkillSwap</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f0f2ff;
    margin: 0;
    padding: 40px 15px;
}
.container {
    max-width: 420px;
    margin: auto;
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 15px #0001;
}
h1 {
    color: #635bdb;
    text-align: center;
}
input, button {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    box-sizing: border-box;
    border-radius: 7px;
}
input {
    border: 1px solid #ccc;
}
button {
    background: #635bdb;
    color: white;
    border: none;
    cursor: pointer;
    font-weight: bold;
}
.message {
    background: #ffe5e5;
    padding: 12px;
    border-radius: 7px;
}
a {
    color: #635bdb;
}
</style>
</head>

<body>
<div class="container">

    <h1>Welcome Back!</h1>
    <p>Log in to your SkillSwap account.</p>

    <?php if ($message !== ""): ?>
        <p class="message">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email"
            placeholder="Email Address" required>

        <input type="password" name="password"
            placeholder="Password" required>

        <button type="submit">Login</button>
    </form>

    <p>Don't have an account?
        <a href="register.php">Register here</a>
    </p>

    <p><a href="index.php">Back to Home</a></p>

</div>
</body>
</html>
