<?php
require_once('./../../config.php');
if (isset($_GET['id']) && $_GET['id'] > 0) {
	$qry = $conn->query("SELECT * from `menu_list` where id = '{$_GET['id']}' and `delete_flag` = 0 ");
	if ($qry->num_rows > 0) {
		foreach ($qry->fetch_assoc() as $k => $v) {
			$$k = $v;
		}
	}
}

?>
<div class="container-fluid">
	<form action="" id="menu-form">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<div class="form-group">
			<label for="category_id" class="control-label">Category</label>
			<select name="category_id" id="category_id" class="form-control form-control-sm rounded-0"
				required="required">
				<option value="" <?= isset($category_id) ? 'selected' : '' ?>></option>
				<?php
				$categories = $conn->query("SELECT * FROM `category_list` where delete_flag = 0 and `status` = 1 ");
				while ($row = $categories->fetch_assoc()):
					?>
					<option value="<?= $row['id'] ?>" <?= isset($category_id) && $category_id == $row['id'] ? 'selected' : '' ?>>
						<?= $row['name'] ?>
					</option>
				<?php endwhile; ?>
			</select>
		</div>
		<div class="form-group">
			<label for="code" class="control-label">Code</label>
			<input type="text" name="code" id="code" class="form-control form-control-sm rounded-0"
				value="<?php echo isset($code) ? $code : ''; ?>" required />
		</div>
		<div class="form-group">
			<label for="name" class="control-label">Name</label>
			<input type="text" name="name" id="name" class="form-control form-control-sm rounded-0"
				value="<?php echo isset($name) ? $name : ''; ?>" "  required/>
		</div>
		
		<div class=" m-1">
			<button id="add" class="btn btn-outline-dark" type="button"><i class="bi bi-plus-circle"></i>
				Add Variant</button>
		</div>

		<table class="table table-bordered" id="crud_table">
			<tr>
				<th>Variant</th>
				<th>Price</th>
				<th>Vat</th>
				<th>Remove</th>
			</tr>

			<!-- echo $var_price;
exit; -->
			<?php


			if (isset($var_price)) {


				$var_price = json_decode(stripslashes($var_price), true);

				$i = 1;
				foreach ($var_price as $values) {
					?>


					<tr id='<?= $i ?>'>
						<td>
							<div class="form-group">
								<input type="text" id="var_name" class="var_name form-control form-control-sm rounded-0"
									value="<?php echo isset($values['var_name']) ? $values['var_name'] : ''; ?>" required />
							</div>
						</td>
						<td>
							<div class=" form-group">
								<input type="number" step="any" id="price<?= $i ?>"
									class="price form-control form-control-sm rounded-0 " onkeyup="vat_calc(<?= $i ?>)" min="1"
									value="<?php echo isset($values['price']) ? $values['price'] : ''; ?>" required />
							</div>
						</td>
						<td>
							<div class="form-group">
								<input type="number" step="any" id="price_vat<?= $i ?>"
									style=" background-color:transparent;border: 1;font-size: 1em;" readonly
									class="form-control form-control-sm rounded-0 " min='0' data-row='<?= $i ?>'
									value="<?php echo isset($values['price']) ? round($values['price'] * .12, 2) : ''; ?>"
									required />
							</div>
						</td>
						<?php if ($i == 1) { ?>
							<td></td>
						<?php } else { ?>
							<td><button type='button' name='remove' data-row='<?= $i ?>'
									class='btn btn-outline-danger btn-sm remove'>Remove</button></td>
						<?php } ?>
					</tr>
					<!-- end foreach loop -->
					<?php $i++;
				}
			} else { ?>

				<tr>
					<td>
						<div class="form-group">
							<input type="text" id="var_name" value='Regular'
								class="var_name form-control form-control-sm rounded-0" required />
						</div>
					</td>
					<td>
						<div class=" form-group">
							<input type="number" step="any" id="price1"
								class="price form-control form-control-sm rounded-0 " onkeyup="vat_calc(1)" required />
						</div>
					</td>
					<td>
						<div class="form-group">
							<input type="number" step="any" id="price_vat1"
								style=" background-color:transparent;border: 1;font-size: 1em;" readonly
								class="form-control form-control-sm rounded-0 " min='0' data-row='1' required />
						</div>
					</td>
					<td></td>
				</tr>
			<?php } ?>

		</table>




		<div class="form-group">
			<label for="description" class="control-label">Description</label>
			<textarea rows="3" name="description" id="description" class="form-control form-control-sm rounded-0"
				required><?php echo isset($description) ? $description : ''; ?></textarea>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Image</label>
			<div class="custom-file">
				<input type="file" class="custom-file-input rounded-circle" id="customFile2" name="menu_image"
					onchange="displayImg2(this,$(this))">
				<label class="custom-file-label" for="customFile2">Choose file</label>
			</div>
		</div>
		<div class="form-group d-flex justify-content-center">
			<img src="<?php echo isset($menu_img) ? validate_image($menu_img) : validate_image('uploads/ins_img.jpg'); ?>"
				alt="" id="cimg2" class="img-fluid img-thumbnail bg-gradient-dark border-dark">
		</div>
		<div class="form-group">
			<label for="status" class="control-label">Status</label>
			<select name="status" id="status" class="form-control form-control-sm rounded-0" required="required">
				<option value="1" <?= isset($status) && $status == 1 ? 'selected' : '' ?>>Available</option>
				<option value="0" <?= isset($status) && $status == 0 ? 'selected' : '' ?>>Unavailable</option>
			</select>
		</div>
	</form>
