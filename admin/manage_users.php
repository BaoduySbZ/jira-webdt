<?php
session_start();
include '../includes/db.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Xử lý xóa người dùng
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: manage_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý người dùng</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f8f8;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .actions {
            text-align: center;
            margin-bottom: 20px;
        }
        .actions a {
            padding: 10px 15px;
            background-color: #2ecc71;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 0 5px;
        }
        .actions a:hover {
            background-color: #27ae60;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #3498db;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        a.btn-delete {
            color: white;
            background-color: #e74c3c;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
        }
        a.btn-delete:hover {
            background-color: #c0392b;
        }
        a.btn-edit {
            color: white;
            background-color: #f39c12;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
        }
        a.btn-edit:hover {
            background-color: #d68910;
        }
    </style>
</head>
<body>

<h2>📋 Quản lý người dùng</h2>

<div class="actions">
    <a href="add_user.php">➕ Thêm người dùng</a>
    <a href="index.php">⬅ Quay lại trang quản trị</a>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>Tên</th>
        <th>Email</th>
        <th>Hành động</th>
    </tr>
    <?php
    $result = $conn->query("SELECT * FROM users ORDER BY id DESC");
    while ($row = $result->fetch_assoc()):
    ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td>
            <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn-edit">Sửa</a>
            <a href="?delete_id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Bạn có chắc muốn xóa người dùng này?')">Xóa</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
