<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_POST['customer_id'];

    if (ctype_digit($customer_id)) {
        // Check if Customer ID exists
        $sql = "SELECT * FROM customer WHERE Customer_ID = $customer_id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Customer ID exists, proceed to the menu
            $_SESSION['cid'] = $customer_id;
            header('Location: menu.php');
            exit();
        } else {
            // Invalid Customer ID
            $message = "<p style='color: red; font-size: 20px; text-align: center;'>Invalid Customer ID</p>";
        }
    } else {
        // Non-numeric Customer ID entered
        $message = "<p style='color: red; font-size: 20px; text-align: center;'>Enter only Numeric value</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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
        .login-container {
            border: 2px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }
        .login-container h1 {
            margin-top: 0;
        }
        form {
            text-align: center;
        }
        .message {
            color: red;
            font-size: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Welcome!</h1>
        <h2>BGDL Banking</h2>
        <form method="POST" action="">
            <label for="customer_id">Enter Customer ID:</label>
            <input type="text" name="customer_id" id="customer_id" required>
            <br><br>
            <input type="submit" value="Login">
        </form>
        <?php if (isset($message)) echo $message; ?>
    </div>
</body>
</html>
