<?php
require_once 'includes/db.php'; // Nhúng kết nối CSDL (đã có session_start())
require_once 'includes/header.php';

// Nếu đã đăng nhập → quay lại index
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, username, password, role, full_name FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // ✅ Lưu session đăng nhập
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'] ?? $user['username'];

            // ✅ Gán session riêng cho admin nếu có
            if ($user['role'] === 'admin') {
                $_SESSION['admin_logged_in'] = true;
            }

            $_SESSION['message'] = ['type' => 'success', 'text' => 'Đăng nhập thành công! Chào mừng ' . htmlspecialchars($_SESSION['full_name']) . '.'];
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Tên đăng nhập hoặc mật khẩu không đúng.'];
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Tên đăng nhập hoặc mật khẩu không đúng.'];
        header("Location: login.php");
        exit();
    }
    $stmt->close();
}
?>

<div class="form-container">
    <h2>Đăng nhập</h2>
    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="username">Tên đăng nhập:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Mật khẩu:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <button type="submit">Đăng nhập</button>
        </div>
        <p style="text-align: center;">Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
    </form>
</div>

<?php
require_once 'includes/footer.php';
$conn->close();
?>
