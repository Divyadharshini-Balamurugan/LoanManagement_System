<?php
session_start();
include 'db.php';

if (!isset($_SESSION['cid'])) {
    header('Location: index.php');
    exit();
}
$cid = $_SESSION['cid'];

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $loan_no = $_POST['loan_no'];

    if (ctype_digit($loan_no)) {
        // Fetch the loan amount
        $sql = "SELECT Amount FROM loan WHERE Loan_No = $loan_no AND Customer_ID = $cid";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $amount = $row['Amount'];

            if ($amount == 0) {
                // Update the loan status to 'Closed'
                $sql = "UPDATE loan SET loan_status = 'Closed' WHERE Loan_No = $loan_no AND Customer_ID = $cid";

                if ($conn->query($sql) === TRUE) {
                    $message = "<p class='success'>Loan successfully closed.</p>";
                } else {
                    $message = "<p class='error'>Error: " . $conn->error . "</p>";
                }
            } else {
                $message = "<p class='error'>Loan amount is $amount. Loan amount is pending, so failed to close the loan.</p>";
            }
        } else {
            $message = "<p class='error'>No such loan found.</p>";
        }
    } else {
        $message = "<p class='error'>Enter only Numeric value</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Close Loan</title>
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
            text-align: center;
        }
        form {
            text-align: center;
        }
        label {
            margin-right: 10px;
        }
        input[type="text"] {
            margin-bottom: 10px;
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
        .error {
            color: red;
            font-size: 20px;
            text-align: center;
        }
        .success {
            color: green;
            font-size: 20px;
            text-align: center;
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
    <h2>Close Loan</h2>
    <form method="POST" action="">
        <label for="loan_no">Enter Loan No:</label>
        <input type="text" name="loan_no" id="loan_no">
        <input type="submit" value="Close">
    </form>
    <?php echo $message; ?>
    <a href="menu.php">Back to Menu</a>
</body>
</html>
