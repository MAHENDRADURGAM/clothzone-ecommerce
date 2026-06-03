<?php
session_start();
include 'db.php';

$message = "";

if (isset($_POST['signup'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if user already exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        $message = "Username already taken!";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO `users` (`username`, `password`) VALUES (?, ?)");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
} else {
    // prepare failed; fallback to original query
    mysqli_query($conn, "INSERT INTO users (username, password) VALUES ('$username', '$password')" );
}
        mysqli_query($conn, $sql);
        $message = "Account created! You can now login.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>User Signup</title>
<style>
    body {
        font-family: Arial;
        background: #f4f4f4;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .form-box {
        width: 350px;
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }
    input {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
    }
    button {
        width: 100%;
        padding: 12px;
        background: black;
        color: white;
        border: none;
        border-radius: 5px;
    }
    .msg {
        text-align: center;
        color: red;
        margin-bottom: 10px;
    }
</style>
</head>

<body>

<div class="form-box">
    <h2 style="text-align:center;">Signup</h2>

    <?php if ($message != "") { echo "<p class='msg'>$message</p>"; } ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Create Username" required>
        <input type="password" name="password" placeholder="Create Password" required>
        <button name="signup">Create Account</button>
    </form>

    <p style="text-align:center;margin-top:10px;">
        Already have an account? <a href="login.php">Login</a>
    </p>
</div>

</body>
</html>
