<?php if ($_settings->chk_flashdata('success')): ?>
	<script>
		alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
	</script>
<?php endif; ?>
<style>
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

	.nav-pills .nav-link:not(.active):hover {
		color: #ffc107;
	}

	.nav-link {
		color: black
	}
</style>
<div class="content bg-gradient-warning py-1 px-2">
	<h3 class="font-weight-bolder text-light">Coupon / Discount</h3>
</div>


<div class="row mt-1 justify-content-center">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

		<div class="card rounded-0">
			<div class="card-body">
				<ul class="nav nav-tabs nav-pills nav-fill" id="myTab" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" id="que_tab" data-toggle="tab" href="#coupon_cont" role="tab"
							aria-controls="coupon_cont" aria-selected="true">
							<h5>Coupon </h5>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="discount_tab" data-toggle="tab" href="#dicount_cont" role="tab"
							aria-controls="dicount_cont" aria-selected="false">
							<h5>Discounted Item</h5>
						</a>
					</li>

				</ul>
				<div class="tab-content" id="myTabContent" style="height:48em;overflow:auto;">
					<div class="tab-pane fade show active" id="coupon_cont" role="tabpanel" aria-labelledby="que_tab">
						<div id="quepon-field">

							<div class="card card-outline rounded-0 card-warning">
								<div class="card-header">
									<h3 class="card-title">List of Coupons</h3>
									<div class="card-tools">
										<a href="javascript:void(0)" id="create_new"
											class="btn btn-flat btn-warning text-light"><span
												class="fas fa-plus"></span> Create
											New</a>
									</div>
								</div>
								<div class="card-body">
									<div class="container-fluid">
										<table class="table table-hover table-striped table-bordered" id="list">
											<colgroup>
												<col width="5%">
												<col width="15%">
												<col width="20%">
												<col width="30%">
												<col width="10%">
												<col width="10%">
												<col width="10%">
											</colgroup>
											<thead>
												<tr>
													<th>#</th>
													<th>Name</th>
													<th>Code</th>
													<th>Amount</th>
													<th>Qty</th>
													<th>Valid Until</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
												<?php
												$i = 1;
												$qry = $conn->query("SELECT * from `coupon_list` where delete_flag != 1 order by `name`  asc ");
												while ($row = $qry->fetch_assoc()):
													?>
													<tr>

														<td>
															<?php echo $i++; ?>
														</td>
														<td class="">
															<?= $row['name'] ?>
														</td>
														<td class="">
															<?= $row['coupon_code'] ?>
														</td>
														<td class="">
															<?= isset($row['amount_type']) && $row['amount_type'] == 1 ? $row['amount'] . '% off' : '₱' . $row['amount'] . ' off' ?>
														</td>
														<td class="">
															<?= $row['qty'] ?>
														</td>
														<td>
															<?php echo date("Y-m-d", strtotime($row['expiration'])) ?>
														</td>





														<td align="center">
															<button type="button"
																class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon"
																data-toggle="dropdown">
																Action
																<span class="sr-only">Toggle Dropdown</span>
															</button>
															<div class="dropdown-menu" role="menu">
																<a class="dropdown-item view-data" href="javascript:void(0)"
																	data-id="<?php echo $row['id'] ?>"><span
																		class="fa fa-eye text-dark"></span>
																	View</a>
																<div class="dropdown-divider"></div>
																<a class="dropdown-item edit-data" href="javascript:void(0)"
																	data-id="<?php echo $row['id'] ?>"><span
																		class="fa fa-edit text-primary"></span>
																	Edit</a>
																<div class="dropdown-divider"></div>
																<a class="dropdown-item delete_data"
																	href="javascript:void(0)"
																	data-id="<?php echo $row['id'] ?>"><span
																		class="fa fa-trash text-danger"></span>
																	Delete</a>
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
					</div>
					<div class="tab-pane fade" id="dicount_cont" role="tabpanel" aria-labelledby="discount-tab">



						<div class="card card-outline rounded-0 card-warning">
							<div class="card-header">
								<h3 class="card-title">List of Dicounted Item</h3>
								<div class="card-tools">
									<a href="javascript:void(0)" id="create_discount"
										class="btn btn-flat btn-warning text-light"><span class="fas fa-plus"></span>
										Create
										New</a>
								</div>
							</div>
							<div class="card-body">
								<div class="container-fluid">
									<table class="table table-hover table-striped table-bordered" id="discount_list">
										<colgroup>
											<col width="5%">
											<col width="15%">
											<col width="20%">
											<col width="30%">
											<col width="10%">
											<col width="10%">
											<col width="10%">
										</colgroup>
										<thead>
											<tr>
												<th>#</th>
												<th>Item Name</th>
												<th>Code</th>
												<th>Variant</th>
												<th>Amount</th>
												<th>Qty</th>
												<th>Valid Until</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$i = 1;
											$qry = $conn->query("SELECT *
											FROM menu_list as ml LEFT JOIN discount_item as di ON ml.id = di.item_id where di.item_id is not null and di.delete_flag != 1 order by ml.name  asc ");
											while ($row = $qry->fetch_assoc()):
												?>
												<tr id='<?= $row['id'] ?>'>

													<td>
														<?php echo $i++; ?>
													</td>
													<td class="">
														<?= $row['name'] ?>
													</td>
													<td class="">
														<?= $row['code'] ?>
													</td>
													<td class="">
														<?= $row['variant'] ?>
													</td>
													<td class="">
														<?= isset($row['type']) && $row['type'] == 1 ? $row['amount'] . '% off' : '₱' . $row['amount'] . ' off' ?>
													</td>
													<td class="">
														<?= $row['qty'] ?>
													</td>
													<td>
														<?php echo date("Y-m-d", strtotime($row['exp_date'])) ?>
													</td>





													<td align="center">
														<button type="button"
															class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon"
															data-toggle="dropdown">
															Action
															<span class="sr-only">Toggle Dropdown</span>
														</button>
														<div class="dropdown-menu" role="menu">
															<a class="dropdown-item view-dis" href="javascript:void(0)"
																data-id="<?php echo $row['id'] ?>"><span
																	class="fa fa-eye text-dark"></span>
																View</a>
															<div class="dropdown-divider"></div>
															<a class="dropdown-item edit-dis" href="javascript:void(0)"
																data-id="<?php echo $row['id'] ?>"><span
																	class="fa fa-edit text-primary"></span>
																Edit</a>
															<div class="dropdown-divider"></div>
															<a class="dropdown-item delete_dis" href="javascript:void(0)"
																data-id="<?php echo $row['id'] ?>"><span
																	class="fa fa-trash text-danger"></span>
																Delete</a>
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




				</div>

			</div>
		</div>
	</div>
</div>


<script>
	$(document).ready(function () {
		$('.delete_data').click(function () {
			_conf("Are you sure to delete this Coupon permanently?", "delete_coupon", [$(this).attr('data-id')])
		})
		$('#create_new').click(function () {
			uni_modal("<i class='far fa-plus-square'></i> Add New Coupon ", "coupon/manage_coupon.php")
		})
		$('#create_discount').click(function () {
			uni_modal("<i class='far fa-plus-square'></i> Add Disconut Coupon ", "coupon/manage_discount.php")
		})
		$('.edit-data').click(function () {
			uni_modal("<i class='fa fa-edit'></i> Add New Coupon ", "coupon/manage_coupon.php?id=" + $(this).attr('data-id'))
		})
		$('.view-data').click(function () {
			uni_modal("<i class='fa fa-th-list'></i> Coupon Details ", "coupon/view_coupon.php?id=" + $(this).attr('data-id'))
		})

		$(document).on("click", ".edit-dis", function () {
			uni_modal("<i class='fa fa-edit'></i> Edit Discounted Item ", "coupon/manage_discount.php?id=" + $(this).attr('data-id'))
		})
		$(document).on("click", ".view-dis", function () {

			uni_modal("<i class='fa fa-th-list'></i> Discounted Item Details ", "coupon/view_discount.php?id=" + $(this).attr('data-id'))
		})
		$(document).on("click", ".delete_dis", function () {

			_conf("Are you sure to delete this Discounted Item permanently?", "delete_dis", [$(this).attr('data-id')])
		})
		$('.table').dataTable({
			columnDefs: [
				{ orderable: false, targets: [6] },
				{ targets: [0], className: 'text-center' }
			],

			order: [0, 'asc']
		});
		$('.dataTable td,.dataTable th').addClass('py-1 px-2 align-middle')
	})
	function delete_coupon($id) {
		start_loader();
		$.ajax({
			url: _base_url_ + "classes/Master.php?f=delete_coupon",
			method: "POST",
			data: { id: $id },
			dataType: "json",
			error: err => {
				console.log(err)
				alert_toast("An error occured.", 'error');
				end_loader();
			},
			success: function (resp) {
				if (typeof resp == 'object' && resp.status == 'success') {
					location.reload();
				} else {
					alert_toast("An error occured.", 'error');
					end_loader();
				}
			}
		})
	}
	function delete_dis($id) {
		start_loader();
		$.ajax({
			url: _base_url_ + "classes/Master.php?f=delete_dis",
			method: "POST",
			data: { id: $id },
			dataType: "json",
			error: err => {
				console.log(err)
				alert_toast("An error occured.", 'error');
				end_loader();
			},
			success: function (resp) {
				if (typeof resp == 'object' && resp.status == 'success') {

					var rowToDelete = $('tr[id="' + $id + '"]');


					var table = $('#discount_list').DataTable();
					// Find and remove the row by ID
					table.row('#' + $id).remove().draw();
					alert_toast("Item Deleted", 'success');
					end_loader();
					$('.modal').modal('hide')
				} else {
					alert_toast("An error occured.", 'error');
					end_loader();
				}
			}
		})
	}
</script>