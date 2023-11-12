<?php
require_once('./../../config.php');

$id = $_POST['id'];


$variants = '';
$name = '';

$code = '';
$menus = $conn->query("SELECT * FROM `menu_list` where  id = " . $id . "");
while ($row = $menus->fetch_assoc()) {
    $price = '';
    $variants = json_decode(stripslashes($row['var_price']), true);
    $button = 'variant_btn';
    $id = $row['id'];
    $code = $row['code'];
    $name = $row['name'];
    $modal = 'data-toggle="modal" data-target="#variants_moadal"';
    // if (count($variants) == 1) {
    //     foreach ($variants as $values) {
    //         $price = format_num($values['price'], 2);
    //         $button = 'item-btn';
    //         $modal = '';
    //     }
    // }
}
// include_once('./../inc/header.php');
?>



<div class="row row-cols-xl-4 row-cols-md-3 row-cols-sm-2 gy-2 gx-2">
    <?php foreach ($variants as $values) {
        $badge_dis = '';
        $price = format_num($values['price'], 2);
        $dis_item = $conn->query("SELECT type,amount FROM `discount_item` where  item_id =  $id and exp_date > CURDATE() and qty > 0  and (`variant` = '" . $values['var_name'] . "' or `variant` = 'All') ");
        if ($dis_item->num_rows >= 1) {
            while ($dis = $dis_item->fetch_assoc()) {
                if ($dis['type'] == 1) {
                    $price = format_num($values['price'] - ($values['price'] * ($dis['amount'] / 100)), 2);

                    $badge_dis = '<span class="badge badge-danger">' . $dis['amount'] . '% off</span>';
                } else if ($dis['type'] == 2) {

                    $badge_dis = '<span class="badge badge-danger">₱' . $dis['amount'] . ' off</span>';
                    $price = format_num($values['price'] - $dis['amount'], 2);
                }

                // $price =$values['var_name'];
            }
        }
        ?>
        <div class="mt-2 col ">

            <button class="btn btn-default btn-block btn-xs  px-2 shadow bg-white border item-btn_var" type="button"
                data-id='<?= $id . '-' . $values['row_id'] ?>' data-price=' <?= $price ?>'>

                <h6 class="m-0 truncate-1">
                    <?= $code . ' : ' . $name ?>
                </h6>

                <p class="m-0 truncate-1">
                    <?= $values['var_name'] ?>
                </p>
                <h5 class="m-0 truncate-1">
                    <?= $price ?>
                    <?= $badge_dis ?>
                </h5>
            </button>
        </div>

    <?php } ?>
</div>


<script>

$( document ).ready(function() {
    $('#var_title').html('<?= $name ?>');
});
    $('.item-btn_var').click(function () {
        alert_toast('Item added', 'success')
        var id = $(this).attr('data-id')
        var price = $(this).attr('data-price')
        var code = $(this).children("h6").text();
        var name = $(this).children("p").text();
        var item = $($('noscript#item-clone').html()).clone()
        if ($('#order-items-body .product-item[data-id="' + id + '"]').length > 0) {
            item = $('#order-items-body .product-item[data-id="' + id + '"]')
            var qty = item.find('input[name="quantity[]"]').val()
            qty = qty > 0 ? qty : 0;
            qty = parseInt(qty) + 1;
            item.find('input[name="quantity[]"]').val(qty)
            calc_total()
            return false;
        }
        item.attr('data-id', id)
        item.find('input[name="menu_id[]"]').val(id)
        item.find('input[name="price[]"]').val(price)
        item.find('.menu_name').html(code + ':\n' + name);
        // item.find('.menu_price').text("x " + (parseFloat(price).toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 })))
        // item.find('.menu_total').text((parseFloat(price).toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 })))
        $('#order-items-body').append(item)
        calc_total()
        item.find('.minus-qty').click(function () {
            var qty = item.find('input[name="quantity[]"]').val()
            qty = qty > 0 ? qty : 0;
            qty = qty == 1 ? 1 : parseInt(qty) - 1
            item.find('input[name="quantity[]"]').val(qty)
            calc_total()
        })
        item.find('.plus-qty').click(function () {
            var qty = item.find('input[name="quantity[]"]').val()
            qty = qty > 0 ? qty : 0;
            qty = parseInt(qty) + 1
            item.find('input[name="quantity[]"]').val(qty)
            calc_total()
        })

        item.find('.rem-item').click(function(){
             
             item.remove()
             calc_total()
  
     })

    })
</script>