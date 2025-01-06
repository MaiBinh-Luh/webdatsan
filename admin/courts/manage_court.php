<?php

require_once('../../config.php');
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM `court_list` WHERE id = '{$_GET['id']}' ");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
    }
}
?>
<div class="container-fluid">
    <form action="" id="court-form">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : ''; ?>">
        <div class="form-group">
            <label for="name" class="control-label">Tên Sân</label>
            <input type="text" name="name" id="name" class="form-control form-control-sm rounded-0" value="<?php echo isset($name) ? $name : ''; ?>" required />
        </div>
        <div class="form-group">
            <label for="price" class="control-label">Tỷ Giá Theo Giờ</label>
            <input type="number" step="any" name="price" id="price" class="form-control form-control-sm rounded-0 text-right" value="<?php echo isset($price) ? $price : ''; ?>" required />
        </div>
        <div class="form-group">
            <label for="quantity" class="control-label">Số Lượng Sân</label>
            <input type="number" name="quantity" id="quantity" class="form-control form-control-sm rounded-0" value="<?php echo isset($quantity) ? $quantity : ''; ?>" required />
        </div>
        <div class="form-group">
            <label for="status" class="control-label">Trạng Thái</label>
            <select name="status" id="status" class="form-control form-control-sm rounded-0" required>
                <option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : ''; ?>>Hoạt Động</option>
                <option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : ''; ?>>Không Hoạt Động</option>
            </select>
        </div>
    </form>
</div>

<script>
    $(document).ready(function () {
        $('#court-form').submit(function (e) {
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove();
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_court",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error: err => {
                    console.log(err);
                    alert_toast("Đã có lỗi xảy ra", 'error');
                    end_loader();
                },
                success: function (resp) {
                    if (typeof resp == 'object' && resp.status == 'success') {
                        location.reload();
                    } else if (resp.status == 'failed' && !!resp.msg) {
                        var el = $('<div>');
                        el.addClass("alert alert-danger err-msg").text(resp.msg);
                        _this.prepend(el);
                        el.show('slow');
                        $("html, body, .modal").scrollTop(0);
                        end_loader();
                    } else {
                        alert_toast("Đã có lỗi xảy ra", 'error');
                        end_loader();
                        console.log(resp);
                    }
                }
            });
        });
    });
</script>
