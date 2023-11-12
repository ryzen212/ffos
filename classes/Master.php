<?php
require_once('../config.php');
require_once('../customer/phpqrcode/qrlib.php');
// session_start();
class Master extends DBConnection
{
	private $settings;

	public function __construct()
	{
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct()
	{
		parent::__destruct();
	}
	function capture_err()
	{
		if (!$this->conn->error)
			return false;
		else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}

	function save_category()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id'))) {
				if (!empty($data))
					$data .= ",";
				$v = htmlspecialchars($this->conn->real_escape_string($v));
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `category_list` where `name` = '{$name}' and delete_flag = 0 " . (!empty($id) ? " and id != {$id} " : "") . " ")->num_rows;
		if ($this->capture_err())
			return $this->capture_err();
		if ($check > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Category already exists.";
			return json_encode($resp);
			exit;
		}
		if (empty($id)) {
			$sql = "INSERT INTO `category_list` set {$data} ";
		} else {
			$sql = "UPDATE `category_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if ($save) {
			$cid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['cid'] = $cid;
			$resp['status'] = 'success';
			if (empty($id))
				$resp['msg'] = "New Category successfully saved.";
			else
				$resp['msg'] = " Category successfully updated.";
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
		// if($resp['status'] == 'success')
		// 	$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function delete_category()
	{
		extract($_POST);
		$del = $this->conn->query("UPDATE `category_list` set `delete_flag` = 1 where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', " Category successfully deleted.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_menu()
	{
		extract($_POST);

		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id'))) {
				if (!empty($data)) {
					$data .= ",";

				}
				if (empty($v) && $k != 'status') {
					$resp['status'] = 'failed';
					$resp['msg'] = "Incomplete Fields";
					return json_encode($resp);
					exit;
				}
				if ($k == 'var_price') {

					$v = json_decode(stripslashes($v), true);

					// check variants value;

					foreach ($v as $values) {
						if (empty($values['var_name']) || empty($values['price']) || empty($values['row_id'])) {
							$resp['status'] = 'failed';
							$resp['msg'] = "Incomplete Variants Fields";
							return json_encode($resp);
							exit;
						}
						if (!is_numeric($values['price']) || $values['price'] < 1) {
							$resp['status'] = 'failed';
							$resp['msg'] = "Invalid Price";
							return json_encode($resp);
							exit;
						}
						$var_name[] = strtolower($values['var_name']);
					}
					foreach ($v as $values) {
						if ((array_count_values($var_name))[strtolower($values['var_name'])] > 1) {

							$resp['status'] = 'failed';
							$resp['msg'] = "Duplicate Variant Names";
							return json_encode($resp);
							exit;
						}

					}

					$v = json_encode($v);
				}



			} else {
				$v = htmlspecialchars($this->conn->real_escape_string($v));
			}


			$data .= " `{$k}`='{$v}' ";


		}
		if (!empty($_FILES['menu_image']['tmp_name'])) {

			$ext = pathinfo($_FILES['menu_image']['name'], PATHINFO_EXTENSION);
			$fname = "uploads/menu/" . uniqid("img-", true) . ".png";
			$accept = array('image/jpeg', 'image/png', 'image/jpg');
			if (!in_array($_FILES['menu_image']['type'], $accept)) {
				$err = "Image file type is invalid";
			}
			if ($_FILES['menu_image']['type'] == 'image/jpeg')
				$uploadfile = imagecreatefromjpeg($_FILES['menu_image']['tmp_name']);
			elseif ($_FILES['menu_image']['type'] == 'image/png')
				$uploadfile = imagecreatefrompng($_FILES['menu_image']['tmp_name']);
			if (!$uploadfile) {
				$resp['status'] = 'failed';
				$resp['msg'] = "Image is invalid";
				return json_encode($resp);
				exit;
			}
			$temp = imagescale($uploadfile, 200, 200);
			if (is_file(base_app . $fname))
				unlink(base_app . $fname);


			$data .= " ,`menu_img`='{$fname}' ";


		} elseif (empty($_FILES['menu_image']['tmp_name']) && empty($id)) {

			$resp['status'] = 'failed';
			$resp['msg'] = "Please add Image";
			return json_encode($resp);
			exit;

		}
		// todo update  delete



		// imagedestroy($temp);



		$check = $this->conn->query("SELECT * FROM `menu_list` where `code` = '{$code}' and delete_flag = 0 " . (!empty($id) ? " and id != {$id} " : "") . " ")->num_rows;
		if ($this->capture_err())
			return $this->capture_err();
		if ($check > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Menu Code already exists.";
			return json_encode($resp);
			exit;
		}
		if (empty($id)) {
			$sql = "INSERT INTO `menu_list` set {$data} ";
		} else {
			$stmt = $this->conn->prepare("SELECT menu_img from menu_list where id = ?");

			$stmt->bind_param('s', $id);
			$stmt->execute();
			$result = $stmt->get_result();
			if ($result->num_rows > 0) {
				$result = $result->fetch_assoc();
				$this->old_menu_image = base_app . $result['menu_img'];
			}
			if (!empty($_FILES['menu_image']['tmp_name'])) {
				$this->delete_img();
			}
			$sql = "UPDATE `menu_list` set {$data} where id = '{$id}' ";

		}


		$save = $this->conn->query($sql);
		if ($save) {
			if (!empty($_FILES['menu_image']['tmp_name'])) {
				$upload = imagepng($temp, base_app . $fname);
			}

			$iid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['iid'] = $iid;
			$resp['status'] = 'success';
			if (empty($id))
				$resp['msg'] = "New Menu successfully saved.";
			else
				$resp['msg'] = " Menu successfully updated.";
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
		// if($resp['status'] == 'success')
		// 	$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function delete_menu()
	{
		extract($_POST);
		$del = $this->conn->query("UPDATE `menu_list` set `delete_flag` = 1 where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', " Menu successfully deleted.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function delete_coupon()
	{
		extract($_POST);
		$del = $this->conn->query("UPDATE `coupon_list` set `delete_flag` = 1 where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', " Menu successfully deleted.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function delete_dis()
	{
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `discount_item` where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', " Menu successfully deleted.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}

	// coupons
	function save_coupon()
	{
		extract($_POST);
		// $resp['status'] = 'failed';
		// $resp['msg'] = "Nagana NAman";
		// return json_encode($resp);
		// exit;
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id'))) {
				if (!empty($data)) {
					$data .= ",";

				}
				if (empty($v)) {
					$resp['status'] = 'failed';
					$resp['msg'] = "Incomplete Fields";
					return json_encode($resp);
					exit;
				}
				if ($k == 'amount' && !is_numeric($v) && $v < 1) {
					$resp['status'] = 'failed';
					$resp['msg'] = "Invalid Amount";
					return json_encode($resp);
					exit;
				}

				if ($k == 'qty' && !is_numeric($v) && $v < 1) {
					$resp['status'] = 'failed';
					$resp['msg'] = "Invalid Quantity";
					return json_encode($resp);
					exit;
				}



				$v = htmlspecialchars($this->conn->real_escape_string($v));



				$data .= " `{$k}`='{$v}' ";


			}
		}
		$check = $this->conn->query("SELECT * FROM `coupon_list` where `coupon_code` = '{$coupon_code}'  " . (!empty($id) ? " and id != {$id} " : "") . " ")->num_rows;
		if ($this->capture_err())
			return $this->capture_err();
		if ($check > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Coupon Code already exists.";
			return json_encode($resp);
			exit;
		}
		if (empty($id)) {
			$sql = "INSERT INTO `coupon_list` set {$data} ";
		} else {

			$sql = "UPDATE `coupon_list` set {$data} where id = '{$id}' ";

		}


		$save = $this->conn->query($sql);
		if ($save) {
			$iid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['iid'] = $iid;
			$resp['status'] = 'success';
			if (empty($id))
				$resp['msg'] = "New Coupon successfully saved.";
			else
				$resp['msg'] = " Coupon successfully updated.";
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
		if ($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}


	function save_discount()
	{
		extract($_POST);
		// $resp['action'] = 'failed';
		// $resp['msg'] = "Nagana NAman";
		// return json_encode($resp);
		// exit;
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id'))) {
				if (!empty($data)) {
					$data .= ",";

				}
				if ($k == 'code' || $k == 'name') {
					continue;
				}
				if (empty($v)) {
					$resp['status'] = 'failed';
					$resp['msg'] = "Incomplete Fields";
					return json_encode($resp);
					exit;
				}

				if ($k == 'amount' && !is_numeric($v) && $v < 1) {
					$resp['status'] = 'failed';
					$resp['msg'] = "Invalid Amount";
					return json_encode($resp);
					exit;
				}

				if ($k == 'qty' && !is_numeric($v) && $v < 1) {
					$resp['status'] = 'failed';
					$resp['msg'] = "Invalid Quantity";
					return json_encode($resp);
					exit;
				}

				if ($type == 1 && $amount > 100) {
					$resp['status'] = 'failed';
					$resp['msg'] = "Invalid amount more than 100%";
					return json_encode($resp);
					exit;
				}

				if ($type == 2) {
					$check_amount = $this->conn->query("SELECT var_price FROM `menu_list` where id = {$item_id}  ");
					while ($row = $check_amount->fetch_assoc()) {
						$variants = json_decode(stripslashes($row['var_price']), true);
						foreach ($variants as $values) {

							if (($values['var_name'] == $variant || $variant == 'All') && $amount > $values['price']) {

								$resp['status'] = 'failed';
								$resp['msg'] = "Invalid amount more than the price";
								return json_encode($resp);
								exit;
							}

						}
					}

				}


				$v = htmlspecialchars($this->conn->real_escape_string($v));

				$data .= " `{$k}`='{$v}' ";


			}
		}
		$check = $this->conn->query("SELECT * FROM `discount_item` where " . (!empty($id) ? " id != {$id} and " : "") . " `item_id` = '{$item_id}'  and variant = 'All' or '{$variant} ' ")->num_rows;
		if ($this->capture_err())
			return $this->capture_err();
		if ($check > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Item is already discounted.";
			$resp['que'] = "SELECT * FROM `discount_item` where `item_id` = '{$item_id}'  and (variant ='All' or variant = '{$variant} ' )' " . (!empty($id) ? "and id != {$id} " : "");
			return json_encode($resp);
			exit;
		}
		if (empty($id)) {
			$sql = "INSERT INTO `discount_item` set {$data} ";
			$resp['que'] = "SELECT * FROM `discount_item` where `item_id` = '{$item_id}'  and (variant ='All' or variant = '{$variant} ' )" . (!empty($id) ? "and id != {$id} " : "");
			$resp['action'] = 'insert';
		} else {

			$sql = "UPDATE `discount_item` set {$data} where id = '{$id}' ";
			$resp['action'] = 'update';
		}


		$save = $this->conn->query($sql);
		if ($save) {
			$iid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['iid'] = $iid;
			$resp['status'] = 'success';
			if (empty($id))
				$resp['msg'] = "New Discounted Item successfully saved.";
			else
				$resp['msg'] = " Discounted Item successfully updated.";
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
		if ($resp['status'] == 'success')
			$resp['data'] = $_POST;

		$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}
	function get_price()
	{
		extract($_POST);


		// $id = $_POST['item_id'];

		$check = $this->conn->query("SELECT var_price,code,name FROM `menu_list` where `id` = '{$item_id}'   ");
		if ($this->capture_err())
			return $this->capture_err();
		if ($check->num_rows < 1) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Item not found.";
			return json_encode($resp);
			exit;
		}
		// $resp['status'] = "SELECT var_price FROM `menu_list` where `id` = '{$item_id}'  " . (!empty($item_id) ? " and id != {$item_id} " : "") . " ";

		while ($row = $check->fetch_assoc()) {
			$resp['item_code'] = $row['code'];
			$resp['item_name'] = $row['name'];
			$resp['data'] = $jsonarray = json_decode(stripslashes($row['var_price']), true);
		}

		$resp['status'] = 'success';
		return json_encode($resp);
	}

	function add_coupon()
	{
		extract($_POST);
		// $resp['status'] = 'failed';
		// $resp['msg'] = "Nagana NAman";
		// return json_encode($resp);
		// exit;
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id'))) {
				if (!empty($data)) {
					$data .= ",";
				}
				if (empty($v)) {
					$resp['status'] = 'failed';
					$resp['msg'] = "Incomplete Fields";
					return json_encode($resp);
					exit;
				}
				$v = htmlspecialchars($this->conn->real_escape_string($v));
				$data .= " `{$k}`='{$v}' ";


			}
		}
		$check = $this->conn->query("SELECT * FROM `coupon_list` where `coupon_code` = '{$coupon_code}' ");
		if ($this->capture_err())
			return $this->capture_err();
		if ($check->num_rows < 1) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Coupon Code not Found.";
			return json_encode($resp);
			exit;
		}

		while ($row = $check->fetch_assoc()) {
			$expire = strtotime($row['expiration']);
			$today = strtotime("today midnight");

			if ($today > $expire) {
				$resp['status'] = 'failed';
				$resp['msg'] = "Coupon is already expire.";
				return json_encode($resp);
				exit;
			}

			if ($row['qty'] < 1) {
				$resp['status'] = 'failed';
				$resp['msg'] = "Coupon usage limit has been reached mean.";
				return json_encode($resp);
				exit;
			}

			$resp['data'] = array("coupon_code" => $row['coupon_code'], "name" => $row['name'], "amount" => $row['amount'], "amount_type" => $row['amount_type'], "id" => $row['id']);
		}

		$resp['status'] = 'success';
		$resp['msg'] = " Coupon has beed Add.";
		if ($resp['status'] == 'success') {
			$this->settings->set_flashdata('success', $resp['msg']);
		}
		return json_encode($resp);
		// $sql = "UPDATE `coupon_list` set {$data} where id = '{$id}' ";



		// $save = $this->conn->query($sql);
		// if ($save) {
		// 	$iid = !empty($id) ? $id : $this->conn->insert_id;
		// 	$resp['iid'] = $iid;
		// 	$resp['status'] = 'success';
		// 	if (empty($id))
		// 		$resp['msg'] = "New Coupon successfully saved.";
		// 	else
		// 		$resp['msg'] = " Coupon successfully updated.";
		// } else {
		// 	$resp['status'] = 'failed';
		// 	$resp['err'] = $this->conn->error . "[{$sql}]";
		// }
		// if ($resp['status'] == 'success')
		// 	$this->settings->set_flashdata('success', $resp['msg']);

	}
	// end coupons
	function place_order()
	{

		$prefix = date("Ymd");
		$code = sprintf("%'.05d", 1);
		while (true) {
			$check = $this->conn->query("SELECT * FROM `order_list` where code = '{$prefix}{$code}'  and delete_flag!=1 ")->num_rows;
			if ($check > 0) {
				$code = sprintf("%'.05d", abs($code) + 1);
			} else {
				$_POST['code'] = $prefix . $code;
				$_POST['queue'] = $code;
				break;
			}
		}

		$_POST['user_id'] = $this->settings->userdata('id');
		extract($_POST);

		$order_fields = ['code', 'queue', 'total_amount', 'customer_code', 'customer_queue', 'tendered_amount', 'user_id', 'menu_note', 'coupon_ids'];
		$data = "";
		foreach ($_POST as $k => $v) {
			if (in_array($k, $order_fields) && !is_array($_POST[$k])) {
				if ($k == 'coupon_ids') {
					$v = json_decode(stripslashes($v), true);
					$coupon_ids = $v;
					$v = json_encode($v);


				} else {
					$v = addslashes(htmlspecialchars($this->conn->real_escape_string($v)));
				}

				if (!empty($data))
					$data .= ", ";
				$data .= " `{$k}` = '{$v}' ";
			}
		}

		$sql = "INSERT INTO `order_list` set {$data}";
		$save = $this->conn->query($sql);

		if ($save) {
			$oid = $this->conn->insert_id;
			$resp['oid'] = $oid;
			$data = '';
			foreach ($menu_id as $k => $v) {
				if (!empty($data))
					$data .= ", ";
				$menu_arr = explode("-", $menu_id[$k]);
				if (!isset($menu_arr[1])) {
					$menu_arr[1] = '1';
				}
				$check = $this->conn->query("SELECT * FROM `discount_item` where item_id = '{$menu_arr[0]}'  and type = {$menu_arr[1]}   and exp_date > CURDATE() and qty > 0  ")->num_rows;
				if ($check > 0) {

					$discount_update = "UPDATE discount_item SET qty = qty-1 WHERE item_id = '{$menu_arr[0]}'  and type = {$menu_arr[1]} ";
					$discount_update = $this->conn->query($discount_update);
				}



				$data .= "('{$oid}', '{$menu_arr[0]}', '{$price[$k]}', '{$quantity[$k]}', '{$menu_arr[1]}')";

			}


			$sql2 = "INSERT INTO `order_items` (`order_id`, `menu_id`, `price`, `quantity`,`variants`) VALUES {$data}";
			$save2 = $this->conn->query($sql2);
			if ($save2) {
				if (!empty($coupon_ids)) {
					$coupon_update = "UPDATE coupon_list SET qty = qty-1 WHERE id IN (" . implode(',', $coupon_ids) . ")";
					$coupon_update = $this->conn->query($coupon_update);
				}


				if (!empty($coupon_ids)) {
					$coupon_update = "UPDATE coupon_list SET qty = qty-1 WHERE id IN (" . implode(',', $coupon_ids) . ")";
					$coupon_update = $this->conn->query($coupon_update);
				}


				$resp['status'] = 'success';
				$resp['msg'] = ' Order has been placed.';
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = "Order has failed to save due to some reason.";
				$resp['err'] = $this->conn->error;
				$resp['sql'] = $sql2;
				$this->conn->query("DELETE FROM `order_list` where id = '{$oid}'");
			}
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "Order has failed to save due to some reason.";
			$resp['err'] = $this->conn->error;
			$resp['sql'] = $sql;
		}
		return json_encode($resp);
	}
	function customer_order()
	{

		$_SESSION['is_order'] = true;

		$prefix = date("Ymd");
		$code = sprintf("%'.05d", 1);

		while (true) {
			$check = $this->conn->query("SELECT * FROM `customer_order` where (code = '{$prefix}{$code}' or queue = '{$code}') AND DATE(date_created) = DATE(NOW())   and delete_flag!=1 ")->num_rows;
			if ($check > 0) {
				$code = sprintf("%'.05d", abs($code) + 1);
			} else {
				$_POST['code'] = $prefix . $code;

				$_POST['queue'] = $code;
				break;
			}
		}

		$customer_code = isset($_SESSION['customer_code']) || !empty($_SESSION['customer_code']) ? $_SESSION['customer_code'] : $_POST['code'];
		$_SESSION['customer_code'] = $customer_code;
		$_POST['code'] = $customer_code;
		$path = 'qrImages/';
		$qrcode = $path . time() . ".png";
		$_POST['qr_img'] = $qrcode;

		$qrcontent = '{"queue": "' . $_POST['queue'] . '", "customer_code": "' . $customer_code . '"}';
		QRcode::png($qrcontent, '../' . $qrcode, 'H', 4, 4);
		// exit;
		extract($_POST);

		$order_fields = ['code', 'queue', 'total_amount', 'tendered_amount', 'qr_img', 'menu_note', 'coupon_ids'];
		$data = "";
		foreach ($_POST as $k => $v) {
			if (in_array($k, $order_fields) && !is_array($_POST[$k])) {
				if ($k == 'coupon_ids') {
					$v = json_decode(stripslashes($v), true);
					$coupon_ids = $v;
					$v = json_encode($v);


				} else {
					$v = addslashes(htmlspecialchars($this->conn->real_escape_string($v)));
				}

				if (!empty($data))
					$data .= ", ";
				$data .= " `{$k}` = '{$v}' ";
			}
		}
		// echo $data;
		// exit;
		$sql = "INSERT INTO `customer_order` set {$data}";
		$save = $this->conn->query($sql);

		if ($save) {
			$oid = $this->conn->insert_id;
			$resp['oid'] = $oid;
			$data = '';
			foreach ($menu_id as $k => $v) {
				if (!empty($data))
					$data .= ", ";
				$menu_arr = explode("-", $menu_id[$k]);
				if (!isset($menu_arr[1])) {
					$menu_arr[1] = '1';
				}
				$data .= "('{$oid}', '{$menu_arr[0]}', '{$price[$k]}', '{$quantity[$k]}', '{$menu_arr[1]}')";

			}

			$sql2 = "INSERT INTO `customer_items` (`order_id`, `menu_id`, `price`, `quantity`,`variants`) VALUES {$data}";
			$save2 = $this->conn->query($sql2);
			if ($save2) {


				$resp['status'] = 'success';
				$resp['msg'] = ' Order has been placed.';
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = "Order has failed to save due to some reason.";
				$resp['err'] = $this->conn->error;
				$resp['sql'] = $sql2;
				$this->conn->query("DELETE FROM `order_list` where id = '{$oid}'");
			}
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "Order has failed to save due to some reason.";
			$resp['err'] = $this->conn->error;
			$resp['sql'] = $sql;
		}
		return json_encode($resp);



	}

	// Get Customer Order
	function get_customer_qrcode()
	{
		$check = $this->conn->query("SELECT * FROM `order_list` where  `customer_queue` = '{$_POST['queue']}'   AND DATE(date_created) = DATE(NOW()) ")->num_rows;

		if ($check > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Order Already Exist.";
			return json_encode($resp);
			exit;
		}
		$data = array();

		$qry = $this->conn->query("SELECT code FROM `customer_order` WHERE queue = '{$_POST['queue']}'  AND DATE(date_created) = DATE(NOW()) ");

		if ($qry) {
			// Fetch one row as an associative array
			$row = $qry->fetch_assoc();

			if ($row) {
				// Access the 'code' value from the fetched row

				if (empty($_POST['code'])) {
					$_POST['code'] = $row['code'];
				}
			} else {
				// No matching row found

				$resp['status'] = 'failed';
				$resp['msg'] = ' No Order Found';
				$resp['data'] = $data;
				return json_encode($resp);
			}
		} else {
			// Query execution failed
			$resp['status'] = 'failed';
			$resp['msg'] = ' Database Error';
			$resp['data'] = $data;
			return json_encode($resp);

		}



		$qry = $this->conn->query("SELECT * FROM `customer_order` where code = '{$_POST['code']}' and queue = '{$_POST['queue']}'");
		if ($qry->num_rows > 0) {
			foreach ($qry->fetch_array() as $k => $v) {

				if (!is_numeric($k))
					$$k = htmlspecialchars_decode($v);

			}

		}

		// 		echo "SELECT * FROM `customer_order` where code = '{$_POST['code']}' and queue = '{$_POST['queue']}'";
// exit;
		$items = $this->conn->query("SELECT oi.*, m.name as `item`,m.code,m.var_price FROM `customer_items` oi inner join `menu_list` m on oi.menu_id = m.id where oi.order_id = '{$id}'");
		while ($row = $items->fetch_assoc()) {
			$row['isVariants'] = false;
			$variants = json_decode(stripslashes($row['var_price']), true);
			if (count($variants) > 1) {
				$row['isVariants'] = true;
			}
			foreach ($variants as $values) {
				if ($row['variants'] == $values['row_id']) {
					$row['var_name'] = $values['var_name'];
				}


			}
			array_push($data, $row);
			// echo $row['price'];

		}
		$resp['status'] = 'success';
		$resp['msg'] = ' Order has been placed.';
		$resp['data'] = $data;
		return json_encode($resp);


	}
	// end Get Customer Order

	function delete_order()
	{
		extract($_POST);
		$_POST['delete_reason'] = htmlspecialchars($this->conn->real_escape_string($_POST['delete_reason']));
		$_POST['password'] = htmlspecialchars($this->conn->real_escape_string($_POST['delete_reason']));
		$_POST['username'] = htmlspecialchars($this->conn->real_escape_string($_POST['delete_reason']));
		$_POST['id'] = htmlspecialchars($this->conn->real_escape_string($_POST['id']));
		if (empty($_POST['delete_reason']) || empty($_POST['id']) || empty($_POST['username']) || empty($_POST['password'])) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Incomplete Fields";
			return json_encode($resp);
			exit;
		}
		$stmt = $this->conn->prepare("SELECT * from users where username = ? and password = ? and type= '1' ");
		$password = md5($password);
		$stmt->bind_param('ss', $username, $password);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0) {

			$del = $this->conn->query("UPDATE `order_list` set delete_flag = 1 , delete_reason = '{$delete_reason}'   where id = '{$id}'");
			if ($del) {
				$stmt = $this->conn->prepare("SELECT coupon_ids from order_list where id = ? ");
				$stmt->bind_param('s', $id);
				$stmt->execute();
				$result = $stmt->get_result();
				$value = $result->fetch_object();
				$coupon_ids = $v = json_decode(stripslashes($value->coupon_ids), true);
				;

				if (!empty($coupon_ids)) {
					$coupon_update = "UPDATE coupon_list SET qty = qty+1 WHERE id IN (" . implode(',', $coupon_ids) . ")";
					$coupon_update = $this->conn->query($coupon_update);
				}

				$resp['status'] = 'success';
				$this->settings->set_flashdata('success', " Order has been deleted successfully.");
			} else {
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
			}


			$resp['status'] = 'success';
			$resp['msg'] = 'Success fully removed';

		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = 'Email or Username incorrect';
		}

		// $resp['status'] = 'success';
		// $resp['msg'] =$username;
		return json_encode($resp);




	}


	function void_order()
	{
		extract($_POST);


		$stmt = $this->conn->prepare("SELECT * from users where username = ? and password = ? and type= '1' ");
		$password = md5($password);
		$stmt->bind_param('ss', $username, $password);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0) {

			$resp['status'] = 'success';
			$resp['msg'] = 'Success fully removed';

		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = 'Email or Username incorrect';
		}

		// $resp['status'] = 'success';
		// $resp['msg'] =$username;
		return json_encode($resp);
	}
	function get_order()
	{
		// // echo json_encode($_SESSION['customer_code']);
		// exit;
		$update = $this->conn->query("DELETE from `customer_order`  where DATE(date_created) < CURDATE() ");
		$update = $this->conn->query("UPDATE `order_list` set `delete_flag` = 1 , `delete_reason`='Order Expired' where DATE(date_created) < CURDATE() and status = 2 ");



		extract($_POST);
		$swhere = "";
		$resp['deleted'] = '';
		if (isset($listed) && count($listed) > 0) {

			foreach ($listed as $value) {
				$orders = $this->conn->query("SELECT delete_flag,status,prev_status FROM `order_list` where id = {$value}");
				while ($row = $orders->fetch_assoc()) {
					if ($row['delete_flag'] == 1) {
						if (($key = array_search($value, $listed)) !== false) {
							unset($listed[$key]);
							$resp['deleted'] = $value;
						}
						continue;
					}
					// if ($row['status'] == 2) {


					// }
					if ($row['prev_status'] != $row['status']) {
						if (($key = array_search($value, $listed)) !== false) {

							$update = $this->conn->query("UPDATE `order_list` set `prev_status` = '{$row['status']}' where id = '{$value}'");
							if ($update) {
								$resp['status'] = 'success';
								unset($listed[$key]);
								$resp['deleted'] = $value;
								$resp['item_status'] = $row['status'];
							}


						}
						continue;
					}



				}
				if (!empty($listed)) {
					$swhere = " and id not in (" . implode(",", $listed) . ")";
					// echo $swhere;
				}


			}
		}

		if ($this->settings->userdata('type') != 3) {
			$orders = $this->conn->query("SELECT id, `queue`,menu_note,status FROM `order_list` where `status` != 1 {$swhere}  and delete_flag!=1 and user_id={$this->settings->userdata('id')}  order by id desc ,abs(unix_timestamp(date_created)) asc limit 10");

		} else {
			$orders = $this->conn->query("SELECT id, `queue`,menu_note,status FROM `order_list` where `status` != 1 {$swhere}  and delete_flag!=1 order by id desc ,abs(unix_timestamp(date_created)) asc limit 10");

		}
		$data = [];
		while ($row = $orders->fetch_assoc()) {
			$items = $this->conn->query("SELECT oi.*, concat(m.code,': ', m.name) as `item`,m.var_price FROM `order_items` oi inner join menu_list m on oi.menu_id = m.id where  order_id = '{$row['id']}'");
			$item_arr = [];
			while ($irow = $items->fetch_assoc()) {

				$jsonarray = json_decode(stripslashes($irow['var_price']), true);

				if (count($jsonarray) != 1) {
					foreach ($jsonarray as $values) {

						if ($values['row_id'] == $irow['variants']) {
							$irow['var_price'] = ' - ' . $values['var_name'];
							break;
						}
					}
				} else {
					$irow['var_price'] = '';
				}
				$item_arr[] = $irow;
			}
			$row['item_arr'] = $item_arr;
			$data[] = $row;
		}
		if ($this->settings->userdata('type') != 3) {

			$count = $this->conn->query("SELECT * from order_list where  status = 2  and delete_flag!=1 and user_id={$this->settings->userdata('id')}  ");
			$resp['count_que'] = $count->num_rows;
			$count = $this->conn->query("SELECT * from order_list where status = 3 and delete_flag!=1 and user_id={$this->settings->userdata('id')}  ");
			$resp['count_prepare'] = $count->num_rows;
			$count = $this->conn->query("SELECT * from order_list where status = 4  and delete_flag!=1 and user_id={$this->settings->userdata('id')}  ");
			$resp['count_serve'] = $count->num_rows;


		} else {

			$count = $this->conn->query("SELECT * from order_list where  status = 2  and delete_flag!=1 ");
			$resp['count_que'] = $count->num_rows;
			$count = $this->conn->query("SELECT * from order_list where  status = 3  and delete_flag!=1 ");
			$resp['count_prepare'] = $count->num_rows;
			$count = $this->conn->query("SELECT * from order_list where  status = 4  and delete_flag!=1 ");
			$resp['count_serve'] = $count->num_rows;
		}









		$resp['status'] = 'success';
		$resp['data'] = $data;

		return json_encode($resp);
	}

	function get_customer_order()
	{
		$update = $this->conn->query("DELETE from `customer_order`  where DATE(date_created) < CURDATE() ");

		$customer_code = isset($_SESSION['customer_code']) || !empty($_SESSION['customer_code']) ? $_SESSION['customer_code'] : '';


		$update = $this->conn->query("UPDATE `order_list` set `delete_flag` = 1 , `delete_reason`='Order Expired' where DATE(date_created) < CURDATE() and status = 2 ");

		$customer_where = !empty($customer_code) ? 'and customer_code = ' . $customer_code : 'and id is null';

		extract($_POST);


		$swhere = "";
		$resp['deleted'] = '';


		if (isset($listed) && count($listed) > 0) {

			foreach ($listed as $value) {
				$orders = $this->conn->query("SELECT delete_flag,status,prev_status FROM `order_list` where id = {$value} {$customer_where} ");
				while ($row = $orders->fetch_assoc()) {
					if ($row['delete_flag'] == 1) {
						if (($key = array_search($value, $listed)) !== false) {
							unset($listed[$key]);
							$resp['deleted'] = $value;
						}
						continue;
					}
					// if ($row['status'] == 2) {


					// }
					if ($row['prev_status'] != $row['status']) {
						if (($key = array_search($value, $listed)) !== false) {

							$update = $this->conn->query("UPDATE `order_list` set `prev_status` = '{$row['status']}' where id = '{$value}  {$customer_where} '");
							if ($update) {
								$resp['status'] = 'success';
								unset($listed[$key]);
								$resp['deleted'] = $value;
								$resp['item_status'] = $row['status'];
							}


						}
						continue;
					}



				}
				if (!empty($listed)) {
					$swhere = "and id not in (" . implode(",", $listed) . ")";
					// echo $swhere;
				}


			}
		}
		$orders = $this->conn->query("SELECT id, `queue`,menu_note,status FROM `order_list` where `status` != 1 {$customer_where} {$swhere}  and delete_flag!=1 order by id desc ,abs(unix_timestamp(date_created)) asc limit 10");
		// echo "SELECT id, `queue`,menu_note,status FROM `order_list` where `status` != 1 {$customer_where} {$swhere}  and delete_flag!=1 order by id desc ,abs(unix_timestamp(date_created)) asc limit 10";
		// exit;
		$data = [];
		// echo "SELECT id, `queue`,menu_note,status FROM `order_list` where `status` != 1 {$swhere}  and delete_flag!=1 order by id desc ,abs(unix_timestamp(date_created)) asc limit 10";
		// exit;
		while ($row = $orders->fetch_assoc()) {
			$items = $this->conn->query("SELECT oi.*, concat(m.code,': ', m.name) as `item`,m.var_price FROM `order_items` oi inner join menu_list m on oi.menu_id = m.id where  order_id = '{$row['id']}'");
			$item_arr = [];
			while ($irow = $items->fetch_assoc()) {

				$jsonarray = json_decode(stripslashes($irow['var_price']), true);

				if (count($jsonarray) != 1) {
					foreach ($jsonarray as $values) {

						if ($values['row_id'] == $irow['variants']) {
							$irow['var_price'] = ' - ' . $values['var_name'];
							break;
						}
					}
				} else {
					$irow['var_price'] = '';
				}
				$item_arr[] = $irow;
			}
			$row['item_arr'] = $item_arr;
			$data[] = $row;
		}
		// echo json_encode($data);
		// exit;

		$count = $this->conn->query("SELECT * from order_list where status = 2 {$customer_where} and delete_flag!=1");
		$resp['count_que'] = $count->num_rows;


		$count = $this->conn->query("SELECT * from order_list where status = 3 {$customer_where} and delete_flag!=1");
		$resp['count_prepare'] = $count->num_rows;




		$count = $this->conn->query("SELECT * from order_list where status = 4 {$customer_where}  and delete_flag!=1");
		$resp['count_serve'] = $count->num_rows;


		$resp['status'] = 'success';
		$resp['data'] = $data;

		return json_encode($resp);
	}
	function serve_order()
	{

		extract($_POST);
		$status;
		if ($order_status == 2) {
			$status = 3;
		}
		if ($order_status == 3) {
			$status = 4;
		}
		if ($order_status == 4) {
			$status = 1;
		}
		$update = $this->conn->query("UPDATE `order_list` set `status` = $status,`prev_status` = $order_status where id = '{$id}'");
		if ($update) {
			$resp['status'] = 'success';
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function delete_img()
	{
		extract($_POST);
		if (!empty($this->old_menu_image)) {
			$path = $this->old_menu_image;
		}
		if (is_file($path)) {
			if (unlink($path)) {
				$resp['status'] = 'success';
			} else {
				$resp['status'] = 'failed';
				$resp['error'] = 'failed to delete ' . $path;
			}
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = 'Unkown ' . $path . ' path';
		}
		return json_encode($resp);
	}

	function add_discountId()
	{

		extract($_POST);

		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id'))) {
				if (!empty($data))
					$data .= ",";
				$v = htmlspecialchars($this->conn->real_escape_string($v));
				$data .= " `{$k}`='{$v}' ";

				if ($k == 'middle_name' || $k == 'suffix_name') {
					continue;
				}
				if (empty($v)) {
					$resp['status'] = 'failed';
					$resp['msg'] = "Incomplete Fields";
					return json_encode($resp);
					exit;
				}
				if ($this->Idnum_exist($id_num)) {
					$resp['status'] = 'failed';
					$resp['msg'] = "Id Number already exist";
					return json_encode($resp);
					exit;
				}

			}
		}

		$sql = "INSERT INTO `discount_list` set {$data}";
		$save = $this->conn->query($sql);

		if ($save) {
			$resp['msg'] = 'Submitted Successfully';
			$resp['status'] = 'success';
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}


		// 	$resp['status'] = 'success';

		// // 	$resp['status'] = 'failed';
		// // 	$resp['error'] = 'failed to delete ' . $path;


		// // $resp['status'] = 'failed';
		// // $resp['error'] = 'Unkown ' . $path . ' path';
		return json_encode($resp);
		exit;
	}

	function Idnum_exist($id_num)
	{


		$stmt = $this->conn->prepare("SELECT * from discount_list where id_num = ? ");

		$stmt->bind_param('s', $id_num);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0) {

			return true;

		} else {
			return false;
		}

	}
	function generateRandomString($length)
	{
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$randomString = '';

		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}

