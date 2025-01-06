<?php
session_start();
session_unset(); // Xóa tất cả dữ liệu trong session
session_destroy(); // Hủy session
header("Location: login.php"); // Chuyển hướng về trang đăng nhập
exit;
?>
