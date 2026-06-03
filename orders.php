<?php
include 'db.php';

$user_id = 1; // demo user

// Select relevant fields, including total_amount and status, for this user's orders
$orders = mysqli_query($conn, 
    "SELECT orders.id, orders.total_amount, orders.status, orders.date,
            users.name AS user_name, 
            products.name AS product_name, products.price 
     FROM orders
     JOIN users ON orders.user_id = users.id
     JOIN products ON orders.productid = products.id
     WHERE orders.user_id = $user_id
     ORDER BY orders.id DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        table { width: 70%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; }
    </style>
</head>
<body>

<h1>My Orders</h1>
<a href="index.php">← Back to Home</a>
<br><br>

<table>
<tr>
    <th>Order ID</th>
    <th>Total Amount</th>
    <th>Status</th>
    <th>View Items</th>
</tr>

<?php
while ($o = mysqli_fetch_assoc($orders)) {
    echo "<tr>";
    echo "<td>".$o['id']."</td>";
    echo "<td>₹".$o['total_amount']."</td>";
    echo "<td>".$o['status']."</td>";
    echo "<td><a href='order_details.php?id=".$o['id']."'>View</a></td>";
    echo "</tr>";
}
?>
</table>

</body>
</html>
