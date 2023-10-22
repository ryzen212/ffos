<?php if ($_settings->chk_flashdata('success')): ?>
	<script>
		alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
	</script>
<?php endif; ?>
<style>
	.order-logo {
		width: 3em;
		height: 3em;
		object-fit: cover;
		object-position: center center;
	}

	#order-field {
		height: 54em;
		overflow: auto;
	}

	.order-list {
		height: 18em;
		overflow: auto;
		position: relative;
	}

	.order-list-header {
		position: sticky;
		top: 0;
		z-index: 2 !important;
	}

	.order-body {
		position: relative;
		z-index: 1 !important;
	}

	#order-field:empty {
		display: flex;
		align-items: center;
		justify-content: center;
	}

	#order-field:empty:after {
		content: "No order has been queued yet.";
		color: #b7b4b4;
		font-size: 1.7em;
		font-style: italic;
	}
</style>



<div class="card card-outline rounded-0 card-warning">
	<div class="card-header">
		<h3 class="card-title">List of Orders</h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<table class="table table-hover table-striped table-bordered" id="list">

				<thead>
					<tr>
						<th>#</th>
						<th>Date Created</th>
						<th>Qr </th>
						<th>Status</th>
						<th>Items</th>


					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$uwhere = "";
					if (isset($_SESSION['customer_code'])) {
						$uwhere = " and code = '{$_SESSION['customer_code']}' ";
					} else {
						$uwhere = " and id is null";
					}


					$qry = $conn->query("SELECT * FROM `customer_order` where  delete_flag!=1  and status!=1 {$uwhere} order by abs(unix_timestamp(date_created)) desc ");
					while ($row = $qry->fetch_assoc()):
						?>
						<tr>
							<td class="text-center">
								<?php echo $i++; ?>
							</td>
							<td>
								<?php echo date("Y-m-d h:i a", strtotime($row['date_created'])) ?>
							</td>

							<td class="text-right">

								<div class="mb-0 text-center font-weight-bolder"><img src="<?= base_url . $row['qr_img'] ?>"
										alt="">
									<br><h6><?= $row['queue'] ?></h6></div>
							</td>
							<td class="text-center">
								<?php
								$Qqry = $conn->query("SELECT status,delete_flag FROM `order_list` where  customer_code = {$row['code']} and  customer_queue = {$row['queue']} ");
								$rowQ = $Qqry->fetch_assoc();
								if ($rowQ) {
									if ($rowQ['delete_flag'] == 1) {
										echo '<span class="badge badge-danger border-gradient-danger px-3 border"><h6>Cancelled</h6></span>';
									} else {
										switch ($rowQ['status']) {
											case 2:
												echo '<span class="badge badge-primary border-gradient-primary px-3 border"><h6>Queued</h6></span>';
												break;

											case 3:
												echo '<span class="badge badge-info border-gradient-info px-3 border"><h6>Preparing</h6></span>';
												break;
											case 4:
												echo '<span class="badge badge-success border-gradient-success text-light px-3 border"><h6>Ready to Serve</h6></span>';
												break;
											case 1:
												echo '<span class="badge badge-light border-gradient-light border px-3 border"><h6>Completed</h6></span>';
												break;

										}
									}




								} else {

									echo "<span class='badge badge-warning'><h6>Not Yet Queued</h6></span>";
								}

								?>
							</td>
							<td>
								<ul>
									<?php
									$items = $conn->query("SELECT oi.*, m.name as `item`,m.code,m.var_price FROM `customer_items` oi inner join `menu_list` m on oi.menu_id = m.id where oi.order_id = '{$row['id']}'");
									while ($rowItem = $items->fetch_assoc()) {
										$rowItem['isVariants'] = false;
										$variants = json_decode(stripslashes($rowItem['var_price']), true);
										if (count($variants) > 1) {
											$row['isVariants'] = true;
										}
										foreach ($variants as $values) {
											if ($rowItem['variants'] == $values['row_id']) {
												echo '<li>' . $rowItem['item'] . ' : ' . $values['var_name'] . '</li>';
											}


										}

										// echo $row['price'];
								
									}

									?>

								</ul>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>

	<!-- delete modal -->


	<script>
		$(document).ready(function () {
			$('.delete_data').click(function () {
				$('.err-msg').remove();
				$('#deleteModal').modal('show')
				$('#order_id').val($(this).attr('data-id'))

				$("#delete_frm")[0].reset()
				// _conf("Are you sure to delete this User permanently?","delete_user",[$(this).attr('data-id')])
			})
			$('.view_receipt').click(function () {


				var nw = window.open(_base_url_ + "admin/sales/print_receipt.php?id=" + $(this).attr('data-id'), '_blank', "width=" + ($(window).width() * .2) + ",left=" + ($(window).width() * .3) + ",height=" + ($(window).height() * .8) + ",top=" + ($(window).height() * .1))
				setTimeout(() => {
					nw.print()
					setTimeout(() => {
						nw.close()
						location.reload()
					}, 300);
				}, 200);

			})
			$('.table').dataTable({
				columnDefs: [
					{ orderable: false, targets: [2] }
				],
				order: [0, 'asc']
			});
			$('.dataTable td,.dataTable th').addClass('py-1 px-2 align-middle')
		})
		$('#delete_frm').submit(function (e) {
			e.preventDefault();
			$('.err-msg').remove();
			var _this = $(this)
			var data = $(this).serialize();
			start_loader();
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=delete_order",
				method: "POST",
				data: data,
				dataType: "json",
				error: err => {
					console.log(err)
					alert_toast("An error occured.", 'error');
					end_loader();
				},
				success: function (resp) {
					if (typeof resp == 'object' && resp.status == 'success') {
						location.reload();
					}
					if (typeof resp == 'object' && resp.status == 'failed') {
						var el = $('<div>')
						el.addClass("alert alert-danger err-msg").text(resp.msg)
						_this.prepend(el)
						el.show('slow')
						$('.modal').scrollTop(0);
						end_loader();
					}
					else {

						alert_toast("An error occured.", 'error');
						end_loader();
					}
				}
			})
		})
	</script>