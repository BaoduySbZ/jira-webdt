<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Lấy ID sản phẩm cần sửa
if (!isset($_GET['id'])) {
    header("Location: manage_products.php");
    exit();
}

$product_id = intval($_GET['id']);

// Lấy thông tin sản phẩm
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "Sản phẩm không tồn tại!";
    exit();
}

// Xử lý cập nhật khi submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $slug = $_POST['slug'] ?: strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
    $category = $_POST['category'];
    $brand = $_POST['brand'];
    $short_desc = $_POST['short_desc'];
    $full_desc = $_POST['full_desc'];
    $price = floatval($_POST['price']);
    $sale_price = $_POST['sale_price'] !== '' ? floatval($_POST['sale_price']) : null;
    $stock = intval($_POST['stock']);
    $status = intval($_POST['status']);

    // Upload ảnh nếu có
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target = "../uploads/" . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $image_path = "uploads/" . $image_name;

        $stmt = $conn->prepare("UPDATE products SET name=?, slug=?, category=?, brand=?, short_desc=?, full_desc=?, price=?, sale_price=?, stock=?, image=?, status=? WHERE id=?");
        $stmt->bind_param("ssssssddisii", $name, $slug, $category, $brand, $short_desc, $full_desc, $price, $sale_price, $stock, $image_path, $status, $product_id);
    } else {
        $stmt = $conn->prepare("UPDATE products SET name=?, slug=?, category=?, brand=?, short_desc=?, full_desc=?, price=?, sale_price=?, stock=?, status=? WHERE id=?");
        $stmt->bind_param("ssssssddiisi", $name, $slug, $category, $brand, $short_desc, $full_desc, $price, $sale_price, $stock, $status, $product_id);
    }

    $stmt->execute();
    header("Location: manage_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>📝 Cập nhật sản phẩm</title>
    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
            padding: 30px;
        }
        .container {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px #ccc;
        }
        input[type=text], input[type=number], textarea, select {
            width: 100%;
            padding: 10px;
            margin: 6px 0 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        label {
            font-weight: bold;
        }
        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn-update {
            background: green;
            color: white;
        }
        .btn-back {
            background: #ccc;
            color: black;
            margin-left: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>🖊️ Cập nhật sản phẩm</h2>

    <form method="post" enctype="multipart/form-data">
        <label>Tên sản phẩm (*):</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

        <label>Slug (URL thân thiện):</label>
        <input type="text" name="slug" value="<?= htmlspecialchars($product['slug']) ?>">

        <label>Danh mục:</label>
        <input type="text" name="category" value="<?= htmlspecialchars($product['category']) ?>">

        <label>Thương hiệu:</label>
        <input type="text" name="brand" value="<?= htmlspecialchars($product['brand']) ?>">

        <label>Mô tả ngắn:</label>
        <textarea name="short_desc" rows="3"><?= isset($product['short_desc']) ? htmlspecialchars($product['short_desc']) : '' ?></textarea>

        <label>Mô tả chi tiết:</label>
        <textarea name="full_desc" rows="5"><?= isset($product['full_desc']) ? htmlspecialchars($product['full_desc']) : '' ?></textarea>

        <label>Giá bán (*):</label>
        <input type="number" name="price" step="0.01" value="<?= htmlspecialchars($product['price']) ?>" required>

        <label>Giá khuyến mãi:</label>
        <input type="number" name="sale_price" step="0.01" value="<?= htmlspecialchars($product['sale_price']) ?>">

        <label>Số lượng tồn kho (*):</label>
        <input type="number" name="stock" value="<?= htmlspecialchars($product['stock']) ?>" required>

        <label>Ảnh sản phẩm:</label><br>
        <?php if (!empty($product['image'])): ?>
            <img src="../<?= $product['image'] ?>" alt="Ảnh sản phẩm" style="height:100px; margin:10px 0;"><br>
        <?php endif; ?>
        <input type="file" name="image"><br><br>

        <label>Trạng thái:</label>
        <select name="status">
            <option value="1" <?= $product['status'] == 1 ? 'selected' : '' ?>>Hiển thị</option>
            <option value="0" <?= $product['status'] == 0 ? 'selected' : '' ?>>Ẩn</option>
        </select>

        <br>
        <button type="submit" class="btn btn-update">✅ Cập nhật sản phẩm</button>
        <a href="manage_products.php" class="btn btn-back">⬅ Quay lại</a>
    </form>
</div>
</body>
</html>
