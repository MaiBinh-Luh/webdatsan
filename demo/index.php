<?php
session_start();
include('includes/db.php');

// Kiểm tra người dùng đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Lấy user_id từ session
$user_id = $_SESSION['user_id'];
$message = "";

// Lấy thông tin các đặt sân
$query = $conn->prepare("
    SELECT bookings.*, fields.name AS field_name, fields.location AS field_location, fields.price AS field_price
    FROM bookings
    INNER JOIN fields ON bookings.field_id = fields.id
    WHERE bookings.user_id = ?
");
$query->bind_param("i", $user_id);
$query->execute();
$booking_result = $query->get_result();

// Lấy danh sách sân
$fields_result = $conn->query("SELECT * FROM fields");

// Xóa đặt sân nếu có yêu cầu
if (isset($_GET['booking_id']) && is_numeric($_GET['booking_id'])) {
    $booking_id = intval($_GET['booking_id']);
    $delete_query = $conn->prepare("DELETE FROM bookings WHERE id = ? AND user_id = ?");
    $delete_query->bind_param("ii", $booking_id, $user_id);
    if ($delete_query->execute()) {
        header("Location: index.php");
        exit;
    } else {
        $message = "Lỗi khi xóa đặt sân!";
    }
}

// Thêm hoặc chỉnh sửa đặt sân
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit_booking_id']) && is_numeric($_POST['edit_booking_id'])) {
        // Chỉnh sửa đặt sân
        $booking_id = intval($_POST['edit_booking_id']);
        $customer_name = htmlspecialchars(trim($_POST['customer_name']));
        $customer_phone = htmlspecialchars(trim($_POST['customer_phone']));
        $booking_date = $_POST['booking_date'];

        $update_query = $conn->prepare("
            UPDATE bookings 
            SET customer_name = ?, customer_phone = ?, booking_date = ?
            WHERE id = ? AND user_id = ?
        ");
        $update_query->bind_param("sssii", $customer_name, $customer_phone, $booking_date, $booking_id, $user_id);

        if ($update_query->execute()) {
            $message = "Cập nhật đặt sân thành công!";
            header("Location: index.php");
        } else {
            $message = "Lỗi khi cập nhật đặt sân!";
        }
    } else {
        // Thêm đặt sân mới
        $field_id = $_POST['field_id'];
        $customer_name = htmlspecialchars(trim($_POST['customer_name']));
        $customer_phone = htmlspecialchars(trim($_POST['customer_phone']));
        $booking_date = $_POST['booking_date'];

        // Kiểm tra sân bóng
        $field_check = $conn->prepare("SELECT id FROM fields WHERE id = ?");
        $field_check->bind_param("i", $field_id);
        $field_check->execute();
        $field_result = $field_check->get_result();

        if ($field_result->num_rows === 0) {
            $message = "Sân bóng không tồn tại!";
        } else {
            $insert_query = $conn->prepare("
                INSERT INTO bookings (field_id, booking_date, customer_name, customer_phone, user_id)
                VALUES (?, ?, ?, ?, ?)
            ");
            $insert_query->bind_param("isssi", $field_id, $booking_date, $customer_name, $customer_phone, $user_id);
            if ($insert_query->execute()) {
                $message = "Đặt sân thành công!";
                header("Location: index.php");
            } else {
                $message = "Đã xảy ra lỗi, vui lòng thử lại.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Sân Bóng</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<style>
/* Popup container */
.popup {
    position: fixed;
    top: 10%;
    left: 50%;
    transform: translateX(-50%);
    min-width: 300px;
    padding: 15px 20px;
    border-radius: 8px;
    font-size: 16px;
    color: #fff;
    text-align: center;
    z-index: 1000;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    animation: fadeInOut 5s ease-in-out;
}

/* Success message style */
.popup.success {
    background-color: #4CAF50; /* Green */
}

/* Error message style */
.popup.error {
    background-color: #f44336; /* Red */
}

/* Animation for fade in and fade out */
@keyframes fadeInOut {
    0%, 100% {
        opacity: 0;
        transform: translateX(-50%) translateY(-20px);
    }
    10%, 90% {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
}
</style>

<body>
<div class="container">
    <header>
        <h1>Đặt Sân Bóng Online</h1>
        <p>Chào <?php echo htmlspecialchars($_SESSION['username']); ?> Chúc bạn một ngày mới vui vẻ...</p>
        <p><a href="logout.php">Đăng xuất</a></p>
    </header>

    <main>
        <!-- Hiển thị thông báo -->
        <?php if ($message): ?>
            <div class="popup <?php echo strpos($message, 'thành công') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>


        <!-- Hiển thị danh sách đặt sân -->
        <section class="booking-info">
            <h3>Danh sách đặt sân của bạn:</h3>
            <?php if ($booking_result->num_rows > 0): ?>
                <?php while ($booking = $booking_result->fetch_assoc()): ?>
                    <div class="booking">
                        <p><strong>Sân:</strong> <?php echo htmlspecialchars($booking['field_name']); ?></p>
                        <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($booking['field_location']); ?></p>
                        <p><strong>Giá:</strong> <?php echo number_format($booking['field_price'], 2); ?> VNĐ</p>
                        <p><strong>Ngày đặt:</strong> <?php echo htmlspecialchars($booking['booking_date']); ?></p>
                        <p><strong>Khách hàng:</strong> <?php echo htmlspecialchars($booking['customer_name']); ?></p>
                        <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($booking['customer_phone']); ?></p>

                        <button class="btn" onclick="toggleEditForm(<?php echo $booking['id']; ?>)">Sửa</button>
                        <button ><a href="index.php?booking_id=<?php echo $booking['id']; ?>" class="btn" onclick="return confirm('Bạn có chắc chắn muốn xóa đặt sân này?')">Xóa</a></button>

                        <!-- Biểu mẫu chỉnh sửa -->
                        <form action="index.php" method="POST" class="edit-form" id="edit-form-<?php echo $booking['id']; ?>" style="display: none;">
                            <input type="hidden" name="edit_booking_id" value="<?php echo $booking['id']; ?>">
                            <label for="customer_name">Tên khách hàng:</label>
                            <input type="text" name="customer_name" value="<?php echo htmlspecialchars($booking['customer_name']); ?>" required>
                            <label for="customer_phone">Số điện thoại:</label>
                            <input type="text" name="customer_phone" value="<?php echo htmlspecialchars($booking['customer_phone']); ?>" required>
                            <label for="booking_date">Ngày đặt sân:</label>
                            <input type="date" name="booking_date" value="<?php echo htmlspecialchars($booking['booking_date']); ?>" required>
                            <input type="submit" class="btn" value="Cập nhật">
                        </form>
                        <hr>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Bạn chưa có đặt sân nào.</p>
            <?php endif; ?>
        </section>

        <!-- Hiển thị danh sách sân bóng -->
        <section class="field-selection">
            <h3>Chọn Sân Bóng Để Đặt</h3>
            <?php if ($fields_result->num_rows > 0): ?>
                <?php while ($field = $fields_result->fetch_assoc()): ?>
                    <div class="field">
                        <h2><?php echo htmlspecialchars($field['name']); ?></h2>
                        <p>Địa chỉ: <?php echo htmlspecialchars($field['location']); ?></p>
                        <p>Giá: <?php echo number_format($field['price'], 2); ?> VNĐ</p>
                        <form action="index.php" method="POST">
                            <input type="hidden" name="field_id" value="<?php echo $field['id']; ?>">
                            <label for="customer_name">Tên khách hàng:</label>
                            <input type="text" name="customer_name" required>
                            <label for="customer_phone">Số điện thoại:</label>
                            <input type="text" name="customer_phone" required>
                            <label for="booking_date">Ngày đặt sân:</label>
                            <input type="date" name="booking_date" required>
                            <input type="submit" class="btn" value="Đặt Sân">
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Không có sân bóng nào!</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Đặt Sân Bóng Online</p>
    </footer>
</div>

<script>
    function toggleEditForm(bookingId) {
        const form = document.getElementById(`edit-form-${bookingId}`);
        form.style.display = form.style.display === "none" ? "block" : "none";
    }
</script>

</body>
</html>
