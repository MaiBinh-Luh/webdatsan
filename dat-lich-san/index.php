    <?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: /dangnhap.php');
    exit;
}

require_once('../initialize.php');

// Kết nối cơ sở dữ liệu
try {
    $conn = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USERNAME, DB_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}

$username = $_SESSION['username']; // Tên người dùng từ session

// Xử lý tạo đặt sân
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_booking'])) {
    $customerName = htmlspecialchars($_POST['customerName']);
    $contact = htmlspecialchars($_POST['contact']);
    $courtId = (int)$_POST['court_id'];
    $courtPrice = (float)$_POST['court_price'];
    $startDate = $_POST['startDate'];
    $timeSlot = $_POST['time_slot'];  // Lấy khung giờ từ form

    // Tách start_time và end_time từ khung giờ đã chọn
    list($startTime, $endTime) = explode('-', $timeSlot);

    // Tính toán thời gian
    $startDateTime = strtotime("$startDate $startTime");
    $endDateTime = strtotime("$startDate $endTime");

    if ($endDateTime <= $startDateTime) {
        die("Thời gian kết thúc phải lớn hơn thời gian bắt đầu.");
    }

    // Kiểm tra xem khung giờ đã có người đặt chưa
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM court_rentals 
        WHERE court_id = :court_id 
        AND (
            (datetime_start < :end_datetime AND datetime_end > :start_datetime)
        )
    ");
    $stmt->execute([
        ':court_id' => $courtId,
        ':start_datetime' => date('Y-m-d H:i:s', $startDateTime),
        ':end_datetime' => date('Y-m-d H:i:s', $endDateTime)
    ]);

    $existingBookings = $stmt->fetchColumn();

    if ($existingBookings > 0) {
        die("Khung giờ đã được đặt. Vui lòng chọn một khung giờ khác.");
    }

    $totalHours = ($endDateTime - $startDateTime) / 3600;
    $totalPrice = $courtPrice * $totalHours;

    // Kiểm tra số lượng sân
    $stmt = $conn->prepare("SELECT quantity FROM court_list WHERE id = :court_id");
    $stmt->execute([':court_id' => $courtId]);
    $court = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($court && $court['quantity'] > 0) {
        $conn->beginTransaction();

        try {
            // Giảm số lượng sân
            $stmt = $conn->prepare("UPDATE court_list SET quantity = quantity - 1 WHERE id = :court_id");
            $stmt->execute([':court_id' => $courtId]);

            // Thêm đặt sân
            $stmt = $conn->prepare("
                INSERT INTO court_rentals (client_name, contact, court_id, court_price, datetime_start, datetime_end, hours, total, status, created_by, date_created, date_updated)
                VALUES (:client_name, :contact, :court_id, :court_price, :datetime_start, :datetime_end, :hours, :total, 0, :created_by, NOW(), NOW())
            ");
            $stmt->execute([
                ':client_name' => $customerName,
                ':contact' => $contact,
                ':court_id' => $courtId,
                ':court_price' => $courtPrice,
                ':datetime_start' => date('Y-m-d H:i:s', $startDateTime),
                ':datetime_end' => date('Y-m-d H:i:s', $endDateTime),
                ':hours' => $totalHours,
                ':total' => $totalPrice,
                ':created_by' => $username
            ]);

            $conn->commit();
            echo "Đặt sân thành công!";
        } catch (Exception $e) {
            $conn->rollBack();
            die("Có lỗi xảy ra: " . $e->getMessage());
        }
    } else {
        echo "Sân này đã hết chỗ. Vui lòng chọn sân khác!";
    }
}

