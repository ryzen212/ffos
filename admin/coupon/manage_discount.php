<?php
require_once('./../../config.php');
if (isset($_GET['id']) && $_GET['id'] > 0) {
	$qry = $conn->query("SELECT * FROM menu_list as ml LEFT JOIN discount_item as di ON ml.id = di.item_id where di.item_id is not null and di.id = '{$_GET['id']}'  and di.delete_flag != 1 order by ml.name  asc  ");
	if ($qry->num_rows > 0) {
		foreach ($qry->fetch_assoc() as $k => $v) {
			$$k = $v;
		}
	}
}

?>
<style>
	th.dt-center,
	td.dt-center {
		text-align: center;
	}
</style>
<div class="container-fluid">
	<form action="" id="discount_frm">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<div class=" form-group">
			<label for="name" class="control-label">Name</label>
			<input type="hidden" name="code" id='item_code' value="<?php echo isset($code) ? $code : '' ?>">
			<input type="hidden" name="name" id='item_name' value="<?php echo isset($name) ? $name : '' ?>">
			<?php if (isset($id)) { ?>
				<input type="hidden" name="item_id" value="<?php echo isset($item_id) ? $item_id : '' ?>">

				<input type="text" class="form-control form-control-sm rounded-0"
					value="<?php echo isset($name) ? $name : ''; ?> " readonly
					style=" background-color:transparent;border: 1;font-size: 1em;" />

			<?php } else { ?>
				<select id="select-menu" class="demo-consoles" name='item_id' placeholder="Select Menu Item..."></select>

			<?php } ?>
		</div>
		<div class=" form-group">
			<label for="status" class="control-label">Amount </label>
			<div class="input-group mb-3">
				<input type="number" min='1' name="amount" id="amount" class="form-control form-control-sm rounded-0"
					value="<?php echo isset($amount) ? $amount : ''; ?>" required />
				<div class="input-group-prepend">
					<select name="type" id="amount_type" class="form-control form-control-sm rounded-0"
						required="required">
						<option value="1" <?= isset($type) && $type == 1 ? 'selected' : '' ?>>% off</option>
						<option value="2" <?= isset($type) && $type == 2 ? 'selected' : '' ?>>₱ off</option>
					</select>
				</div>
			</div>
		</div>
		<div class="form-group">

			<?php if (isset($id)) { ?>
				<label for="exampleFormControlSelect1">Variant</label>
				<input type="text" name='variant' class="form-control form-control-sm rounded-0"
					value="<?php echo isset($variant) ? $variant : ''; ?> " readonly
					style=" background-color:transparent;border: 1;font-size: 1em;" />

			<?php } else { ?>
				<label for="exampleFormControlSelect1">Variants</label>
				<select class="form-control" id="item_variants" name='variant'>

				<?php } ?>

			</select>
		</div>
		<div class=" form-group">
			<label for="name" class="control-label">Quantity</label>
			<input type="number" min='1' name="qty" id="qty" class="form-control form-control-sm rounded-0"
				value="<?php echo isset($qty) ? $qty : ''; ?>" required />
		</div>
		<div class=" form-group">
			<label for="name" class="control-label">Valid Until</label>
			<input type="date" name="exp_date" id="expiration" class="form-control form-control-sm rounded-0"
				value="<?php echo isset($exp_date) ? $exp_date : ''; ?>" required />
		</div>

	</form>
