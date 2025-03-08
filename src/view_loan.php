<?php
session_start();
include 'db.php';

if (!isset($_SESSION['cid'])) {
    header('Location: index.php');
    exit();
}
$cid = $_SESSION['cid'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $loan_no = $_POST['loan_no'];

    if (ctype_digit($loan_no)) {
        $sql = "SELECT * FROM loan WHERE Loan_No = $loan_no AND Customer_ID = $cid";
        $result = $conn->query($sql);
    } else {
        echo "<p class='error'>Enter only numeric value</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Loan Detail</title>
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
        h2 {
            margin-top: 0;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            margin-right: 10px;
        }
        input[type="text"] {
            margin-bottom: 10px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            padding: 10px 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            transition: background-color 0.3s, color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #333;
            color: #fff;
        }
        table {
            border-collapse: collapse;
            width: 80%;
            max-width: 800px;
            margin-bottom: 20px;
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
        .error {
            color: red;
            font-weight: bold;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        a {
            display: inline-block;
            text-decoration: none;
            color: #333;
            font-size: 18px;
            padding: 10px 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            transition: background-color 0.3s, color 0.3s;
            margin-bottom: 20px;
        }
        a:hover {
            background-color: #333;
            color: #fff;
        }
    </style>
</head>
<body>
    <h2>Loan Detail</h2>
    <form method="POST" action="">
        <label for="loan_no">Enter Loan No:</label>
        <input type="text" name="loan_no" id="loan_no">
        <input type="submit" value="Submit">
    </form>

    <?php
    if (isset($result) && $result->num_rows > 0) {
        echo "<table>";
        echo "<tr>";
        while ($fieldinfo = $result->fetch_field()) {
            echo "<th>{$fieldinfo->name}</th>";
        }
        echo "</tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>{$value}</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>No results found</p>";
    }
    $conn->close();
    ?>

    <a href="menu.php">Back to Menu</a>
</body>
</html>
