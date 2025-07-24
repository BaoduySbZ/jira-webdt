<?php
session_start();
require_once '../includes/db.php';

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Xử lý xóa sản phẩm
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: manage_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý sản phẩm</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f2f2f2; padding: 30px;">

    <div style="max-width: 1000px; margin: 0 auto; background: white; padding: 25px; box-shadow: 0 0 10px rgba(0,0,0,0.1); border-radius: 10px;">
        <h2 style="color: #2c3e50; margin-bottom: 20px;">📦 Quản lý sản phẩm</h2>

        <div style="margin-bottom: 15px;">
            <a href="add_product.php" style="text-decoration: none; background-color: #27ae60; color: white; padding: 10px 18px; border-radius: 5px; font-weight: bold;">➕ Thêm sản phẩm</a>
            <a href="admin_panel.php" style="text-decoration: none; margin-left: 10px; background-color: #3498db; color: white; padding: 10px 18px; border-radius: 5px; font-weight: bold;">⬅ Trang quản trị</a>
        </div>

        <table style="width: 100%; border-collapse: collapse;">
            <tr style="background-color: #ecf0f1;">
                <th style="border: 1px solid #ccc; padding: 12px;">ID</th>
                <th style="border: 1px solid #ccc; padding: 12px;">Tên sản phẩm</th>
                <th style="border: 1px solid #ccc; padding: 12px;">Giá</th>
                <th style="border: 1px solid #ccc; padding: 12px;">Hành động</th>
            </tr>

            <?php
            $result = $conn->query("SELECT * FROM products ORDER BY id DESC");
            while ($row = $result->fetch_assoc()):
            ?>
            <tr style="background-color: #fff;">
                <td style="border: 1px solid #ccc; padding: 10px; text-align: center;"><?= $row['id'] ?></td>
                <td style="border: 1px solid #ccc; padding: 10px;"><?= htmlspecialchars($row['name']) ?></td>
                <td style="border: 1px solid #ccc; padding: 10px;"><?= number_format($row['price']) ?> đ</td>
                <td style="border: 1px solid #ccc; padding: 10px; text-align: center;">
                    <a href="edit_product.php?id=<?= $row['id'] ?>" style="color: #2980b9; text-decoration: none; margin-right: 10px;">✏️ Sửa</a>
                    <a href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Bạn có chắc muốn xoá sản phẩm này?')" style="color: red; text-decoration: none;">🗑️ Xoá</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <?php if ($result->num_rows === 0): ?>
            <p style="text-align: center; margin-top: 20px;">Không có sản phẩm nào.</p>
        <?php endif; ?>
    </div>

</body>
</html>
