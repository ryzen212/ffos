<?php
require_once('./../../config.php');

if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM `customer_order` where id = '{$_GET['id']}'");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_array() as $k => $v) {
            if (!is_numeric($k))
                $$k = htmlspecialchars_decode($v);
        }



    }

    // echo $total_coupon;
    // exit;
}
// if (isset($user_id)) {
//     $user = $conn->query("SELECT username FROM `users` where id= '{$user_id}'");
//     if ($user->num_rows > 0) {
//         $processed_by = $user->fetch_array()[0];
//     }
// }


?>
<!DOCTYPE html>
<html lang="en">
<?php

include_once('./../inc/header.php');
?>

<body>

    <style>
        /* html, body{
        width:100% !important;
        min-height:unset !important;
        min-width:unset !important;
    } */
    </style>
    <?php
    require_once('../inc/topBarNavreceipt.php');
    ?>
    <section class="container">
        <div class="row mt-5">
            <div class="col-12">

                <div class="card">
                    <div class="card-body">
                        <div class="style px-2 py-1" line-height="1em">
                            <div class="mb-0 text-center font-weight-bolder">
                                <?= $_settings->info('name') ?>
                            </div>
                            <div class="mb-0 text-center font-weight-bolder"><img src="<?=base_url.$qr_img ?>" alt=""></div>
                            
                            <hr>
                            <div class="d-flex w-100">
                                <div class="col-auto">Customer Code:</div>
                                <div class="col-auto flex-shrink-1 flex-grow-1 pl-2">
                                   <b><?= isset($queue) ? $queue : '' ?></b>
                                </div>
                            </div>
                            <div class="d-flex w-100">
                                <div class="col-auto">Date & Time:</div>
                                <div class="col-auto flex-shrink-1 flex-grow-1 pl-2">
                                    <?= isset($date_created) ? date("M, d Y H:i", strtotime($date_created)) : '' ?>
                                </div>
                            </div>
                            <!-- <div class="d-flex w-100">
                                <div class="col-auto">Processed By:</div>
                                <div class="col-auto flex-shrink-1 flex-grow-1 pl-2">
                                    <?= isset($processed_by) ? $processed_by : '' ?>
                                </div>
                            </div> -->
                            <hr>
                            <div class="w-100 border-bottom border-dark" style="display:flex">
                                <div style="width:15%" class="font-weight-bolder text-center">QTY</div>
                                <div style="width:55%" class="font-weight-bolder text-center">Items</div>
                                <div style="width:30%" class="font-weight-bolder text-center">Total</div>
                            </div>
                            <?php if (isset($id)): ?>
                                <?php
                                $total_noVat = 0;
                                $items = $conn->query("SELECT oi.*, concat(m.code,' - ', m.name) as `item`,m.var_price FROM `customer_items` oi inner join `menu_list` m on oi.menu_id = m.id where oi.order_id = '{$id}'");
                                while ($row = $items->fetch_assoc()):
                                    $total_noVat = $total_noVat + format_num($row['price'] * $row['quantity'], 2);
                                   
                                    ?>
                                    <div class="w-100" style="display:flex">
                                        <div style="width:15%" class="text-center">
                                            <?= format_num($row['quantity']) ?>
                                        </div>
                                        <div style="width:55%" class="">
                                            <div style="line-height:1em">
                                                <div>

                                                    <?php
                                                    $item = $row['item'];
                                                    $jsonarray = json_decode(stripslashes($row['var_price']), true);

                                                    if (count($jsonarray) != 1) {
                                                        foreach ($jsonarray as $values) {

                                                            if ($values['row_id'] == $row['variants']) {
                                                                $item = $item . ' : ' . $values['var_name'];
                                                                break;
                                                            }
                                                        }
                                                    }
                                                    echo $item;

                                                    ?>
                                                </div>
                                                <small class="text-muted">x
                                                    <?= format_num($row['price'], 2) ?>
                                                </small>
                                            </div>
                                        </div>
                                        <div style="width:30%" class="text-right">
                                            <?= format_num($row['price'] * $row['quantity'], 2) ?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                            <div class="border border-dark mb-1"></div>

                            <div class="w-100 mb-2" style="display:flex">
                                <h5 style="width:70%" class="mb-0 font-weight-bolder">Grand Total</h5>
                                <h5 style="width:30%" class="mb-0 font-weight-bolder text-right">
                                    <?= isset($total_amount) ? format_num($total_amount, 2) : '0.00' ?>
                                </h5>
                            </div>
                          

                          
                          
                         
                           
                            <!-- <div class="border border-dark mb-0"></div>

                            <div class="border border-dark mb-1"></div>
                            <div class="py-3">
                                <center>
                                    <div class="font-weight-bolder">Queue #</div>
                                </center>
                                <h3 class="text-center foont-weight-bolder mb-0">
                                    <?= isset($queue) ? $queue : '' ?>
                                </h3>
                            </div> -->
                            <div class="border border-dark mb-1"></div>
                            <div class="d-flex justify-content-center">
                                <div class="mb-0 ">TIN:
                                    <?= isset($_SESSION) ? $_SESSION['system_info']['tin'] : '' ?>
                                </div>

                            </div>
                            <div class="d-flex justify-content-center">
                                <div class="mb-0 ">Address:
                                    <?= isset($_SESSION) ? $_SESSION['system_info']['address'] : '' ?>
                                </div>

                            </div>
                            <div class="d-flex justify-content-center">
                                <div class="mb-0 ">Phone:
                                    <?= isset($_SESSION) ? $_SESSION['system_info']['phone'] : '' ?>
                                </div>

                            </div>
                            <div class="d-flex justify-content-center">
                                <div class="mb-0 ">Email:
                                    <?= isset($_SESSION) ? $_SESSION['system_info']['email'] : '' ?>
                                </div>

                            </div>

                            <div class="border border-dark mb-1"></div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <!-- <button type="button" class="btn btn-success btn-lg mr-2" onclick="print_receipt()">Print
                                Receipt</button> -->
                            <button type="button" class="btn btn-warning btn-lg " onclick="complete_order()">Next
                                Order</button>
                        </div>
                    </div>
                </div>
            </div>


</body>

<script>


    document.querySelector('title').innerHTML = "Unofficial Receipt - Print View"

    function print_receipt() {
        // alert('aw');

        setTimeout(() => {

            var nw = window.open(_base_url_ + "customer/sales/print_receipt.php?id=<?php echo $_GET['id'] ?>", '_blank', "width=" + ($(window).width() * .2) + ",left=" + ($(window).width() * .3) + ",height=" + ($(window).height() * .8) + ",top=" + ($(window).height() * .1))
            setTimeout(() => {
                nw.print()
                setTimeout(() => {
                    nw.close()
                    location.reload()
                }, 300);
            }, 200);
        }, 200);
    }
    function complete_order() {
        window.location.replace(_base_url_ + "customer/?page=sales/manage_sale&s=complete")

    }





</script>

</html>