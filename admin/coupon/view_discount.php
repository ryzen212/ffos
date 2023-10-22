<?php
require_once('./../../config.php');
if (isset($_GET['id']) && $_GET['id'] > 0) {
	$qry = $conn->query("SELECT * FROM menu_list as ml LEFT JOIN discount_item as di ON ml.id = di.item_id where di.item_id is not null and di.id = '{$_GET['id']}'  and di.delete_flag != 1 order by ml.name  asc  ");
	if ($qry->num_rows > 0) {
		foreach ($qry->fetch_assoc() as $k => $v) {
			$$k = $v;
		}
	} else {
		echo '<script>alert("Item not Found."); location.replace("./?page=coupon")</script>';
	}
} 
?>
<style>
	#uni_modal .modal-footer {
		display: none;
	}
</style>
<div class="container-fluid">
	<dl>
		
	
		<dt class="text-muted">Name</dt>
		<dd class="pl-4">
			<?= isset($name) ? $name : "" ?>
		</dd>

		<dt class="text-muted">Variant</dt>
		<dd class="pl-4">
			<?= isset($variant) ? $variant : "" ?>
		</dd>
		<dt class="text-muted">Quantity</dt>
		<dd class="pl-4">
			<?= isset($qty) ? $qty : "" ?>
		</dd>
		<dt class="text-muted">Amount</dt>
		<dd class="pl-4">
		<?= isset($type) && $type == 1 ? $amount.'% off' : '₱'.$amount.' off'   ?>
		</dd>
		
		<dt class="text-muted">Valid Until</dt>
		<dd class="pl-4">	<?= isset($exp_date) ? $exp_date : "" ?> </dd>
	</dl>
</div>
<hr class="mx-n3">
<div class="text-right pt-1">
	<button class="btn btn-sm btn-flat btn-light bg-gradient-light border" type="button" data-dismiss="modal"><i
			class="fa fa-times"></i> Close</button>
</div>

