<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db.php';

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Users</title>

<style>
    body {
        font-family: Arial;
        background: #f5f5f5;
        margin: 0;
        padding: 0;
    }

    header {
        background: black;
        color: white;
        padding: 20px;
        text-align: center;
        font-size: 26px;
    }

    table {
        width: 90%;
        margin: 30px auto;
        border-collapse: collapse;
        background: white;
        border-radius: 10px;
        overflow: hidden;
    }

    th, td {
        padding: 15px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }

    th {
        background: #222;
        color: white;
    }
</style>

</head>

<body>

<header>All Users</header>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($users)) { ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['email']; ?></td>
    </tr>
    <?php } ?>

</table>

</body>
</html>
