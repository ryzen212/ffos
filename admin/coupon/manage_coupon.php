<?php
require_once('./../../config.php');
if (isset($_GET['id']) && $_GET['id'] > 0) {
	$qry = $conn->query("SELECT * from `coupon_list` where id = '{$_GET['id']}' and delete_flag != 1");
	if ($qry->num_rows > 0) {
		foreach ($qry->fetch_assoc() as $k => $v) {
			$$k = $v;
		}
	}
}

?>
<div class="container-fluid">
	<form action="" id="menu-coupon">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">

		<div class="form-group">
			<label for="code" class="control-label">Code</label>
			<input type="text" name="coupon_code" id="coupon_code" class="form-control form-control-sm rounded-0"
				value="<?php echo isset($coupon_code) ? $coupon_code : ''; ?>" required />
		</div>
		<div class="form-group">
			<label for="name" class="control-label">Name</label>
			<input type="text" name="name" id="name" class="form-control form-control-sm rounded-0"
				value="<?php echo isset($name) ? $name : ''; ?>" required />
		</div>
		<div class=" form-group">
			<label for="status" class="control-label">Amount </label>
			<div class="input-group mb-3">
				<input type="number" min='1' name="amount" id="amount" class="form-control form-control-sm rounded-0"
					value="<?php echo isset($amount) ? $amount : ''; ?>" required />
				<div class="input-group-prepend">
					<select name="amount_type" id="amount_type" class="form-control form-control-sm rounded-0"
						required="required">
						<option value="1" <?= isset($amount_type) && $amount_type == 1 ? 'selected' : '' ?>>% off</option>
						<option value="2" <?= isset($amount_type) && $amount_type == 2 ? 'selected' : '' ?>>₱ off</option>
					</select>
				</div>
			</div>
		</div>
		<div class=" form-group">
			<label for="name" class="control-label">Quantity</label>
			<input type="number" min='1' name="qty" id="qty" class="form-control form-control-sm rounded-0"
				value="<?php echo isset($qty) ? $qty : ''; ?>" required />
		</div>
		<div class=" form-group">
			<label for="name" class="control-label">Valid Until</label>
			<input type="date" name="expiration" id="expiration" class="form-control form-control-sm rounded-0"
				value="<?php echo isset($expiration) ? $expiration : ''; ?>" required />
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


		$('#menu-coupon').submit(function (e) {



			e.preventDefault();



			var formData = new FormData($(this)[0]);


			// alert(schedule);

			var _this = $(this)
			$('.err-msg').remove();
			start_loader();
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=save_coupon",
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
						uni_modal("<i class='fa fa-th-list'></i> Menu Details ", "coupon/view_coupon.php?id=" + resp.iid)
						$('#uni_modal').on('hide.bs.modal', function () {
							location.reload()
						})
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

</script>