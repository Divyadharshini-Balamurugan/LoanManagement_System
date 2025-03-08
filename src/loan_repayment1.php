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
    $account_number = $_POST['account_number'];
    $amount_paid = $_POST['amount_paid'];

    // Check if the provided loan number corresponds to the customer
    $loan_check_sql = "SELECT * FROM loan WHERE Loan_No = $loan_no AND Customer_ID = $cid";
    $loan_check_result = $conn->query($loan_check_sql);

    if ($loan_check_result->num_rows == 0) {
        echo '<p class="error">The provided loan number does not exist or does not belong to you.</p>';
    } else {
        // Check if the provided account number corresponds to the provided loan number
        $account_check_sql = "SELECT * FROM account WHERE Account_No = $account_number AND Customer_ID = $cid";
        $account_check_result = $conn->query($account_check_sql);

        if ($account_check_result->num_rows == 0) {
            echo '<p class="error">The provided account number does not correspond to your account.</p>';
        } else {
            $row = $loan_check_result->fetch_assoc();
            $pending_amount = $row['Amount'];

            // Validation for amount paid
            if ($amount_paid <= 0 || $amount_paid > $pending_amount) {
                echo '<p class="error">Invalid amount. Please enter an amount greater than 0 and less than or equal to the pending loan amount.</p>';
            } else {
                $transaction_status = 'Failed';
                $transaction_id = '';

                // Start transaction
                $conn->begin_transaction();

                // Reduce the amount from the loan
                $new_pending_amount = $pending_amount - $amount_paid;
                $update_loan_sql = "UPDATE loan SET Amount = $new_pending_amount WHERE Loan_No = $loan_no";
                if ($conn->query($update_loan_sql)) {
                    // Insert payment details into payment table
                    $transaction_date = date("Y-m-d");
                    $insert_payment_sql = "INSERT INTO payment (Transaction_Date, Transaction_status, Amount, Customer_ID, Account_No, Loan_No) 
                                            VALUES ('$transaction_date', 'Success', $amount_paid, $cid, $account_number, $loan_no)";
                    if ($conn->query($insert_payment_sql)) {
                        // Get the transaction ID
                        $transaction_id = $conn->insert_id;
                        $transaction_status = 'Success';

                        // Reduce paid amount from the account balance
                        $update_account_sql = "UPDATE account SET Balance = Balance - $amount_paid WHERE Account_No = $account_number";
                        if (!$conn->query($update_account_sql)) {
                            // Rollback transaction if account balance update fails
                            $conn->rollback();
                            $transaction_status = 'Failed';
                        }

                        // Commit transaction if everything is successful
                        if ($transaction_status == 'Success') {
                            $conn->commit();
                        }
                    } else {
                        // Rollback transaction if payment insertion fails
                        $conn->rollback();
                    }
                }

                // Display transaction status
                if ($transaction_status == 'Success') {
                    // Fetch account balance after transaction
                    $account_balance_sql = "SELECT Balance FROM account WHERE Account_No = $account_number";
                    $account_balance_result = $conn->query($account_balance_sql);
                    $account_balance = $account_balance_result->fetch_assoc()['Balance'];

                    // Fetch loan due amount after transaction
                    $loan_due_sql = "SELECT Amount FROM loan WHERE Loan_No = $loan_no";
                    $loan_due_result = $conn->query($loan_due_sql);
                    $loan_due_amount = $loan_due_result->fetch_assoc()['Amount'];

                    echo '<p class="success">Transaction successful. Transaction ID: ' . $transaction_id . '</p>';
                    echo '<p>Account Balance: ' . $account_balance . '</p>';
                    echo '<p>Loan Due Amount: ' . $loan_due_amount . '</p>';
                } else {
                    echo '<p class="error">Transaction failed.</p>';
                }
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Loan Repayment</title>
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
            width: 300px;
            text-align: center;
        }
        h2 {
            margin-top: 0;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: flex-start; /* Align labels and inputs to the start */
        }
        label {
            margin: 10px 0;
            display: block; /* Ensure each label appears on a new line */
        }
        input, select {
            margin: 10px 0;
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
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Loan Repayment</h2>
        <form method="POST" action="">
            <label for="loan_no">Loan Number:</label>
            <input type="text" name="loan_no" id="loan_no" required><br>
            <label for="account_number">Account Number:</label>
            <input type="text" name="account_number" id="account_number" required><br>
            <label for="amount_paid">Amount Paid:</label>
            <input type="number" name="amount_paid" id="amount_paid" required><br>
            <input type="submit" value="Submit">
        </form>
        <a href="menu.php">Back to Menu</a>
    </div>
</body>
</html>
