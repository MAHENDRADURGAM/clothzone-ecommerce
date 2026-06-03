<?php
session_start();
include 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $res = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' LIMIT 1");
    if ($res && mysqli_num_rows($res) === 1) {
        $user = mysqli_fetch_assoc($res);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CLOTHZONE</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <style>
     body {
    margin: 0;
    background: #0f0f0f;              /* keep dark page */
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding-top: 80px;
    box-sizing: border-box;
    color: #111;                      /* default text dark */
}

     .auth-card {
    width: 420px;
    max-width: 95%;
    background: #ffffff;              /* white card */
    padding: 26px 24px 22px;
    border-radius: 16px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.55);
}
       h1 {
    margin: 0 0 8px;
    font-size: 26px;
    font-weight: 600;
    color: #111;                      /* black heading */
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
    border: 1px solid #ccc;           /* light border */
    background: #fafafa;              /* light input bg */
    color: #111;                      /* black text */
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
            background: #fff;
            color: #000;
            border: none;
            border-radius: 999px;
            font-weight: 600;
            cursor: pointer;
            font-size: 15px;
            transition: 0.25s;
        }
        .btn:hover {
            background: #e5e5e5;
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
            background: #3b0b0b;
            color: #ffb3b3;
            font-size: 13px;
        }

        /* Mobile layout */
        @media (max-width: 768px) {
            body {
                font-size: 14px;
                margin: 0;
                padding: 16px 10px;
                box-sizing: border-box;
            }

            .auth-card {
                width: 100%;
                max-width: 360px;
                padding: 24px 20px;
                margin: 0;
                box-sizing: border-box;
            }

            h1 {
                font-size: 22px;
            }

            .auth-sub {
                font-size: 13px;
                margin-bottom: 18px;
            }

            label {
                font-size: 13px;
            }

            input {
                font-size: 14px;
                padding: 10px 11px;
            }

            .btn {
                font-size: 14px;
                padding: 11px 0;
            }

            .bottom-text {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>

<div class="auth-card">
    <h1>Login</h1>
    <div class="auth-sub">Welcome back! Sign in to your account.</div>

    <?php if ($error) { ?>
        <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST">
        <label>Email</label>
        <input type="email" name="email" required />

        <label>Password</label>
        <input type="password" name="password" required />

        <button type="submit" class="btn">Sign In</button>
    </form>
    
<div class="bottom-text">
    Don't have an account? <a href="register.php">Sign up</a><br>
    Or <a href="login_mobile.php">sign in with mobile number</a>
</div>

</div>

</body>
</html>