		return $randomString;
	}

}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'delete_img':
		echo $Master->delete_img();
		break;
	case 'save_category':
		echo $Master->save_category();
		break;
	case 'delete_category':
		echo $Master->delete_category();
		break;
	case 'save_menu':
		echo $Master->save_menu();
		break;
	case 'delete_menu':
		echo $Master->delete_menu();
		break;
	case 'place_order':
		echo $Master->place_order();
		break;
	case 'get_customer_qrcode':
		echo $Master->get_customer_qrcode();
		break;
	case 'get_customer_order':
		echo $Master->get_customer_order();
		break;

	case 'customer_order':
		echo $Master->customer_order();
		break;
	case 'void_order':
		echo $Master->void_order();
		break;
	case 'delete_order':
		echo $Master->delete_order();
		break;
	case 'get_order':
		echo $Master->get_order();
		break;
	case 'serve_order':
		echo $Master->serve_order();
		break;
	case 'discount':
		echo $Master->add_discountId();
		break;
	case 'save_coupon':
		echo $Master->save_coupon();
		break;
	case 'add_coupon':
		echo $Master->add_coupon();
		break;
	case 'save_discount':
		echo $Master->save_discount();
		break;
	case 'get_price':
		echo $Master->get_price();
		break;
	case 'delete_coupon':
		echo $Master->delete_coupon();
		break;
	case 'delete_dis':
		echo $Master->delete_dis();
		break;
	default:
		// echo $sysset->index();
		break;
}