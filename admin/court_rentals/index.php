<?php if($_settings->userdata('type') == 1): ?>
    <!-- Type 1 user: Show full rental history -->
    <div class="card card-outline rounded-0 card-navy">
        <div class="card-header">
            <h3 class="card-title">Lịch Sử</h3>
            <div class="card-tools">
                <a href="./?page=court_rentals/manage_court_rental" class="btn btn-primary bg-gradient-primary btn-flat btn-sm"><i class="fa fa-plus"></i> Create New</a>
            </div>
        </div>
        <div class="card-body">
            <div class="container-fluid">
                <table class="table table-hover table-striped table-bordered" id="list">
                    <colgroup>
                        <col width="5%">
                        <col width="15%">
                        <col width="20%">
                        <col width="20%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date Created</th>
                            <th>Client</th>
                            <th>Court</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        $qry = $conn->query("SELECT cr.*, c.name as `court` FROM `court_rentals` cr inner join court_list c on cr.court_id = c.id order by c.`status` asc ");
                        while($row = $qry->fetch_assoc()):
                        ?>
                            <tr>
                                <td class="text-center"><?php echo $i++; ?></td>
                                <td><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
                                <td><?php echo $row['client_name'] ?></td>
                                <td><?php echo $row['court'] ?></td>
                                <td class=""><?= date("M d, Y h:i A", strtotime($row['datetime_start'])) ?></td>
                                <td class=""><?= date("M d, Y h:i A", strtotime($row['datetime_end'])) ?></td>
                                <td class="text-center">
                                    <?php
                                    switch($row['status']){
                                        case 0:
                                            echo '<span class="badge badge-secondary bg-gradient-secondary text-sm px-3 rounded-pill">On-Going</span>';
                                            break;
                                        case 1:
                                            echo '<span class="badge badge-success bg-gradient-teal text-sm px-3 rounded-pill">Done</span>';
                                            break;
                                    }
                                    ?>
                                </td>
                                <td align="center">
                                    <button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                        Action
                                    </button>
                                    <div class="dropdown-menu" role="menu">
                                        <a class="dropdown-item view_data" href="./?page=court_rentals/view_court_rental&id=<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span> View</a>

                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php elseif($_settings->userdata('type') == 2): ?>
    <!-- Type 2 user: Show search functionality -->
    <div class="card card-outline rounded-0 card-navy">
        <div class="card-header">
            <h3 class="card-title">Tìm Lịch Sử</h3>
            <div class="card-tools">
                <a href="./?page=court_rentals/manage_court_rental" class="btn btn-primary bg-gradient-primary btn-flat btn-sm"><i class="fa fa-plus"></i> Create New</a>
            </div>
        </div>
        <div class="card-body">
            <div class="container-fluid">
                <!-- Search Box for Type 2 -->
                <div class="mb-3">
                    <input type="text" id="search_input" class="form-control" placeholder="Nhập tên khách hàng đặt sân..">
                </div>

                <div class="rental-history-container">
                    <table class="table table-hover table-striped table-bordered" id="list">
                        <colgroup>
                            <col width="5%">
                            <col width="15%">
                            <col width="20%">
                            <col width="20%">
                            <col width="10%">
                            <col width="10%">
                            <col width="10%">
                            <col width="10%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date Created</th>
                                <th>Client</th>
                                <th>Court</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="rental_history">
                            <?php 
                            $i = 1;
                            $qry = $conn->query("SELECT cr.*, c.name as `court` FROM `court_rentals` cr inner join court_list c on cr.court_id = c.id order by c.`status` asc ");
                            while($row = $qry->fetch_assoc()):
                            ?>
                                <tr class="rental_row">
                                    <td class="text-center"><?php echo $i++; ?></td>
                                    <td><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
                                    <td><?php echo $row['client_name'] ?></td>
                                    <td><?php echo $row['court'] ?></td>
                                    <td class=""><?= date("M d, Y h:i A", strtotime($row['datetime_start'])) ?></td>
                                    <td class=""><?= date("M d, Y h:i A", strtotime($row['datetime_end'])) ?></td>
                                    <td class="text-center">
                                        <?php
                                        switch($row['status']){
                                            case 0:
                                                echo '<span class="badge badge-secondary bg-gradient-secondary text-sm px-3 rounded-pill">On-Going</span>';
                                                break;
                                            case 1:
                                                echo '<span class="badge badge-success bg-gradient-teal text-sm px-3 rounded-pill">Done</span>';
                                                break;
                                        }
                                        ?>
                                    </td>
                                    <td align="center">
                                        <button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                            Action
                                        </button>
                                        <div class="dropdown-menu" role="menu">
                                            <a class="dropdown-item view_data" href="./?page=court_rentals/view_court_rental&id=<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span> View</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
$(document).ready(function(){
    $('.delete_data').click(function(){
        _conf("Bạn có chắc chắn muốn xóa lịch sử thuê này không?","delete_court_rental",[$(this).attr('data-id')])
    });

    $('.table').dataTable({
        columnDefs: [
            { orderable: false, targets: [7] }
        ],
        order:[0,'asc']
    });

    $('.dataTable td,.dataTable th').addClass('py-1 px-2 align-middle');

    // Ẩn lịch sử khi trang tải, chỉ hiển thị khi có tìm kiếm hợp lệ
    $('.rental-history-container').hide();  // Ẩn toàn bộ lịch sử khi trang mới truy cập

    // Chức năng tìm kiếm cho người dùng loại 2
    <?php if($_settings->userdata('type') == 2): ?>
        $('#search_input').on('keyup', function() {
            var searchTerm = $(this).val().toLowerCase();
            var visibleRows = 0;  // Đếm số lượng dòng hiển thị
            var hasResults = false;  // Kiểm tra nếu có kết quả tìm kiếm hợp lệ

            // Nếu ô tìm kiếm trống, ẩn toàn bộ bảng lịch sử
            if (searchTerm === "") {
                $('.rental_row').hide();
                $('.rental-history-container').hide();  // Ẩn container lịch sử thuê khi ô tìm kiếm trống
                return;  // Dừng lại, không cần kiểm tra thêm
            }

            // Kiểm tra tìm kiếm trên các dòng
            $('.rental_row').each(function() {
                var client = $(this).find('td').eq(2).text().toLowerCase().trim();
                var court = $(this).find('td').eq(3).text().toLowerCase().trim();
                
                // Kiểm tra xem từ khóa tìm kiếm có khớp chính xác với tên khách hàng hoặc tên sân
                if (client === searchTerm || court === searchTerm) {
                    $(this).show();
                    visibleRows++;  // Tăng số lượng dòng hiển thị
                    hasResults = true;  // Có kết quả tìm kiếm hợp lệ
                } else {
                    $(this).hide();
                }
            });

            // Nếu không có kết quả tìm kiếm hợp lệ, ẩn container lịch sử thuê
            if (!hasResults) {
                $('.rental-history-container').hide();
            } else {
                $('.rental-history-container').show();  // Hiển thị lại container lịch sử nếu có kết quả
            }
        });
    <?php endif; ?>
});

</script>
