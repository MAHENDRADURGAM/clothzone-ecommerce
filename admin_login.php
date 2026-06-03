<?php
session_start();
include 'db.php';

// if already logged in, go to dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // simple check against admins table (adjust table/columns if different)
    $sql = "SELECT * FROM admins WHERE username = '$username' LIMIT 1";
    $res = mysqli_query($conn, $sql);

    if ($res && mysqli_num_rows($res) === 1) {
        $admin = mysqli_fetch_assoc($res);

        // if you store plain password:
        if ($admin['password'] === $password) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['username'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - CLOTHZONE</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin:0;
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            background:#050505;
            font-family:'Poppins',sans-serif;
        }
        .card {
            width:360px;
            background:#101010;
            border-radius:16px;
            padding:28px 26px 26px;
            color:#fff;
            box-shadow:0 18px 50px rgba(0,0,0,0.7);
        }
        h1 {
            margin:0 0 6px;
            font-size:22px;
        }
        .sub {
            font-size:13px;
            color:#9ca3af;
            margin-bottom:20px;
        }
        label {
            display:block;
            margin-bottom:6px;
            font-size:13px;
            font-weight:500;
        }
        input {
            width:100%;
            padding:10px 11px;
            border-radius:10px;
            border:1px solid #333;
            background:#171717;
            color:#fff;
            font-size:14px;
            outline:none;
        }
        input:focus {
            border-color:#fff;
        }
        .btn {
            width:100%;
            margin-top:18px;
            padding:10px 0;
            border:none;
            border-radius:999px;
            background:#fff;
            color:#000;
            font-weight:600;
            font-size:14px;
            cursor:pointer;
        }
        .error {
            margin-bottom:12px;
            padding:8px 10px;
            border-radius:8px;
            background:#3b0b0b;
            color:#ffb3b3;
            font-size:13px;
        }
        .back-link {
            display:block;
            margin-top:14px;
            font-size:12px;
            color:#9ca3af;
            text-decoration:none;
            text-align:center;
        }
    </style>
</head>
<body>

<div class="card">
    <h1>Admin Login</h1>
    <div class="sub">Enter your admin credentials to access CLOTHZONE dashboard.</div>

    <?php if ($error) { ?>
        <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST">
        <label>Admin Username</label>
        <input type="text" name="username" required>

        <label style="margin-top:12px;">Admin Password</label>
        <input type="password" name="password" required>

        <button type="submit" class="btn">Login</button>
    </form>

    <a href="index.php" class="back-link">← Back to site</a>
</div>

</body>
</html>
