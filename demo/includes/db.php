<?php
$servername = "localhost";
$username = "shopni10_bongda123";
$password = "shopni10_bongda123";
$dbname = "shopni10_bongda123";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Đảm bảo kết nối sử dụng UTF-8
$conn->set_charset("utf8mb4");  // Hoặc utf8 nếu bạn không cần hỗ trợ các ký tự đặc biệt hơn

?>
