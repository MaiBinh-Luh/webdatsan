<?php
include('includes/db.php');

$message = ""; // Biến thông báo

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Mã hóa mật khẩu

    // Kiểm tra xem tên người dùng hoặc email đã tồn tại chưa
    $checkUser = $conn->query("SELECT * FROM users WHERE username='$username' OR email='$email'");
    if ($checkUser->num_rows > 0) {
        $message = "Tên đăng nhập hoặc email đã tồn tại!";
    } else {
        // Chèn người dùng mới vào bảng
        $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
        if ($conn->query($sql) === TRUE) {
            $message = "success"; // Thông báo thành công
        } else {
            $message = "Lỗi: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .register-container {
            background: #fff;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .register-container h1 {
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
        .register-btn {
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
        .register-btn:hover {
            background: #5a76d7;
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }
        .login-link a {
            color: #6e8efb;
            text-decoration: none;
        }
        .login-link a:hover {
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
    <script>
        // Hiển thị popup
        window.onload = function () {
            const message = "<?php echo $message; ?>";
            const popup = document.querySelector('.popup');
            const overlay = document.querySelector('.overlay');
            const popupMessage = document.querySelector('.popup p');

            if (message === "success") {
                popupMessage.textContent = "Đăng ký thành công!";
                popup.style.display = 'block';
                overlay.style.display = 'block';
                setTimeout(() => {
                    window.location.href = "login.php";
                }, 2000); // Chuyển hướng sau 2 giây
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
    <div class="register-container">
        <h1>Đăng Ký</h1>
        <form method="POST">
            <div class="form-group">
                <label for="username">Tên đăng nhập:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="register-btn">Đăng Ký</button>
        </form>
        <div class="login-link">
            <p>Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
        </div>
    </div>
</body>
</html>