// Xử lý yêu cầu xóa đơn đặt sân
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_booking'])) {
    $bookingId = $_POST['booking_id'];

    // Xóa booking và cập nhật lại số lượng sân
    $sqlCheck = "SELECT * FROM court_rentals WHERE id = :id AND created_by = :username";
    $stmt = $conn->prepare($sqlCheck);
    $stmt->execute([':id' => $bookingId, ':username' => $_SESSION['username']]);
    $rental = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($rental) {
        // Xóa đơn đặt sân
        $sqlDelete = "DELETE FROM court_rentals WHERE id = :id";
        $stmt = $conn->prepare($sqlDelete);
        $stmt->execute([':id' => $bookingId]);

        // Trả lại số lượng sân
        $sqlUpdateQuantity = "UPDATE court_list SET quantity = quantity + 1 WHERE id = :court_id";
        $stmt = $conn->prepare($sqlUpdateQuantity);
        $stmt->execute([':court_id' => $rental['court_id']]);

        echo "success"; // Xóa thành công
    } else {
        echo "Không tìm thấy đơn đặt sân.";
    }
}

// Lấy lịch sử đặt sân
$stmt = $conn->prepare("
    SELECT r.id, r.client_name, r.contact, c.name AS court_name, r.court_price, r.datetime_start, r.datetime_end, r.hours, r.total
    FROM court_rentals r
    JOIN court_list c ON r.court_id = c.id
    WHERE r.created_by = :username
    ORDER BY r.date_created DESC
");
$stmt->execute([':username' => $username]);
$userRentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Sân Bóng Đá</title>
    <style>
        /* Tổng quan */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f9;
            color: #333;
        }

        h1, h2 {
            text-align: center;
            color: #007BFF;
        }

        /* Header */
        header {
            background: #007BFF;
            color: #fff;
            text-align: center;
            padding: 1rem 0;
            font-size: 1.5rem;
            font-weight: bold;
        }

        /* Menu Navigation */
        nav {
            display: flex;
            justify-content: center;
            background: #343a40;
            padding: 0.5rem;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            margin: 0 1rem;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background 0.3s;
        }

        nav a:hover {
            background: #007BFF;
        }

        /* Form */
        .container {
            width: 90%;
            max-width: 800px;
            margin: 2rem auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }

        label {
            display: block;
            margin: 1rem 0 0.5rem;
            font-weight: bold;
        }

        input, select, button {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #007BFF;
        }

        button {
            background: #007BFF;
            color: #fff;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #0056b3;
        }

        /* Bảng danh sách */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }

        table th, table td {
            text-align: left;
            padding: 0.75rem;
            border: 1px solid #ddd;
        }

        table th {
            background: #007BFF;
            color: #fff;
        }

        table tr:nth-child(even) {
            background: #f4f4f9;
        }

        /* Hiệu ứng responsive */
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                align-items: center;
            }

            nav a {
                margin: 0.5rem 0;
            }

            .container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!--<header>-->
    <!--    <div>LOGO</div>-->
    <!--</header>-->

    <nav>
        <a href="/">Trang Chủ</a>
        <a href="/dat-san">Đặt sân</a>
        <!--<a href="/">Giới thiệu</a>-->
        <!--<a href="/">Liên Hệ</a>-->
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/admin">Quản lý</a>
            <a href="/dangxuat.php">Đăng xuất</a>
        <?php else: ?>
            <a href="/dangnhap.php">Đăng nhập</a>
        <?php endif; ?>
    </nav>

    <h1>Đặt Lịch Sân</h1>

    <div class="container">
        <form method="POST">
            <label for="customerName">Tên Khách Hàng:</label>
            <input type="text" id="customerName" name="customerName" required>

            <label for="contact">Liên Hệ Sân Bóng:</label>
            <input type="text" id="contact" name="contact" required>

            <label for="court_id">Chọn Sân:</label>
            <select name="court_id" id="court_id" required onchange="updateCourtDetails()">
                <option value="" disabled selected>Chọn sân</option>
                <?php
                // Fetch court options from the database
                $sql = "SELECT id, name, price, quantity FROM court_list WHERE status = 1 AND delete_flag = 0";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $courts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($courts as $court) {
                    echo "<option value='" . $court['id'] . "' data-price='" . $court['price'] . "' data-quantity='" . $court['quantity'] . "'>" 
                        . $court['name'] . " - Giá: " . number_format($court['price'], 0, ',', '.') . " VND - Còn: " . $court['quantity'] . "</option>";
                }
                ?>
            </select>

            <!-- Trường ẩn để lưu giá sân -->
            <input type="hidden" id="court_price" name="court_price" value="">

            <!-- Hiển thị giá sân cho người dùng -->
            <p>Giá sân: <span id="courtPriceDisplay">0</span> VND / GIỜ</p>

            <label for="startDate">Ngày và Giờ Bắt Đầu:</label>
            <input type="date" id="startDate" name="startDate" required>

            <label for="time_slot">Chọn Khung Giờ:</label>
            <select name="time_slot" id="time_slot" required>
                <option value="09:00-10:00" data-start="09:00" data-end="10:00">9 GIỜ - 10 GIỜ SÁNG</option>
                <option value="10:00-11:00" data-start="10:00" data-end="11:00">10 GIỜ - 11 GIỜ SÁNG</option>
                <option value="12:00-12:00" data-start="11:00" data-end="12:00">11 GIỜ - 12 GIỜ SÁNG</option>
                <option value="12:00-01:00" data-start="12:00" data-end="01:00">12 GIỜ- 1 GIỜ TRƯA</option>                
                <option value="01:00-02:00" data-start="01:00" data-end="02:00">1 GIỜ - 2 GIỜ TRƯA</option>
                <option value="02:00-03:00" data-start="02:00" data-end="03:00">2 GIỜ - 3 GIỜ TRƯA</option>     
                <option value="03:00-04:00" data-start="03:00" data-end="04:00">3 GIỜ - 4 GIỜ TRƯA</option>  
                <option value="04:00-05:00" data-start="04:00" data-end="05:00">4 GIỜ - 5 GIỜ TỐI</option>       
                <option value="05:00-06:00" data-start="05:00" data-end="06:00">5 GIỜ - 6 GIỜ TỐI</option>                     
                <option value="06:00-07:00" data-start="06:00" data-end="07:00">6 GIỜ- 7 GIỜ TỐI</option>
                <option value="07:00-08:00" data-start="07:00" data-end="08:00">7 GIỜ - 8 GIỜ TỐI</option>
                <option value="08:00-09:00" data-start="08:00" data-end="09:00">8 GIỜ- 9 GIỜ TỐI</option>                
            </select>

            <button type="submit" name="create_booking">Tạo Đặt Sân</button>
        </form>
    </div>

    <h2>Lịch Sử Đặt Sân</h2>
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Tên Khách Hàng</th>
                    <th>Sân</th>
                    <th>Giá</th>
                    <th>Giờ</th>
                    <th>Thành Tiền</th>
                    <th>Thao Tác</th> 
                </tr>
            </thead>
            <tbody>
                <?php foreach ($userRentals as $rental): ?>
                    <tr>
                        <td><?= $rental['client_name'] ?></td>
                        <td><?= $rental['court_name'] ?></td>
                        <td><?= number_format($rental['court_price'], 0, ',', '.') ?> VND</td>
                        <td><?= date('d-m-Y H:i', strtotime($rental['datetime_start'])) ?> - <?= date('d-m-Y H:i', strtotime($rental['datetime_end'])) ?></td>
                        <td><?= number_format($rental['total'], 0, ',', '.') ?> VND</td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="booking_id" value="<?= $rental['id']; ?>">
                                <button type="submit" name="delete_booking">Xóa</button>
                            </form>
                        </td>                        
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Cập nhật giá sân khi người dùng chọn sân
        function updateCourtDetails() {
            var select = document.getElementById('court_id');
            var selectedOption = select.options[select.selectedIndex];
            var price = selectedOption.getAttribute('data-price');
            document.getElementById('court_price').value = price;
            document.getElementById('courtPriceDisplay').textContent = price;
        }
    </script>

</body>
</html>