</div>
<script>




	$(document).ready(function () {




		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth() + 1; //January is 0!
		var yyyy = today.getFullYear();
		if (dd < 10) {
			dd = '0' + dd
		}
		if (mm < 10) {
			mm = '0' + mm
		}

		today = yyyy + '-' + mm + '-' + dd;
		document.getElementById("expiration").setAttribute("min", today);

		var count = 0;
		$('#discount_frm').submit(function (e) {
			e.preventDefault();

			var formData = new FormData($(this)[0]);



			var _this = $(this)
			$('.err-msg').remove();
			start_loader();
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=save_discount",
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				method: 'POST',
				type: 'POST',
				dataType: 'json',
				error: err => {
					console.log(err)
					alert_toast("An error occured", 'error');
					end_loader();
				},
				success: function (resp) {
					if (typeof resp == 'object' && resp.status == 'success') {
						// location.reload()
						alert_toast(resp.msg, 'success')
						uni_modal("<i class='fa fa-th-list'></i> Discount Details ", "coupon/view_discount.php?id=" + resp.iid)
						$('#uni_modal').on('hide.bs.modal', function () {

						})

						var table = $('#discount_list').DataTable();
						// const table = new DataTable('#discount_list');
						var row_data = resp.data;
						var last_row = table.row(':last').data()
						var action = "<div  class='text-center'><button type='button'"
						action += "	class='btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon'"
						action += "data-toggle='dropdown'>"
						action += "	Action<span class='sr-only'>Toggle Dropdown</span>"
						action += "</button>"
						action += "<div class='dropdown-menu' role='menu'>"
						action += "<a class='dropdown-item view-dis' href='javascript:void(0)'"
						action += "data-id='" + resp.iid + "'><span"
						action += "	class='fa fa-eye text-dark'></span> View</a>"
						action += "<div class='dropdown-divider'></div>"
						action += "<a class='dropdown-item edit-dis' href='javascript:void(0)'"
						action += "data-id='" + resp.iid + "'><span class='fa fa-edit text-primary'></span> Edit</a>"
						action += "	<div class='dropdown-divider'></div>"
						action += "	<a class='dropdown-item delete_dis'"
						action += "	data-id='" + resp.iid + "'><span class='fa fa-trash text-danger'></span> Delete</a>"
						action += "</div></div>"

						if (last_row === undefined) {
							count = 1;
						} else {
							count = Number(last_row[0]) + 1
						}

						if (resp.action == 'insert') {

							table.row.add([count, row_data.name, row_data.code, row_data.variant, row_data.type == 1 ? row_data.amount + '% off' : '₱' + row_data.amount + ' off', row_data.qty, row_data.exp_date, action]).node().id = resp.iid;
							table.draw();

						} else if (resp.action == 'update') {

							var x = document.getElementById(row_data.id);


							$('#' + resp.iid).html('<td class="text-center" >' + x.rowIndex + '</td><td>' + row_data.name + '</td><td>' + row_data.code + '</td><td>' + row_data.variant + '</td><td>' + (row_data.type == 1 ? row_data.amount + '% off' : '₱' + row_data.amount + ' off') + '</td><td>' + row_data.qty + '</td><td>' + row_data.exp_date + '</td><td>' + action + '</td>')

						}
						$('.dataTable td,.dataTable th').addClass('py-1 px-2 align-middle')

					} else if (resp.status == 'failed' && !!resp.msg) {
						var el = $('<div>')
						el.addClass("alert alert-danger err-msg").text(resp.msg)
						_this.prepend(el)
						el.show('slow')
						$('.modal').scrollTop(0);
						end_loader()
					} else {
						alert_toast("An error occured", 'error');
						end_loader();
						console.log(resp)
					}
				}
			})
		})

	})

	$('#select-menu').selectize({
		options: [
			<?php $menu = $conn->query('SELECT ml.name , ml.var_price,ml.code,cl.name as cat_name ,ml.id as item_id FROM menu_list as ml LEFT JOIN category_list as cl ON ml.category_id = cl.id  order by ml.name  asc');
			while ($row = $menu->fetch_assoc()) { ?>

																																																					{ category: '<?= $row['cat_name'] ?>', value: "<?= $row['item_id'] ?>", name: "<?= $row['name'] . ' - ' . $row['code'] ?>" },

			<?php }
			?>



		],


		optionGroupRegister: function (optgroup) {
			var capitalised = optgroup.charAt(0).toUpperCase() + optgroup.substring(1);
			var group = {
				label: 'Category: ' + capitalised
			};

			group[this.settings.optgroupValueField] = optgroup;

			return group;
		},
		optgroupField: 'category',
		labelField: 'name',
		searchField: ['name'],
		sortField: 'name'
	});


	$("#select-menu").change(function () {
		var item_id = $("#select-menu").val();

		$.ajax({
			url: _base_url_ + "classes/Master.php?f=get_price",
			data: { item_id: item_id },
			method: 'POST',
			type: 'POST',
			dataType: 'json',
			error: err => {
				console.log(err)
				alert_toast("An error occured", 'error');

			},
			success: function (resp) {
				if (typeof resp == 'object' && resp.status == 'success') {
					// location.reload()
					var var_price = resp.data;
					$('#item_variants').html('');
					var html_code = '';
					$('#item_code').val(resp.item_code)
					$('#item_name').val(resp.item_name)
					if (var_price.length > 1) {
						html_code += "<option>All</option>"
					}

					var_price.forEach(function (item, index) {

						console.log(item.var_name);
						html_code += "<option value='" + item.var_name + "'> " + item.var_name + "</option>";

					});

					$('#item_variants').append(html_code);

				} else {
					alert_toast("An error occured", 'error');

					console.log(resp)
				}
			}
		})




	})


</script>