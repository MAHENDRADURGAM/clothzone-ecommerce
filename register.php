<?php
session_start();
include 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' LIMIT 1");
        if (mysqli_num_rows($check) > 0) {
            $error = "Email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql  = "INSERT INTO users (name, email, password) VALUES ('$name','$email','$hash')";
            mysqli_query($conn, $sql);
            header("Location: login.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Account - CLOTHZONE</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            background: #0f0f0f;                 /* dark page background */
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;             /* card near top */
            justify-content: center;
            padding-top: 80px;                   /* distance from top */
            box-sizing: border-box;
            color: #111;
        }
        .auth-card {
            width: 420px;
            max-width: 95%;
            background: #ffffff;                 /* white card */
            padding: 26px 24px 24px;
            border-radius: 16px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.55);
        }
        .auth-card h1 {
            margin: 0 0 8px;
            font-size: 26px;
            font-weight: 600;
            color: #111;
        }
        .auth-sub {
            margin-bottom: 22px;
            color: #555;
            font-size: 14px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: 500;
            color: #222;
        }
        input {
            width: 100%;
            padding: 11px 12px;
            border-radius: 10px;
            border: 1px solid #ccc;
            background: #fafafa;
            color: #111;
            font-size: 14px;
            outline: none;
        }
        input:focus {
            border-color: #111;
        }
        .btn {
            width: 100%;
            margin-top: 18px;
            padding: 12px 0;
            background: #111;
            color: #fff;
            border: none;
            border-radius: 999px;
            font-weight: 600;
            cursor: pointer;
            font-size: 15px;
            transition: 0.25s;
        }
        .btn:hover {
            background: #222;
        }
        .bottom-text {
            margin-top: 18px;
            font-size: 13px;
            color: #555;
            text-align: center;
        }
        .bottom-text a {
            color: #111;
            text-decoration: none;
            font-weight: 500;
        }
        .error {
            margin-bottom: 12px;
            padding: 8px 10px;
            border-radius: 8px;
            background: #fdecec;
            color: #c62828;
            font-size: 13px;
        }

        /* mobile */
        @media (max-width: 768px) {
            body {
                padding-top: 60px;
                padding-left: 10px;
                padding-right: 10px;
            }
            .auth-card {
                width: 100%;
                max-width: 360px;
                padding: 22px 18px 20px;
            }
            .auth-card h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>

<div class="auth-card">
    <h1>Create Account</h1>
    <div class="auth-sub">Join to get the latest styles and offers.</div>

    <?php if ($error) { ?>
        <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST">
        <label>Full Name</label>
        <input type="text" name="name" required>

        <label style="margin-top:14px;">Email</label>
        <input type="email" name="email" required>

        <label style="margin-top:14px;">Password</label>
        <input type="password" name="password" required>

        <label style="margin-top:14px;">Confirm Password</label>
        <input type="password" name="confirm_password" required>

        <button type="submit" class="btn">Sign Up</button>
    </form>

    <div class="bottom-text">
        Already have an account? <a href="login.php">Login</a>
    </div>
</div>

</body>
</html>