</div>
<script>
	function displayImg2(input, _this) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) {
				_this.siblings('.custom-file-label').html(input.files[0].name)
				$('#cimg2').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}

	function vat_calc(i) {
		var price = document.getElementById("price" + i).value;
		var vat_price = document.getElementById("price_vat" + i);

		vat_price.value = ((Number(price) / 1.12) * .12).toFixed(2);;
		// vat_price.value =  	(Number(price) + (Number(price)*.12)).toFixed(2);;
		// vat_price.toFixed(2)
	}
	$(document).ready(function () {
		var table = document.getElementById('crud_table');
		var rows = table.rows;
		var count = rows.length;
		$('#add').click(function () {

			var html_code = "<tr id='" + count + "'>";

			html_code += "<td><div class='form-group'><input type='text'  id='var_name' class='var_name form-control form-control-sm rounded-0' value='' required/></div></td>";
			html_code += "<td><div class=' form-group'><input type='number' step='any'  id='price" + count + "' class='price form-control form-control-sm rounded-0 ' onkeyup='vat_calc(" + count + ")' value='<?php echo isset($price) ? $price : ''; ?>' required /></div></td>";
			html_code += "<td><input type='number' min='1' step='any' id='price_vat" + count + "' style=' background-color:transparent;border: 1;font-size: 1em;' readonly class='form-control form-control-sm rounded-0 ' min='0' data-row='1' value='<?php echo isset($price) ? round($price * .12, 2) : ''; ?>' required /></td>";
			html_code += "<td><button type='button' name='remove' data-row='" + count + "' class='btn btn-outline-danger btn-sm remove'>Remove</button></td>";
			html_code += "</tr>";
			count = count + 1;
			$('#crud_table').append(html_code);
		});
		$(document).on('click', '.remove', function () {
			$(this).closest('tr').remove();

		});

		$('#uni_modal .modal-dialog').addClass(' modal-xl')
		$('#uni_modal').on('shown.bs.modal', function () {
			$('#category_id').select2({
				placeholder: "Select Category Here",
				width: '100%',
				containerCssClass: 'form-control form-control-sm rounded-0',
				dropdownParent: $('#uni_modal')
			})
		})
		$('#menu-form').submit(function (e) {



			e.preventDefault();
			var content = [];
			var row = [];

			var table = document.getElementById('crud_table');
			var rows = table.rows;

			var datas = [];
			for (var i = 0; i < rows.length; i++) {
				var rowTr = rows[i];

				rows[i].setAttribute('id', i);
				if (i == 0) continue;

				var temp = {};
				temp['var_name'] = "";
				temp['price'] = "";

				var var_name = rowTr.getElementsByClassName('var_name')[0].value;
				if (var_name) { // exisiting element
					temp['var_name'] = var_name;
				}
				var price = rowTr.getElementsByClassName('price')[0].value;
				if (price) {
					temp['price'] = price;
				}

				temp['row_id'] = i;
				//additional data
				datas.push(temp);
				// console.log(rowTr);
			}

			const variants = JSON.stringify(datas);
			var formData = new FormData($(this)[0]);


			// alert(schedule);
			formData.append("var_price", variants);
			var _this = $(this)
			$('.err-msg').remove();
			start_loader();
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=save_menu",
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
						uni_modal("<i class='fa fa-th-list'></i> Menu Details ", "menus/view_menu.php?id=" + resp.iid)
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