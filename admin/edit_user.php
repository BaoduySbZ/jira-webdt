<?php
session_start();
include '../includes/db.php';

// Kiểm tra nếu chưa đăng nhập admin thì chuyển hướng
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Lấy ID người dùng cần sửa từ URL
if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$id = intval($_GET['id']);

// Lấy thông tin người dùng từ DB
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "<p>Người dùng không tồn tại.</p>";
    exit();
}

// Xử lý khi submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $email, $id);
    $stmt->execute();

    header("Location: manage_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa người dùng</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f8f8f8;
            padding: 40px;
        }
        h2 {
            color: #333;
        }
        form {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            max-width: 400px;
            margin: auto;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 8px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            margin-top: 15px;
            padding: 10px 16px;
            background-color: #4CAF50;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: #45a049;
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

<h2>✏️ Sửa người dùng</h2>

<form method="POST">
    <label for="name">Họ tên:</label>
    <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" required>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>

    <button type="submit">Lưu thay đổi</button>
</form>

<a href="manage_users.php" class="back-link">⬅ Quay lại danh sách người dùng</a>

</body>
</html>
