<?php
session_start();
include('includes/db.php');
// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Lấy thông tin quyền từ cơ sở dữ liệu
$user_id = $_SESSION['user_id'];
$query = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
$query->close();

// Kiểm tra quyền admin
if (!$user || $user['is_admin'] != 1) {
    echo "<script>
            alert('Bạn không phải là admin!');
            window.location.href = '/';
          </script>";
    exit;
}

// Xử lý sửa thông tin người dùng
if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $is_admin = $_POST['is_admin'];

    // Cập nhật thông tin người dùng
    $update_query = "UPDATE users SET username = ?, email = ?, is_admin = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('ssii', $username, $email, $is_admin, $user_id);
    $stmt->execute();
    $stmt->close();

    // Sau khi cập nhật, chuyển hướng trở lại trang quản trị
    header("Location: admin.php");
    exit;
}
// Xử lý sửa thông tin sân bóng
if (isset($_POST['update_field'])) {
    $field_id = $_POST['field_id'];
    $field_name = $_POST['field_name'];
    $field_location = $_POST['field_location'];
    $field_price = $_POST['field_price'];

    $update_query = "UPDATE fields SET name = ?, location = ?, price = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('ssdi', $field_name, $field_location, $field_price, $field_id);
    $stmt->execute();
    $stmt->close();

    // Sau khi cập nhật, chuyển hướng trở lại trang quản trị
    header("Location: admin.php");
    exit;
}

// Kiểm tra nếu có tham số edit_field trong URL
if (isset($_GET['edit_field'])) {
    $field_id = $_GET['edit_field'];
    // Lấy thông tin sân bóng theo ID
    $field_result = $conn->query("SELECT * FROM fields WHERE id = $field_id");
    if ($field_result->num_rows > 0) {
        $field = $field_result->fetch_assoc();
    } else {
        // Nếu không tìm thấy sân bóng, chuyển hướng về trang quản trị
        header("Location: admin.php");
        exit;
    }
}

// Xử lý sửa thông tin người dùng
if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $is_admin = $_POST['is_admin'];

    // Cập nhật thông tin người dùng
    $update_query = "UPDATE users SET username = ?, email = ?, is_admin = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('ssii', $username, $email, $is_admin, $user_id);
    $stmt->execute();
    $stmt->close();

    // Sau khi cập nhật, chuyển hướng trở lại trang quản trị
    header("Location: admin.php");
    exit;
}

// Kiểm tra nếu có tham số edit_user trong URL
if (isset($_GET['edit_user'])) {
    $user_id = $_GET['edit_user'];
    // Lấy thông tin người dùng theo ID
    $user_result = $conn->query("SELECT * FROM users WHERE id = $user_id");
    if ($user_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();
    } else {
        // Nếu không tìm thấy người dùng, chuyển hướng về trang quản trị
        header("Location: admin.php");
        exit;
    }
}

// Xử lý xóa sân bóng
if (isset($_GET['delete_field'])) {
    $field_id = $_GET['delete_field'];

    $delete_query = "DELETE FROM fields WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('i', $field_id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin.php");
    exit;
}

// Xử lý xóa người dùng
if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];

    $delete_query = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin.php");
    exit;
}

// Xử lý thêm người dùng
if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $is_admin = $_POST['is_admin'];

    $insert_query = "INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param('sssi', $username, $email, $password, $is_admin);
    $stmt->execute();
    $stmt->close();
}

// Xử lý thêm sân bóng
if (isset($_POST['add_field'])) {
    $field_name = $_POST['field_name'];
    $field_location = $_POST['field_location'];
    $field_price = $_POST['field_price'];

    $insert_query = "INSERT INTO fields (name, location, price) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param('ssd', $field_name, $field_location, $field_price);
    $stmt->execute();
    $stmt->close();
}

// Lấy thông tin tất cả các sân và người dùng
$fields_result = $conn->query("SELECT * FROM fields");
$users_result = $conn->query("SELECT * FROM users");

// Lấy thông tin tất cả các đặt sân từ bảng bookings kết hợp với bảng fields và users
$bookings_query = "
    SELECT b.id AS booking_id, b.booking_date, b.customer_name, b.customer_phone, 
           f.name AS field_name, u.username AS user_name
    FROM bookings b
    JOIN fields f ON b.field_id = f.id
    JOIN users u ON b.user_id = u.id
