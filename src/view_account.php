<?php
session_start();
include 'db.php';

if (!isset($_SESSION['cid'])) {
    header('Location: index.php');
    exit();
}
$cid = $_SESSION['cid'];

$sql = "SELECT customer.Customer_ID, customer.Customer_Name, customer.Account_No, customer.DOB, account.Type, account.Balance,customer.Door_No, customer.Street, customer.City, customer.state, customer.pincode 
        FROM customer 
        INNER JOIN account ON customer.Account_No = account.Account_No 
        WHERE customer.Customer_ID = $cid";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Detail</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            background-color: #f0f0f0;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            border: 2px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 1000px;
            text-align: center;
        }
        h2 {
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f9f9f9;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #333;
            font-size: 18px;
            padding: 10px 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            transition: background-color 0.3s, color 0.3s;
        }
        a:hover {
            background-color: #333;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Account Detail</h2>
        <table>
            <tr>
                <th>Customer_ID</th>
                <th>Customer_Name</th>
                <th>Account_No</th>
                <th>DOB</th>
                <th>Type</th>
                <th>Balance</th>
                <th>Door_No</th>
                <th>Street</th>
                <th>City</th>
                <th>State</th>
                <th>Pincode</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['Customer_ID'] . "</td>";
                    echo "<td>" . $row['Customer_Name'] . "</td>";
                    echo "<td>" . $row['Account_No'] . "</td>";
                    echo "<td>" . $row['DOB'] . "</td>";
                    echo "<td>" . $row['Type'] . "</td>";
                    echo "<td>" . $row['Balance'] . "</td>";
                    echo "<td>" . $row['Door_No'] . "</td>";
                    echo "<td>" . $row['Street'] . "</td>";
                    echo "<td>" . $row['City'] . "</td>";
                    echo "<td>" . $row['state'] . "</td>";
                    echo "<td>" . $row['pincode'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='11'>No results found</td></tr>";
            }
            $conn->close();
            ?>
        </table>
        <a href="menu.php">Back to Menu</a>
    </div>
</body>
</html>
