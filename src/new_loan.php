<?php
session_start();
include 'db.php';

if (!isset($_SESSION['cid'])) {
    header('Location: index.php');
    exit();
}
$cid = $_SESSION['cid'];

function GetEmp($cid, $conn) {
    $sql = "SELECT Employee_ID FROM has WHERE Customer_ID = $cid";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['Employee_ID'];
    }
    return 0;
}

function checkExistingLoan($cid, $loan_type, $conn) {
    $sql = "SELECT * FROM loan WHERE Customer_ID = $cid AND Type = '$loan_type' AND loan_status = 'Active'";
    $result = $conn->query($sql);
    return $result->num_rows > 0;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $loan_type = $_POST['loan_type'];
    $loan_amount = $_POST['loan_amount'];
    $loan_duration_months = $_POST['loan_duration'];
    $currentDate = date("Y-m-d");
    $empid = GetEmp($cid, $conn);

    // Check if account number corresponds to customer ID
    $account_number = $_POST['account_number'];
    $account_check_sql = "SELECT * FROM account WHERE Customer_ID = $cid AND Account_No = $account_number";
    $account_check_result = $conn->query($account_check_sql);
    if ($account_check_result->num_rows == 0) {
        echo '<p class="error">The provided account number does not correspond to your account.</p>';
    } else {
        // Validation for loan amount
        if ($loan_amount == 'more') {
            echo '<p class="error">Please visit a nearby bank to apply for loans greater than 5 lakhs.</p>';
        } elseif ($loan_amount < 0) {
            echo '<p class="error">Loan amount cannot be negative.</p>';
        } else {
            // Validation for loan duration
            if (!in_array($loan_duration_months, [3, 6, 12])) {
                echo '<p class="error">Invalid loan duration. Please select from 3, 6, or 12 months.</p>';
            } else {
                if ($loan_type == 'Personal_loan' || $loan_type == 'Asset_loan') {
                    $interest_rate = $loan_type == 'Personal_loan' ? 0.05 : 0.02;

                    if (checkExistingLoan($cid, $loan_type, $conn)) {
                        echo '<p class="error">You already have an active ' . $loan_type . '.</p>';
                    } else {
                        // Asset loan additional checks
                        if ($loan_type == 'Asset_loan') {
                            $asset_name = $_POST['asset_name'];
                            $asset_value = $_POST['asset_value'];

                            if ($asset_value < $loan_amount) {
                                echo '<p class="error">Asset value must be equal to or greater than the loan amount.</p>';
                            } else {
                                // Insert loan and asset details
                                $sql = "INSERT INTO loan (Loan_No, Type, interest_rate, loan_status, loan_duration_Months, Amount, Customer_ID, Employee_ID, Approval_date, Approval_status) 
                                        VALUES (NULL, '$loan_type', $interest_rate, 'Active', $loan_duration_months, $loan_amount, $cid, $empid, '$currentDate', 'Approved')";

                                if ($conn->query($sql) === TRUE) {
                                    // Retrieve the inserted loan number
                                    $loan_no = $conn->insert_id;

                                    // Insert asset details
                                    $asset_sql = "INSERT INTO asset (Asset_Name, Asset_Value, Customer_ID, Loan_No) 
                                                  VALUES ('$asset_name', $asset_value, $cid, $loan_no)";
                                    if ($conn->query($asset_sql) === TRUE) {
                                        // Update loan amount in account table
                                        $update_balance_sql = "UPDATE account SET Balance = Balance + $loan_amount WHERE Account_No = $account_number";
                                        if ($conn->query($update_balance_sql) === TRUE) {
                                            // Retrieve current account balance
                                            $balance_query = "SELECT Balance FROM account WHERE Account_No = $account_number";
                                            $balance_result = $conn->query($balance_query);
                                            $balance_row = $balance_result->fetch_assoc();
                                            $current_balance = $balance_row['Balance'];

                                            // Display success message with loan number
                                            echo '<p class="success">Loan successfully applied. Loan No: ' . $loan_no . '</p>';
                                            echo '<p class="success">Loan amount credited to your account. Current Balance: ' . $current_balance . '</p>';
                                        } else {
                                            echo '<p class="error">Error updating account balance: ' . $conn->error . '</p>';
                                        }
                                    } else {
                                        echo '<p class="error">Error adding asset details: ' . $conn->error . '</p>';
                                    }
                                } else {
                                    echo '<p class="error">Error applying loan: ' . $conn->error . '</p>';
                                }
                            }
                        } else {
                            // Insert loan for personal loan
                            $sql = "INSERT INTO loan (Loan_No, Type, interest_rate, loan_status, loan_duration_Months, Amount, Customer_ID, Employee_ID, Approval_date, Approval_status) 
                                    VALUES (NULL, '$loan_type', $interest_rate, 'Active', $loan_duration_months, $loan_amount, $cid, $empid, '$currentDate', 'Approved')";

                            if ($conn->query($sql) === TRUE) {
                                // Retrieve the inserted loan number
                                $loan_no = $conn->insert_id;

                                // Update loan amount in account table
                                $update_balance_sql = "UPDATE account SET Balance = Balance + $loan_amount WHERE Account_No = $account_number";
                                if ($conn->query($update_balance_sql) === TRUE) {
                                    // Retrieve current account balance
                                    $balance_query = "SELECT Balance FROM account WHERE Account_No = $account_number";
                                    $balance_result = $conn->query($balance_query);
                                    $balance_row = $balance_result->fetch_assoc();
                                    $current_balance = $balance_row['Balance'];

                                    // Display success message with loan number
                                    echo '<p class="success">Loan successfully applied. Loan No: ' . $loan_no . '</p>';
                                    echo '<p class="success">Loan amount credited to your account. Current Balance: ' . $current_balance . '</p>';
                                } else {
                                    echo '<p class="error">Error updating account balance: ' . $conn->error . '</p>';
                                }
                            } else {
                                echo '<p class="error">Error applying loan: ' . $conn->error . '</p>';
                            }
                        }
                    }
                }
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Loan</title>
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
            align-items: flex-start;
        }
        label {
            margin: 10px 0;
            display: block;
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
        .error {
            color: red;
        }
        .success {
            color: green;
            font-weight: bold;
        }
    </style>
    <script>
        function toggleAssetFields() {
            var loanType = document.getElementById('loan_type').value;
            var assetFields = document.getElementById('asset-fields');

            if (loanType === 'Asset_loan') {
                assetFields.style.display = 'block';
            } else {
                assetFields.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('loan_type').addEventListener('change', toggleAssetFields);
            toggleAssetFields();  // Initial call to set the correct state on page load
        });
    </script>
</head>
<body>
    <div class="container">
        <h2>New Loan</h2>
        <form method="POST" action="">
            <label for="loan_type">Loan Type:</label>
            <select name="loan_type" id="loan_type" required>
                <option value="Personal_loan">Personal Loan</option>
                <option value="Asset_loan">Asset Loan</option>
            </select>
            <label for="loan_amount">Loan Amount:</label>
            <select id="loan_amount" name="loan_amount" required>
                <option value="100000">1 Lakh</option>
                <option value="50000">50 Thousand</option>
                <option value="300000">3 Lakhs</option>
                <option value="500000">5 Lakhs</option>
                <option value="more">More</option>
            </select>
            <label for="loan_duration">Loan Duration (Months):</label>
            <select id="loan_duration" name="loan_duration" required>
                <option value="3">3 Months</option>
                <option value="6">6 Months</option>
                <option value="12">12 Months</option>
            </select>
            <label for="account_number">Account Number:</label>
            <input type="number" id="account_number" name="account_number" required>
            <div id="asset-fields">
                <label for="asset_name">Asset Name:</label>
                <input type="text" id="asset_name" name="asset_name">
                <label for="asset_value">Asset Value:</label>
                <input type="number" id="asset_value" name="asset_value">
            </div>
            <input type="submit" value="Apply">
        </form>
        <a href="menu.php">Back to Menu</a>
    </div>
</body>
</html>
