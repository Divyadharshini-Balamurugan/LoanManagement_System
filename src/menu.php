<?php
session_start();
if (!isset($_SESSION['cid'])) {
    header('Location: index.php');
    exit();
}
$cid = $_SESSION['cid'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Menu</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .menu-container {
            border: 2px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }
        .menu-container h1 {
            margin-top: 0;
        }
        .menu-container a {
            display: block;
            margin: 10px 0;
            text-decoration: none;
            color: #333;
            font-size: 18px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            transition: background-color 0.3s, color 0.3s;
        }
        .menu-container a:hover {
            background-color: #333;
            color: #fff;
        }
        .logout {
            color: red;
        }
    </style>
</head>
<body>
    <div class="menu-container">
        <h1>BGDL Banking</h1>
        <h2>Main Menu</h2>
        <a href="view_account.php">View Account Detail</a>
        <a href="new_loan.php">New Loan</a>
        <a href="view_loan.php">View Loan Detail</a>
        <a href="loan_repayment1.php">Loan Repayment</a>
        <a href="close_loan.php">Close Loan</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</body>
</html>