";
$bookings_result = $conn->query($bookings_query);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Trang Quản Trị</h1>
            <p>Chào, <?php echo $_SESSION['username']; ?>! Bạn đang ở trang quản trị.</p>
            <p><a href="logout.php">Đăng xuất</a></p>
        </header>

        <main>
            <!-- Danh Sách Đặt Sân -->
            <section>
                <h2>Danh Sách Đặt Sân</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID Đặt Sân</th>
                            <th>Tên Người Đặt</th>
                            <th>Điện Thoại</th>
                            <th>Tên Sân</th>
                            <th>Ngày Đặt</th>
                            <th>Người Dùng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($bookings_result->num_rows > 0) {
                            while ($row = $bookings_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['booking_id'] . "</td>";
                                echo "<td>" . $row['customer_name'] . "</td>";
                                echo "<td>" . $row['customer_phone'] . "</td>";
                                echo "<td>" . $row['field_name'] . "</td>";
                                echo "<td>" . $row['booking_date'] . "</td>";
                                echo "<td>" . $row['user_name'] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Không có thông tin đặt sân nào.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>            

            <!-- Danh Sách Sân Bóng -->
            <section>
                <h2>Danh Sách Sân Bóng</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID Sân</th>
                            <th>Tên Sân</th>
                            <th>Địa chỉ</th>
                            <th>Giá</th>
                            <th>Sửa</th>
                            <th>Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $fields_result = $conn->query("SELECT * FROM fields");
                        if ($fields_result->num_rows > 0) {
                            while ($row = $fields_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['id'] . "</td>";
                                echo "<td>" . $row['name'] . "</td>";
                                echo "<td>" . $row['location'] . "</td>";
                                echo "<td>" . number_format($row['price'], 2) . " VNĐ</td>";
                                echo "<td><a href='?edit_field=" . $row['id'] . "'>Sửa</a></td>";
                                echo "<td><a href='?delete_field=" . $row['id'] . "' onclick='return confirm(\"Bạn chắc chắn muốn xóa sân này?\")'>Xóa</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Không có sân nào.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                
                <h3>Thêm Sân Bóng</h3>
                <form method="POST">
                    <label for="field_name">Tên Sân:</label>
                    <input type="text" id="field_name" name="field_name" required><br>

                    <label for="field_location">Địa Chỉ:</label>
                    <input type="text" id="field_location" name="field_location" required><br>

                    <label for="field_price">Giá:</label>
                    <input type="number" id="field_price" name="field_price" step="0.01" required><br>

                    <button type="submit" name="add_field">Thêm Sân</button>
                </form>                

                <!-- Sửa sân bóng -->
                <?php if (isset($field)) { ?>
                    <h3>Sửa Sân Bóng</h3>
                    <form method="POST">
                        <input type="hidden" name="field_id" value="<?php echo $field['id']; ?>">

                        <label for="field_name_<?php echo $field['id']; ?>">Tên Sân:</label>
                        <input type="text" id="field_name_<?php echo $field['id']; ?>" name="field_name" value="<?php echo $field['name']; ?>" required><br>

                        <label for="field_location_<?php echo $field['id']; ?>">Địa Chỉ:</label>
                        <input type="text" id="field_location_<?php echo $field['id']; ?>" name="field_location" value="<?php echo $field['location']; ?>" required><br>

                        <label for="field_price_<?php echo $field['id']; ?>">Giá:</label>
                        <input type="number" id="field_price_<?php echo $field['id']; ?>" name="field_price" value="<?php echo $field['price']; ?>" step="0.01" required><br>

                        <button type="submit" name="update_field">Cập Nhật</button>
                    </form>
                <?php } ?>

            </section>

            <!-- Danh Sách Người Dùng -->
            <section>
                <h2>Danh Sách Người Dùng</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID Người Dùng</th>
                            <th>Tên Người Dùng</th>
                            <th>Email</th>
                            <th>Quyền Admin</th>
                            <th>Sửa</th>
                            <th>Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $users_result = $conn->query("SELECT * FROM users");
                        if ($users_result->num_rows > 0) {
                            while ($row = $users_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['id'] . "</td>";
                                echo "<td>" . $row['username'] . "</td>";
                                echo "<td>" . $row['email'] . "</td>";
                                echo "<td>" . ($row['is_admin'] ? 'Có' : 'Không') . "</td>";
                                echo "<td><a href='?edit_user=" . $row['id'] . "'>Sửa</a></td>";
                                echo "<td><a href='?delete_user=" . $row['id'] . "' onclick='return confirm(\"Bạn chắc chắn muốn xóa người dùng này?\")'>Xóa</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Không có người dùng nào.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <h3>Thêm Người Dùng</h3>
                <form method="POST">
                    <label for="username">Tên Người Dùng:</label>
                    <input type="text" id="username" name="username" required><br>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required><br>

                    <label for="password">Mật Khẩu:</label>
                    <input type="password" id="password" name="password" required><br>

                    <label for="is_admin">Quyền Quản Trị:</label>
                    <select id="is_admin" name="is_admin">
                        <option value="0">Không</option>
                        <option value="1">Có</option>
                    </select><br>

                    <button type="submit" name="add_user">Thêm Người Dùng</button>
                </form>

                <!-- Sửa người dùng -->
                <?php if (isset($user)) { ?>
                    <h3>Sửa Người Dùng</h3>
                    <form method="POST">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">

                        <label for="username_<?php echo $user['id']; ?>">Tên Người Dùng:</label>
                        <input type="text" id="username_<?php echo $user['id']; ?>" name="username" value="<?php echo $user['username']; ?>" required><br>

                        <label for="email_<?php echo $user['id']; ?>">Email:</label>
                        <input type="email" id="email_<?php echo $user['id']; ?>" name="email" value="<?php echo $user['email']; ?>" required><br>

                        <label for="is_admin_<?php echo $user['id']; ?>">Quyền Admin:</label>
                        <select id="is_admin_<?php echo $user['id']; ?>" name="is_admin">
                            <option value="1" <?php echo ($user['is_admin'] == 1) ? 'selected' : ''; ?>>Có</option>
                            <option value="0" <?php echo ($user['is_admin'] == 0) ? 'selected' : ''; ?>>Không</option>
                        </select><br>

                        <button type="submit" name="update_user">Cập Nhật</button>
                    </form>
                <?php } ?>

            </section>
        </main>
    </div>
</body>
</html>
