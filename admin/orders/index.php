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

	.nav-link.active {
		background: #ffc107 linear-gradient(180deg, #ffca2c, #ffc107) repeat-x !important;
		color: #fff !important;
	}

	.nav-link {
		color: black
	}

	.nav-pills .nav-link:not(.active):hover {
		color: #ffca2c;
	}
</style>
<ul class="nav nav-tabs nav-pills nav-fill" id="myTab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="que_tab" data-toggle="tab" href="#order_que" role="tab" aria-controls="order_que"
			aria-selected="true">
			<h5>Orders <span id='badge-que' class="badge badge-danger"></span></h5>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="served_tab" data-toggle="tab" href="#order_served" role="tab" aria-controls="served_tab"
			aria-selected="false">
			<h5>Served Orders</h5>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="prepare_tab" data-toggle="tab" href="#order_prepare" role="tab"
			aria-controls="order_prepare" aria-selected="false">
			<h5>Deleted Orders</h5>
		</a>
	</li>

</ul>

<div class="tab-content" id="myTabContent" style="height:48em;overflow:auto;">
	<div class="tab-pane fade show active" id="order_que" role="tabpanel" aria-labelledby="que_tab">
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
								<th>Transaction Code</th>
								<th>Queue</th>
								<th>Total Amount</th>
								<th>Notes</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 1;
							$uwhere = "";
							if ($_settings->userdata('type') != '1')
								$uwhere = " and user_id = '{$_settings->userdata('id')}' ";

							$qry = $conn->query("SELECT * FROM `order_list` where  delete_flag!=1  and status!=1 {$uwhere} order by abs(unix_timestamp(date_created)) desc ");
							while ($row = $qry->fetch_assoc()):
								?>
								<tr>
									<td class="text-center">
										<?php echo $i++; ?>
									</td>
									<td>
										<?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?>
									</td>
									<td class="">
										<?= $row['code'] ?>
									</td>
									<td class="">
										<?= $row['queue'] ?>
									</td>
									<td class="text-right">
										<?= format_num($row['total_amount'], 2) ?>
									</td>
									<td class="text-right">
										<?= (empty($row['notes'])) ? ('N/A') : ($row['notes']); ?>
									</td>

									<td class="text-center">
										<?php
										switch ($row['status']) {
											case 2:
												echo '<span class="badge badge-warning border-gradient-danger px-3 border">Queued</span>';
												break;

											case 3:
												echo '<span class="badge badge-info border-gradient-info px-3 border">Preparing</span>';
												break;
											case 4:
												echo '<span class="badge badge-success border-gradient-success text-light px-3 border">Ready to Serve</span>';
												break;
											default:
												echo '<span class="badge badge-light border-gradient-light border px-3 border">N/A</span>';
												break;

										}
										?>
									</td>
									<?php if ($row['user_id'] == $_settings->userdata('id')) { ?>
										<td class="text-center">
											<div class="btn-group btn-group-sm">
												<a class="btn btn-flat btn-sm btn-light bg-gradient-light border view_receipt"
													href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"
													title="Print Receipt"><small><span class="fa fa-receipt"></span></small></a>
												<a class="btn btn-flat btn-sm btn-danger bg-gradient-danger delete_data"
													href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"
													title="Delete Order"><small><span class="fa fa-trash"></span></small></a>
											</div>
										</td>
									<?php } else {
										$stmt = $conn->prepare("SELECT CONCAT(`firstname` ,' ',`lastname`)as name from users where id = ? ");
										$stmt->bind_param('s', $row['user_id']);
										$stmt->execute();
										$result = $stmt->get_result();
										$value = $result->fetch_object();

										?>
										<td class="text-center">
											<?= 'Cashier name :' . $value->name; ?>
										</td>
									<?php } ?>
								</tr>
							<?php endwhile; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="order_served" role="tabpanel" aria-labelledby="prepare-tab">
		<div class="card card-outline rounded-0 card-warning">
			<div class="card-header">
				<h3 class="card-title">List of Served Orders</h3>
			</div>
			<div class="card-body">
				<div class="container-fluid">
					<table class="table table-hover table-striped table-bordered" id="list">

						<thead>
							<tr>
								<th>#</th>
								<th>Date Created</th>
								<th>Transaction Code</th>
								<th>Queue</th>
								<th>Total Amount</th>
								<th>Notes</th>
								<th>Cashier Name</th>
								<th>Status</th>

							</tr>
						</thead>
						<tbody>
							<?php
							$i = 1;
							$uwhere = "";
							if ($_settings->userdata('type') != '1')
								$uwhere = " and user_id = '{$_settings->userdata('id')}' ";

							$qry = $conn->query("SELECT * FROM `order_list` where  status = 1 and delete_flag !=1 {$uwhere} order by abs(unix_timestamp(date_created)) desc ");
							while ($row = $qry->fetch_assoc()):
								?>
								<tr>
									<td class="text-center">
										<?php echo $i++; ?>
									</td>
									<td>
										<?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?>
									</td>
									<td class="">
										<?= $row['code'] ?>
									</td>
									<td class="">
										<?= $row['queue'] ?>
									</td>
									<td class="text-right">
										<?= format_num($row['total_amount'], 2) ?>
									</td>
									<td class="text-right">
										<?= (empty($row['notes'])) ? ('N/A') : ($row['notes']); ?>
									</td>
									<?php
									$stmt = $conn->prepare("SELECT CONCAT(`firstname` ,' ',`lastname`)as name from users where id = ? ");
									$stmt->bind_param('s', $row['user_id']);
									$stmt->execute();
									$result = $stmt->get_result();
									$value = $result->fetch_object();

									?>
									<td class="text-center">
										<?= 'Cashier name :' . $value->name; ?>
									</td>
									<td class="text-center">
										<span class="badge badge-success border-gradient-success px-3 border">Served</span>
									</td>

								</tr>
							<?php endwhile; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="order_prepare" role="tabpanel" aria-labelledby="prepare-tab">
		<div class="card card-outline rounded-0 card-warning">
			<div class="card-header">
				<h3 class="card-title">List of Deleted Orders</h3>
			</div>
			<div class="card-body">
				<div class="container-fluid">
					<table class="table table-hover table-striped table-bordered" id="list">

						<thead>
							<tr>
								<th>#</th>
								<th>Date Created</th>
								<th>Transaction Code</th>
								<th>Queue</th>
								<th>Total Amount</th>
								<th>Notes</th>
								<th>Cashier Name</th>
								<th>Reason</th>

							</tr>
						</thead>
						<tbody>
							<?php
							$i = 1;
							$uwhere = "";
							if ($_settings->userdata('type') != '1')
								$uwhere = " and user_id = '{$_settings->userdata('id')}' ";

							$qry = $conn->query("SELECT * FROM `order_list` where  delete_flag =1  {$uwhere} order by abs(unix_timestamp(date_created)) desc ");
							while ($row = $qry->fetch_assoc()):
								?>
								<tr>
									<td class="text-center">
										<?php echo $i++; ?>
									</td>
									<td>
										<?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?>
									</td>
									<td class="">
										<?= $row['code'] ?>
									</td>
									<td class="">
										<?= $row['queue'] ?>
									</td>
									<td class="text-right">
										<?= format_num($row['total_amount'], 2) ?>
									</td>
									<td class="text-right">
										<?= (empty($row['notes'])) ? ('N/A') : ($row['notes']); ?>
									</td>
									<?php
									$stmt = $conn->prepare("SELECT CONCAT(`firstname` ,' ',`lastname`)as name from users where id = ? ");
									$stmt->bind_param('s', $row['user_id']);
									$stmt->execute();
									$result = $stmt->get_result();
									$value = $result->fetch_object();

									?>
									<td class="text-center">
										<?= 'Cashier name :' . $value->name; ?>
									</td>

									<td class="text-center">
										<?= htmlspecialchars($row['delete_reason']) ?>
									</td>

								</tr>
							<?php endwhile; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- delete modal -->

<div class="modal fade" tabindex="-1" id="deleteModal" data-backdrop="static" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Delete Order</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id='delete_frm'>
				<div class="modal-body">
					<input type="hidden" name="id" id='order_id'>
					<div class="form-group">
						<label for="exampleInputEmail1">Username:</label>
						<input type="text" class="form-control" name="username" id='username'
							aria-describedby="emailHelp" placeholder="Enter Username">

					</div>
					<div class="form-group">
						<label for="exampleInputPassword1">Password:</label>
						<input type="password" class="form-control" name="password" id='password'
							placeholder="Password">
					</div>
					<div class="form-group">
						<label for="message-text" class="col-form-label">Reason:</label>
						<textarea class="form-control" name='delete_reason' id="message-text"></textarea>
					</div>

				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Save changes</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</form>
		</div>
		</form>
	</div>
</div>
</div>
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
				{ orderable: false, targets: [5] }
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