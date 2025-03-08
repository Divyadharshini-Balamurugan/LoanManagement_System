# 🏦 Loan Management System

## 📌 Overview
The **Loan Management System** is a web-based application developed using **PHP & MySQL** that helps users manage loans efficiently. It enables users to **apply for loans, track repayments, manage loan statuses, and generate reports.** The system automatically approves loans based on predefined criteria.

---

## ✨ Features
✅ **User Registration & Authentication** – Secure login system for users.
✅ **Loan Application** – Users can apply for loans with details like amount, tenure, and interest rate.
✅ **Automated Loan Approval** – Loans are approved automatically based on predefined criteria.
✅ **Repayment Tracking** – Monitor installment payments and remaining balance.
✅ **Interest Calculation** – Automatic interest computation based on loan type.
✅ **User Dashboard** – Track loan status, repayments, and history.

---

## 🛠 Tech Stack
🖥 **Backend**: PHP  
📄 **Frontend**: HTML, CSS, JavaScript, Bootstrap  
💾 **Database**: MySQL  

---

## ⚙️ Installation & Setup

1. **Clone the repository**
   ```sh
   git clone https://github.com/your-repo/loan-management.git
   cd loan-management
   ```

2. **Setup MySQL Database**
   - Open MySQL and create a database:
     ```sql
     CREATE DATABASE loanmanagement;
     USE loanmanagement;
     ```
   - Import the provided `loan-db.sql` file into MySQL.
   - Ensure your database name is **`loanmanagement`** to match the configuration in `config.php`.

3. **Configure Database Connection**
   - Open `config.php` and set your database credentials:
     ```php
     $con = mysqli_connect("localhost", "root", "", "loanmanagement");
     ```
   - Modify credentials if using a different MySQL user/password.

4. **Run the Application**
   - Place the project folder inside your web server directory (e.g., `htdocs` for XAMPP).
   - Start Apache and MySQL using XAMPP or any preferred local server.
   - Access the app via `http://localhost/loan-management/`

---

## 🐈 Database Schema

### **Table: `users`**
```sql
CREATE TABLE users (
  user_id INT(11) PRIMARY KEY AUTO_INCREMENT,
  fullname VARCHAR(100) NOT NULL,
  email VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### **Table: `loans`**
```sql
CREATE TABLE loans (
  loan_id INT(11) PRIMARY KEY AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  loan_amount DECIMAL(10,2) NOT NULL,
  interest_rate DECIMAL(5,2) NOT NULL,
  tenure INT(11) NOT NULL COMMENT 'Loan tenure in months',
  status ENUM('approved', 'rejected', 'completed') DEFAULT 'approved',
  applied_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);
```

### **Table: `repayments`**
```sql
CREATE TABLE repayments (
  payment_id INT(11) PRIMARY KEY AUTO_INCREMENT,
  loan_id INT(11) NOT NULL,
  payment_amount DECIMAL(10,2) NOT NULL,
  payment_date DATE NOT NULL,
  FOREIGN KEY (loan_id) REFERENCES loans(loan_id) ON DELETE CASCADE
);
```

---

## 🔗 Key PHP Files & Functionality
- **`index.php`** – User login page  
- **`dashboard.php`** – User dashboard  
- **`apply_loan.php`** – Loan application form  
- **`loan_status.php`** – Automatically checks and updates loan status  
- **`repayment.php`** – Track and manage repayments  
- **`export.php`** – Export loan and repayment data  
- **`config.php`** – Database configuration  

---

## 🎯 How to Use
1. **🔐 Login/Register** – Users sign up or log in.
2. **💰 Apply for a Loan** – Users submit loan requests.
3. **📄 Loan Approval** – The system approves/rejects loans automatically.
4. **🗓️ Track Repayments** – Users view and make repayments.
5. **📄 Generate Reports** – Export loan and repayment data.

---

## 🚀 Future Enhancements
🔹 **Automated EMI Calculation**  
🔹 **Notifications for Due Payments**  
🔹 **Integration with Payment Gateway**  
🔹 **Credit Score Analysis**  

---

## 🐜 License
This project is open-source under the **MIT License**.  

💡 *Enhance financial management with this Loan Management System!* 🚀

