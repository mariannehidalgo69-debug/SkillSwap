
<?php
require_once "config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if ($full_name === "" || $email === "" || $password === "") {
        $message = "Please complete all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        $check = $conn->prepare(
            "SELECT user_id FROM users WHERE email = ?"
        );
        $check->execute([$email]);

        if ($check->fetch()) {
            $message = "This email is already registered.";
        } else {
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO users
                (role_id, full_name, email, password)
                VALUES (?, ?, ?, ?)"
            );

            $stmt->execute([
                2,
                $full_name,
                $email,
                $hashed_password
            ]);

            $message = "Registration successful! You can now log in.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register | SkillSwap</title>
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
    margin: 9px 0;
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
    background: #eeeaff;
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
    <h1>Join SkillSwap</h1>
    <p>Create your student account and start learning!</p>

    <?php if ($message !== ""): ?>
        <p class="message">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="full_name"
            placeholder="Full Name" required>

        <input type="email" name="email"
            placeholder="Email Address" required>

        <input type="password" name="password"
            placeholder="Password (at least 8 characters)"
            minlength="8" required>

        <input type="password" name="confirm_password"
            placeholder="Confirm Password" required>

        <button type="submit">Create Account</button>
    </form>

    <p>Already registered? <a href="login.php">Login here</a></p>
    <p><a href="index.php">Back to Home</a></p>
</div>

</body>
</html>
