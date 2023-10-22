<?php
require_once('./../../config.php');
if (isset($_GET['id']) && $_GET['id'] > 0) {
	$qry = $conn->query("SELECT * from `coupon_list` where id = '{$_GET['id']}' ");
	if ($qry->num_rows > 0) {
		foreach ($qry->fetch_assoc() as $k => $v) {
			$$k = $v;
		}
	} else {
		echo '<script>alert("Coupon ID is not valid."); location.replace("./?page=coupon")</script>';
	}
} else {
	echo '<script>alert("Coupon ID is Required."); location.replace("./?page=coupon")</script>';
}
?>
<style>
	#uni_modal .modal-footer {
		display: none;
	}
</style>
<div class="container-fluid">
	<dl>
		
		<dt class="text-muted">Coupon Code</dt>
		<dd class="pl-4">
			<?= isset($coupon_code) ? $coupon_code : "" ?>
		</dd>
		<dt class="text-muted">Name</dt>
		<dd class="pl-4">
			<?= isset($name) ? $name : "" ?>
		</dd>

		<dt class="text-muted">Quantity</dt>
		<dd class="pl-4">
			<?= isset($qty) ? $qty : "" ?>
		</dd>

		<dt class="text-muted">Amount</dt>
		<dd class="pl-4">
		<?= isset($amount_type) && $amount_type == 1 ? $amount.'% off' : '₱'.$amount.' off'   ?>
		</dd>
		
		<dt class="text-muted">Valid Until</dt>
		<dd class="pl-4">	<?= isset($expiration) ? $expiration : "" ?> </dd>
	</dl>
</div>
<hr class="mx-n3">
<div class="text-right pt-1">
	<button class="btn btn-sm btn-flat btn-light bg-gradient-light border" type="button" data-dismiss="modal"><i
			class="fa fa-times"></i> Close</button>
</div>

