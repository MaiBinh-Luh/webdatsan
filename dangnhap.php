<?php
require_once('./initialize.php');

// Kết nối tới cơ sở dữ liệu
try {
    $conn = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USERNAME, DB_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Thiết lập chế độ lỗi
} catch (PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());  // In ra lỗi nếu không thể kết nối
}

// Biến message để truyền vào JavaScript
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Kiểm tra dữ liệu
    if (empty($username) || empty($password)) {
        $message = "Vui lòng điền đầy đủ thông tin!";
    } else {
        // Kiểm tra tên đăng nhập và mật khẩu
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && md5($password) === $user['password']) {
            // Mật khẩu đúng, lưu thông tin người dùng vào session
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['type'] = $user['type'];

            // Đặt thông báo thành công và chuyển hướng
            $message = "success";
            header("Location: " . base_url . "/");
            exit;
        } else {
            $message = "Tên đăng nhập hoặc mật khẩu không đúng!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <style>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-image: url("https://demo.taixiumomo.top/uploads/cover.png?v=1733616749");
            background-size:cover;
            background-repeat:no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            background: #fff;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-container h1 {
            font-size: 24px;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-group input:focus {
            border-color: #6e8efb;
            outline: none;
        }
        .login-btn {
            width: 100%;
            background: #6e8efb;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .login-btn:hover {
            background: #5a76d7;
        }
        .register-link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }
        .register-link a {
            color: #6e8efb;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }

        /* Popup styles */
        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            display: none; /* Ẩn mặc định */
            z-index: 1000;
            text-align: center;
            animation: fadeIn 0.3s ease-out;
        }
        .popup h2 {
            margin-bottom: 10px;
            font-size: 20px;
            color: #333;
        }
        .popup p {
            margin-bottom: 20px;
            font-size: 16px;
            color: #666;
        }
        .popup button {
            background: #6e8efb;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .popup button:hover {
            background: #5a76d7;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translate(-50%, -60%);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%);
            }
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none; /* Ẩn mặc định */
        }
    </style>
    </style>
    <script>
        // Hiển thị popup
        window.onload = function () {
            const message = "<?php echo $message; ?>";
            const popup = document.querySelector('.popup');
            const overlay = document.querySelector('.overlay');
            const popupMessage = document.querySelector('.popup p');

            if (message === "success") {
                popupMessage.textContent = "Đăng nhập thành công!";
                popup.style.display = 'block';
                overlay.style.display = 'block';
                setTimeout(() => {
                    window.location.href = "/"; // Chuyển hướng về trang chính sau 2 giây
                }, 2000); 
            } else if (message) {
                popupMessage.textContent = message;
                popup.style.display = 'block';
                overlay.style.display = 'block';
            }
        };

        // Ẩn popup khi bấm nút
        function closePopup() {
            document.querySelector('.popup').style.display = 'none';
            document.querySelector('.overlay').style.display = 'none';
        }
    </script>
</head>
<body>
    <div class="overlay"></div>
    <div class="popup">
        <h2>Thông Báo</h2>
        <p></p>
        <button onclick="closePopup()">Đóng</button>
    </div>
    <div class="login-container">
        <h1>Đăng Nhập</h1>
        <form method="POST">
            <div class="form-group">
                <label for="username">Tên đăng nhập:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-btn">Đăng Nhập</button>
        </form>
        <div class="register-link">
            <p>Chưa có tài khoản? <a href="dangky.php">Đăng ký ngay</a></p>
        </div>
    </div>
</body>
</html>
