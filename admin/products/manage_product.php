<?php
require_once('../../config.php');

// Lấy thông tin sản phẩm nếu có ID
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `product_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nhận thông tin từ form
    $name = $_POST['name'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $img_url = $_POST['img_url'];  // Lấy URL ảnh từ form
    $quantity = $_POST['quantity']; // Lấy số lượng sản phẩm từ form
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    
    // Kiểm tra nếu URL ảnh có tồn tại
    if (!empty($img_url) && filter_var($img_url, FILTER_VALIDATE_URL) === false) {
        echo json_encode(['status' => 'failed', 'msg' => 'URL ảnh không hợp lệ.']);
        exit();
    }

    // Thêm hoặc cập nhật thông tin sản phẩm vào cơ sở dữ liệu
    if (!empty($id)) {
        // Cập nhật sản phẩm nếu có ID
        $query = "UPDATE `product_list` SET `name` = '$name', `price` = '$price', `status` = '$status', `img_url` = '$img_url', `quantity` = '$quantity' WHERE `id` = '$id'";
    } else {
        // Thêm mới sản phẩm
        $query = "INSERT INTO `product_list` (`name`, `price`, `status`, `img_url`, `quantity`) VALUES ('$name', '$price', '$status', '$img_url', '$quantity')";
    }

    if ($conn->query($query)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'failed', 'msg' => 'Đã xảy ra lỗi khi lưu sản phẩm.']);
    }
}

?>

<div class="container-fluid">
    <form action="" id="product-form" method="POST">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : ''; ?>">

        <!-- Tên sản phẩm -->
        <div class="form-group">
            <label for="name" class="control-label">Name</label>
            <input type="text" name="name" id="name" class="form-control form-control-sm rounded-0" value="<?php echo isset($name) ? $name : ''; ?>" required />
        </div>

        <!-- Giá sản phẩm -->
        <div class="form-group">
            <label for="price" class="control-label">Price</label>
            <input type="number" name="price" id="price" class="form-control form-control-sm rounded-0 text-right" value="<?php echo isset($price) ? $price : ''; ?>" required />
        </div>

        <!-- Số lượng sản phẩm -->
        <div class="form-group">
            <label for="quantity" class="control-label">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control form-control-sm rounded-0" value="<?php echo isset($quantity) ? $quantity : ''; ?>" required min="1" />
        </div>

        <!-- Trạng thái sản phẩm -->
        <div class="form-group">
            <label for="status" class="control-label">Status</label>
            <select name="status" id="status" class="form-control form-control-sm rounded-0" required>
                <option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : ''; ?>>Active</option>
                <option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>

        <!-- URL ảnh sản phẩm -->
        <div class="form-group">
            <label for="img_url" class="control-label">Product Image URL</label>
            <input type="text" name="img_url" id="img_url" class="form-control form-control-sm rounded-0" value="<?php echo isset($img_url) ? $img_url : ''; ?>" required />
            <?php if (isset($img_url) && $img_url): ?>
                <img src="<?php echo $img_url; ?>" alt="Product Image" class="mt-2" style="width: 100px; height: auto;">
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Save Product</button>
    </form>
</div>

<script>
    $(document).ready(function(){
        $('#product-form').submit(function(e){
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove();
            start_loader();

            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_product",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                dataType: 'json',
                error: function(err) {
                    console.log(err);
                    alert_toast("An error occurred", 'error');
                    end_loader();
                },
                success: function(resp) {
                    if (typeof resp == 'object' && resp.status == 'success') {
                        location.reload();  // reload the page after success
                    } else if (resp.status == 'failed' && !!resp.msg) {
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg);
                            _this.prepend(el);
                            el.show('slow');
                            $("html, body, .modal").scrollTop(0);
                            end_loader();
                    } else {
                        alert_toast("An error occurred", 'error');
                        end_loader();
                        console.log(resp);
                    }
                }
            });
        });
    });
</script>
