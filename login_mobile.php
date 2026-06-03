<?php
session_start();
include 'db.php';

$step  = isset($_SESSION['login_step']) ? $_SESSION['login_step'] : 1;
$error = '';
$debug_otp = ''; // show OTP on screen ONLY for testing

// STEP 1: handle mobile submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['phone']) && $step == 1) {
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));

    if ($phone === '') {
        $error = "Please enter mobile number.";
    } else {
        // generate 4-digit OTP
        $otp = rand(1000, 9999);

        // store in session (temporary)
        $_SESSION['login_phone'] = $phone;
        $_SESSION['login_otp']   = $otp;
        $_SESSION['login_step']  = 2;
        $step = 2;

        // LATER: send $otp by SMS. For now show it for testing.
        $debug_otp = $otp;
    }
}

// STEP 2: handle OTP submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp']) && $step == 2) {
    $entered = trim($_POST['otp']);
    $saved   = isset($_SESSION['login_otp']) ? $_SESSION['login_otp'] : null;
    $phone   = isset($_SESSION['login_phone']) ? $_SESSION['login_phone'] : null;

    if ($entered === '' || !$saved || !$phone) {
        $error = "Session expired. Please try again.";
        $step  = 1;
        $_SESSION['login_step'] = 1;
    } elseif ($entered != $saved) {
        $error = "Invalid OTP. Please try again.";
    } else {
        // OTP correct → find or create user by phone
        $res = mysqli_query($conn, "SELECT * FROM users WHERE phone = '$phone' LIMIT 1");
        if ($res && mysqli_num_rows($res) === 1) {
            $user = mysqli_fetch_assoc($res);
        } else {
            // create new user with this phone
            $dummyPass = password_hash(uniqid('phone_'), PASSWORD_DEFAULT);
            mysqli_query($conn, "
                INSERT INTO users (name, email, phone, password)
                VALUES ('User $phone', NULL, '$phone', '$dummyPass')
            ");
            $id   = mysqli_insert_id($conn);
            $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $id"));
        }

        // log in
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        // clear temporary session data
        unset($_SESSION['login_step'], $_SESSION['login_phone'], $_SESSION['login_otp']);

        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in with Mobile - CLOTHZONE</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        /* full-screen overlay covering site */
        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            display: flex;
            align-items: flex-start;      /* near top */
            justify-content: center;
            padding-top: 80px;            /* distance from very top */
            box-sizing: border-box;
            z-index: 9999;
        }

        /* white popup card */
        .mobile-card {
            width: 420px;
            max-width: 95%;
            background: #fff;
            border-radius: 12px;
            padding: 26px 24px 22px;
            box-sizing: border-box;
        }

        .mobile-card h1 {
            margin: 0 0 12px;
            font-size: 22px;
            font-weight: 600;
            color: #111;
        }

        .mobile-card p {
            margin: 0 0 20px;
            font-size: 13px;
            color: #555;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 500;
            color: #222;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px 11px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
            outline: none;
            box-sizing: border-box;
        }

        input:focus {
            border-color: #111;
        }

        .btn {
            width: 100%;
            margin-top: 18px;
            padding: 11px 0;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: #111;
            color: #fff;
        }

        .btn-primary:hover {
            background: #222;
        }

        .error {
            margin-bottom: 10px;
            padding: 8px 10px;
            font-size: 12px;
            border-radius: 6px;
            background: #fdecec;
            color: #c62828;
        }

        .info {
            margin-top: 10px;
            font-size: 11px;
            color: #777;
        }

        .footer-text {
            margin-top: 16px;
            font-size: 11px;
            color: #777;
        }

        .footer-text a {
            color: #111;
            text-decoration: none;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .overlay {
                padding-top: 60px;
            }
            .mobile-card {
                max-width: 360px;
                padding: 22px 18px 18px;
            }
            .mobile-card h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
<div class="overlay">
    <div class="mobile-card">
        <h1><?php echo $step == 1 ? 'Welcome to CLOTHZONE' : 'Enter OTP'; ?></h1>
        <p>
            <?php if ($step == 1) { ?>
                Enter your mobile number to continue.
            <?php } else { ?>
                An OTP has been sent to <strong><?php echo htmlspecialchars($_SESSION['login_phone']); ?></strong>.
            <?php } ?>
        </p>

        <?php if ($error) { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>

        <?php if ($step == 1) { ?>
            <form method="POST">
                <label>Mobile Number</label>
                <input type="text" name="phone" placeholder="Enter mobile number" required />
                <button type="submit" class="btn btn-primary">CONTINUE</button>
            </form>
        <?php } else { ?>
            <form method="POST">
                <label>Enter OTP</label>
                <input type="number" name="otp" placeholder="4-digit OTP" required />
                <button type="submit" class="btn btn-primary">VERIFY &amp; CONTINUE</button>
            </form>
        <?php } ?>

        <div class="footer-text">
            By Signing In, you agree to our
            <a href="#">Terms &amp; Conditions</a> and <a href="#">Privacy Policy</a>.
        </div>

        <?php if ($debug_otp) { ?>
            <div class="info">[DEV ONLY] OTP: <?php echo $debug_otp; ?></div>
        <?php } ?>
    </div>
</div>
</body>
</html>
