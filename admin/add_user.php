<?php
session_start();
include '../includes/db.php';

// Kiểm tra nếu chưa đăng nhập admin thì chuyển hướng
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Xử lý khi submit form thêm người dùng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Hash mật khẩu
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, username, password, role) VALUES (?, ?, ?, ?, 'user')");
    $stmt->bind_param("ssss", $name, $email, $username, $hashed_password);
    $stmt->execute();

    header("Location: manage_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm người dùng</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f2f2f2;
            padding: 40px;
        }
        h2 {
            color: #333;
        }
        form {
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            max-width: 450px;
            margin: auto;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin-top: 12px;
            font-weight: bold;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            margin-top: 16px;
            padding: 10px 18px;
            background-color: #28a745;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: #218838;
        }
        a.back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #333;
            text-decoration: none;
        }
    </style>
</head>
<body>

<h2>➕ Thêm người dùng mới</h2>

<form method="POST">
    <label for="name">Họ tên:</label>
    <input type="text" name="name" id="name" required>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>

    <label for="username">Tên đăng nhập:</label>
    <input type="text" name="username" id="username" required>

    <label for="password">Mật khẩu:</label>
    <input type="password" name="password" id="password" required>

    <button type="submit">Thêm người dùng</button>
</form>

<a href="manage_users.php" class="back-link">⬅ Quay lại danh sách người dùng</a>

</body>
</html>
