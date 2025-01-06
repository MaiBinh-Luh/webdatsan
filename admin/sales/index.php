<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<div class="card card-outline rounded-0 card-navy">
	<div class="card-header">
		<h3 class="card-title">List of Sales</h3>
		<div class="card-tools">
			<a href="./?page=sales/manage_sale" id="create_new" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div>
	</div>
	<div class="card-body">
        <div class="container-fluid">
			<table class="table table-hover table-striped table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="25%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Date Created</th>
						<th>Client</th>
						<th>Items</th>
						<th>Total Amount</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
					$qry = $conn->query("SELECT st.*, COALESCE((SELECT COUNT(product_id) FROM `sales_transaction_items` where sales_transaction_id = st.id), 0) as `items` 
											FROM `sales_transaction` st
											ORDER BY unix_timestamp(st.date_created) DESC");

					while($row = $qry->fetch_assoc()):
						// Get the product details for each sale transaction
						$product_query = $conn->query("SELECT p.name, p.img_url, p.quantity 
														FROM `sales_transaction_items` sti
														JOIN `product_list` p ON sti.product_id = p.id
														WHERE sti.sales_transaction_id = {$row['id']}");
						$product = $product_query->fetch_assoc();
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td><?php echo $row['client_name'] ?></td>
							<td class="text-right">
								<!-- Displaying Product Image and Quantity -->
								<?php if($product): ?>
									<img src="<?php echo $product['img_url']; ?>" alt="Product Image" style="width: 50px; height: auto;">
									<p><?php echo $product['name']; ?></p>
									<p>Còn lại: <?php echo $product['quantity']; ?> sản phẩm</p>
								<?php endif; ?>
							</td>
							<td class="text-right"><?php echo format_num($row['total'],2) ?></td>
							<td align="center">
								<button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
									Action
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<div class="dropdown-menu" role="menu">
									<a class="dropdown-item view_data" href="./?page=sales/view_details&id=<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span> View</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item edit_data" href="./?page=sales/manage_sale&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>

								</div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this sale permanently?","delete_sale",[$(this).attr('data-id')])
		})
		$('.table').dataTable({
			columnDefs: [
					{ orderable: false, targets: [5] }
			],
			order:[0,'asc']
		});
		$('.dataTable td,.dataTable th').addClass('py-1 px-2 align-middle')
	})
	function delete_sale($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_sale",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>
