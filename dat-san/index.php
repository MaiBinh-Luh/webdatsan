<?php
session_start(); // Khởi tạo session

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
    header('Location: /dangnhap.php');
    exit; // Dừng lại để không thực thi tiếp mã
}

require_once('../initialize.php');

try {
    // Kết nối tới cơ sở dữ liệu
    $conn = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USERNAME, DB_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Lấy lịch sử đặt sân của người dùng từ bảng sales_transaction
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM sales_transaction WHERE user_id = :user_id ORDER BY date_created DESC");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy dữ liệu sản phẩm từ bảng product_list
    $stmt = $conn->prepare("SELECT id, name, price, img_url, quantity FROM product_list WHERE status = 1 AND delete_flag = 0");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC); // Lấy tất cả sản phẩm

    // Nếu có sản phẩm, chia thành 4 block
    $chunks = array_chunk($products, 4);

    // Kiểm tra nếu form được gửi (customer info)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['client_name'], $_POST['contact'], $_POST['product_id'])) {
        // Nhận thông tin từ form
        $client_name = $_POST['client_name'];
        $contact = $_POST['contact'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        $total = 0.00;

        // Lấy thông tin sản phẩm từ bảng product_list
        $stmt = $conn->prepare("SELECT * FROM product_list WHERE id = :product_id");
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        // Tính toán số tiền đơn hàng
        $price = $product['price'];
        $amount = $price * $quantity;
        $total += $amount;

        // Chèn thông tin vào bảng sales_transaction
        $date_created = date("Y-m-d H:i:s");
        $stmt = $conn->prepare("INSERT INTO sales_transaction (client_name, contact, total, court_rental_id, date_created, date_updated, quantity, img_url, user_id) 
                                VALUES (:client_name, :contact, :total, :court_rental_id, :date_created, :date_updated, :quantity, :img_url, :user_id)");
        $stmt->bindParam(':client_name', $client_name);
        $stmt->bindParam(':contact', $contact);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':court_rental_id', $court_rental_id); // If applicable
        $stmt->bindParam(':date_created', $date_created);
        $stmt->bindParam(':date_updated', $date_created); // assuming created and updated time are the same initially
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':img_url', $product['img_url']); // Adding the image URL from the product
        $stmt->bindParam(':user_id', $_SESSION['user_id']); // Lưu user_id
        $stmt->execute();

        // Lấy ID của giao dịch
        $sales_transaction_id = $conn->lastInsertId();

        // Chèn sản phẩm vào bảng sales_transaction_items
        $stmt = $conn->prepare("INSERT INTO sales_transaction_items (sales_transaction_id, product_id, price, quantity) 
                                VALUES (:sales_transaction_id, :product_id, :price, :quantity)");
        $stmt->bindParam(':sales_transaction_id', $sales_transaction_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->execute();

        // Cập nhật số lượng sản phẩm trong bảng product_list
        $stmt = $conn->prepare("UPDATE product_list SET quantity = quantity - :quantity WHERE id = :product_id");
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();

        echo "Đơn hàng đã được thêm thành công!";
    }

    // Kiểm tra nếu có yêu cầu xóa
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['transaction_id'])) {
        $transaction_id = $_GET['transaction_id'];

        // Xóa các mục liên quan trong bảng sales_transaction_items
        $stmt = $conn->prepare("DELETE FROM sales_transaction_items WHERE sales_transaction_id = :transaction_id");
        $stmt->bindParam(':transaction_id', $transaction_id);
        $stmt->execute();

        // Xóa giao dịch trong bảng sales_transaction
        $stmt = $conn->prepare("DELETE FROM sales_transaction WHERE id = :transaction_id");
        $stmt->bindParam(':transaction_id', $transaction_id);
        $stmt->execute();

        // Chuyển hướng lại trang để cập nhật dữ liệu
        header('Location: /dat-san'); // Thay đổi đường dẫn nếu cần thiết
        exit;
    }

} catch (PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Đặt sân hôm nay</title>
    <style>
        /* Tổng quan */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #f9f9fb;
            color: #333;
        }

        /* Header */
        header {
            background: #007BFF;
            color: white;
            text-align: center;
            padding: 1rem 0;
            font-size: 1.5rem;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Menu Navigation */
        nav {
            display: flex;
            justify-content: center;
            background: #343a40;
            padding: 0.5rem;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 1rem;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        nav a:hover {
            background: #007BFF;
        }

        /* Tiêu đề */
        h1 {
            text-align: center;
            color: #007BFF;
            margin: 2rem 0;
            font-size: 2rem;
        }

        .products {
            display: flex;
            flex-wrap: wrap;
            gap: 10px; /* Khoảng cách giữa các sản phẩm */
            justify-content: center;
        }

        .product-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .product-block {
            background-color: #fff;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            width: calc(50% - 10px); /* Mỗi sản phẩm chiếm 50% chiều rộng */
            box-sizing: border-box;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 300px; /* Giới hạn chiều rộng */
        }

        .product-block img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .product-block input,
        .product-block button {
            width: 100%; /* Đảm bảo input và button nằm gọn trong block */
            padding: 0.4rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box; /* Đảm bảo kích thước hợp lý */
        }

        .product-block input:focus {
            outline: none;
            border-color: #007BFF;
        }

        .product-block button {
            background: #007BFF;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .product-block button:hover {
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

        th, td {
            padding: 8px;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        /* Footer */
        footer {
            background: #343a40;
            color: white;
            text-align: center;
            padding: 1rem 0;
            margin-top: 2rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <!--<header>-->
    <!--    <div>LOGO</div>-->
    <!--</header>-->

    <!-- Menu Navigation -->
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

    <!-- Tiêu đề -->
    <h1>Đặt Sân Hôm Nay</h1>

    <!-- Sản phẩm có sẵn để đặt sân -->
    <div class="products">
        <?php foreach ($chunks as $chunk): ?>
            <div class="product-row">
                <?php foreach ($chunk as $product): ?>
                    <div class="product-block">
                        <img src="<?php echo $product['img_url']; ?>" alt="<?php echo $product['name']; ?>">
                        <h4><?php echo $product['name']; ?></h4>
                        <p>Giá: <?php echo number_format($product['price'], 0, ',', '.'); ?> VND/ 1 GIỜ</p>
                        <p>Còn lại: <?php echo $product['quantity']; ?> sân</p>

                        <!-- Form điền thông tin khách hàng -->
                        <form action="" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                            <label for="client_name">Tên khách hàng:</label>
                            <input type="text" id="client_name" name="client_name" required><br>

                            <label for="contact">Số điện thoại:</label>
                            <input type="text" id="contact" name="contact" required><br>

                            <label for="quantity">Số lượng:</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['quantity']; ?>" required><br>

                            <button type="submit">Đặt sân</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Lịch sử đặt sân của người dùng -->
    <h2>Lịch sử đặt sân của bạn</h2>
    <table>
        <thead>
            <tr>
                <th>Ngày</th>
                <th>Sân</th>
                <th>Số lượng</th>
                <th>Tổng tiền</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?php echo $transaction['date_created']; ?></td>
                    <td>
                        <?php
                        // Lấy thông tin sản phẩm từ sales_transaction_items
                        $stmt = $conn->prepare("SELECT p.name FROM sales_transaction_items sti
                                                JOIN product_list p ON sti.product_id = p.id
                                                WHERE sti.sales_transaction_id = :sales_transaction_id");
                        $stmt->bindParam(':sales_transaction_id', $transaction['id']);
                        $stmt->execute();
                        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($items as $item) {
                            echo $item['name'] . '<br>';
                        }
                        ?>
                    </td>
                    <td><?php echo $transaction['quantity']; ?></td>
                    <td><?php echo number_format($transaction['total'], 0, ',', '.'); ?> VND</td>
                    <td>
                        <!-- Nút xóa giao dịch -->
                        <a href="?action=delete&transaction_id=<?php echo $transaction['id']; ?>" onclick="return confirm('Bạn chắc chắn muốn xóa giao dịch này?')">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
                    
</body>
</html>
