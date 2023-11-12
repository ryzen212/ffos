<style>
    #pos-field {
        min-height: 54em;
        display: flex;
    }

    #menu-list {
        height: 100%;
        overflow-y: auto;
        overflow-x: hidden;
    }

    #order-list {

        height: 100%;
        overflow: auto;
    }

    #cat-list {
        height: 4em !important;
        /* overflow: auto; */
        display: flex;

    }

    #item-list {
        height: 40em !important;
        overflow-y: auto;
        overflow-x: hidden;
    }

    #item-list.empty-data {
        width: 100%;
        align-items: center;
        justify-content: center;
        display: flex;
    }

    #item-list.empty-data:after {
        content: 'Selected category has no menu items yet.';
        color: #b7b4b4;
        font-size: 1.7em;
        font-style: italic;
    }

    div#order-items-holder {
        min-height: 46em !important;
     
    
        overflow: auto;
        position: relative;
    }

    div#order-items-header {
        position: sticky !important;
        top: 0;
        z-index: 1;
    }

    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    button:focus {
        outline: 1px dotted;
        */ outline: 0px auto -webkit-focus-ring-color !important;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }

    .tabbable .nav-tabs {
        overflow-x: hidden;
        overflow-y: hidden;
        flex-wrap: nowrap;

    }

    .tabbable {
        /* border: .5px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.25rem; */
    }

    .tabbable .nav-tabs .nav-link {
        white-space: nowrap;
        border: 0 !important;

        outline: 0px dotted !important;
    }

    /* Hide scrollbar for Chrome, Safari and Opera */
    #nav-tab::-webkit-scrollbar {
        display: none;
    }

    /* Hide scrollbar for IE, Edge and Firefox */
    #nav-tab {
        height: 100%;
        -ms-overflow-style: none;
        /* IE and Edge */
        scrollbar-width: none;
        /* Firefox */
    }

    .searched {
        display: block !important;
    }

    /* .arrow {
    padding:10px;
    position:absolute;
    background-color:#fff;
    z-index:1
}

#slideBack {
      left:0;
}


#slide {
      right:30%;
} */


    #cimg {
        padding: 0.25rem;
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.075);
        width: -webkit-fill-available;
        height: auto;

    }

    ?>
</style>

<div class="row mt-3 w-100 justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card rounded-0">
            <div class="card-body">
                <form action="" id="sales-form">
                    <input type="hidden" name="total_amount" value="0">
                    <!-- <input type="hidden" name="coupon_ids" id="coupons"> -->
                    <div id="pos-field" class='row'>
                        <div id="menu-list" class='col-xl-8 col-lg-12 col-md-12 col-sm-12'>


                            <h3>Order</h3>
                            <div class="row">
                                <div class="rounded-0 input-group col col-lg-6 col-md-12  col-sm-12 mb-2">

                                    <input type="text" class="form-control rounded" id="Search"
                                        style="border-radius:0px;" onkeyup="item_search()" placeholder="Search for Menu"
                                        title="Type in a name">

                                </div>
                            </div>
                            <!-- <div class="row">
                                <button
                                    class="btn btn-lg mr-2 btn-warning text-light font-weight-bold col col-xl-1 col-lg-3 col-md-3 col-sm-3 mb-2"
                                    type='button' id='add_discount' data-toggle="tooltip" data-placement="top"
                                    title="Add Discount">
                                    <i class='fas fa-percent' style='font-size:20px'></i></button>
                                <button
                                    class="btn btn-lg btn-warning mr-2 text-light font-weight-bold menu_note col col-xl-1 col-lg-3 col-md-3 col-sm-3 mb-2"
                                    type='button' id='add_note' data-toggle="tooltip" data-placement="top"
                                    title="Add Note"><i class='fas fa-file-alt' style='font-size:20px'></i></button>
                                <button
                                    class="btn btn-lg btn-warning mr-2 text-light font-weight-bold menu_note col col-xl-1 col-lg-3 col-md-3 col-sm-3 mb-2"
                                    type='button' id='add_coupon' data-toggle="tooltip" data-placement="top"
                                    title="Add Coupon"><i class='fas fa-ticket-alt' style='font-size:20px'></i></button>
                            </div> -->

                            <div id="cat-list" class="py-1 ">
                                <div class="col-1 d-flex justify-content-center" id='prev_cat'>
                                    <button id="slideBack" class="arrow btn btn-warning text-light" type="button"><i
                                            class='fas fa-angle-left' style='font-size:20px'></i></button>
                                </div>


                                <nav class="tabbable col-10" id='tabbale'>


                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                        <?php

                                        $cid = '';
                                        $categories = $conn->query("SELECT * FROM `category_list` where  delete_flag = 0 and `status` = 1 order by `name` asc");
                                        while ($row = $categories->fetch_assoc()):
                                            if (empty($cid)) {
                                                $cid = $row['id'];
                                            }
                                            ?>
                                            <button
                                                class="nav-item  btn-lg nav-link active cat_btn <?= isset($cid) && $cid == $row['id'] ? "bg-gradient-warning text-light" : "bg-gradient-light" ?>"
                                                type="button" data-id='<?= $row['id'] ?>'>
                                                <?= $row['name'] ?>
                                            </button>

                                        <?php endwhile; ?>




                                    </div>


                                </nav>
                                <div class="col-1 d-flex justify-content-center" id='next_cat'>
                                    <button id="slide" class="arrow btn btn-warning text-light " type="button"><i
                                            class='fas fa-angle-right' style='font-size:20px'></i></button>

                                </div>


                            </div>




                            <h4>Menu</h4>
                            <div id="item-list" class="py-1">
                                <div class="row ">
                                    <?php
                                    $variants = '';
                                    $name = '';
                                    $id = '';
                                    $code = '';
                                    $menus = $conn->query("SELECT * FROM `menu_list` where  delete_flag = 0 and `status` = 1 order by `name` asc");
                                    while ($row = $menus->fetch_assoc()):
                                        $price = '<br>';
                                        $variants = json_decode(stripslashes($row['var_price']), true);
                                        $button = 'variant_btn';
                                        $id = $row['id'];

                                        $code = $row['code'];
                                        $name = $row['name'];
                                        $modal = 'data-toggle="modal" data-target="#variants_modal"';
                                        if (count($variants) == 1) {
                                            foreach ($variants as $values) {
                                                // if discounted
                                                $old_price = format_num($values['price'], 2);
                                                $price = format_num($values['price'], 2);
                                                $button = 'item-btn';
                                                $modal = '';
                                                $badge_dis = '';
                                                $dis_item = $conn->query("SELECT type,amount FROM `discount_item` where  item_id =  $id and exp_date > CURDATE() and qty > 0 and `variant` = '" . $values['var_name'] . "' ");
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

                                            }
                                        }
                                        ?>

                                        <div class="mt-2 col col-xl-3 col-lg-4  col-md-6 col-sm-6 <?= isset($cid) && $cid == $row['category_id'] ? "" : "d-none" ?> menu-item"
                                            data-cat-id='<?= $row['category_id'] ?>'>

                                            <button
                                                class="btn btn-default btn-block btn-xs  px-2 shadow bg-white border  <?= $button ?>"
                                                type="button" data-id='<?= $row['id'] ?>' data-price='<?= $price ?>'
                                                <?= $modal ?>>
                                                <img src="<?php echo (validate_image($row['menu_img'])) ?>" alt="" id="cimg">
                                                <h6 class="m-0 truncate-1">
                                                    <?= $row['code'] ?>
                                                </h6>
                                                <p class="m-0 truncate-1">
                                                    <?= $name ?>
                                                </p>
                                                <h5 class="m-0 truncate-1">
                                                    <?= $price ?>
                                                    <?= isset($badge_dis) ? $badge_dis : '' ?>
                                                </h5>

                                            </button>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>


                        </div>
                        <div id="order-list" class="bg-gradient-dark p-1 col col-xl-4 col-lg-12 col-md-12 col-sm-12">

                            <div id="order-items-holder" class="bg-gradient-light mb-3">
                                <div id="order-items-header">
                                    <div class="d-flex w-100 bg-gradient-warning">
                                        <div class="col-3 text-center font-weight-bolder m-0 border">QTY</div>
                                        <div class="col-5 text-center font-weight-bolder m-0 border">Menu</div>
                                        <div class="col-3 text-center font-weight-bolder m-0 border">Total</div>
                                        <div class="col-1 text-center font-weight-bolder m-0 border"><i
                                                class="fa fa-times"></i></div>

                                    </div>
                                </div>
                                <div id="order-items-body"></div>
                            </div>
                 
                           
                          
                            <div class="d-flex w-100 mb-2">
                                <h3 class="col-5 mb-0">Grand Total</h3>
                                <h3 class="col-7 mb-0 bg-gradient-light rounded-0 text-right" id="grand_total">0.00</h3>
                            </div>


                       

                            <div class="m-auto d-flex justify-content-center  ">
                                <button class="btn btn-lg mr-2 btn-warning text-light font-weight-bold">Place
                                    Order</button>

                                <button class="btn btn-lg btn-warning text-light font-weight-bold clear_order"
                                    type='button'>Cancel Order</button>
                            </div>


                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<noscript id="item-clone">
    <div class="d-flex w-100 bg-gradient-light product-item">
        <div class="col-3 text-center font-weight-bolder m-0 border align-middle">
            <input type="hidden" name="menu_id[]" value="">
            <input type="hidden" name="price[]" value="">
            <div class="row ">
                <button class="btn btn-warning btn-xs btn-flat minus-qty col col-xl-3 col-md-12 col-sm-12 "
                    type="button"><i class="fa fa-minus"></i></button>
                <input type="number" min='1' value='1' name="quantity[]"
                    class="form-control form-control-xs rounded-0 text-center col col-xl-6 col-md-12 col-sm-12" required
                    readonly>
                <button class="btn btn-warning btn-xs btn-flat plus-qty col col-xl-3 col-md-12 col-sm-12"
                    type="button"><i class="fa fa-plus"></i></button>
            </div>
        </div>
        <div class="col-5 font-weight-bolder m-0 border align-middle">
            <div style="line-height:1em" class="text-sm">
                <div class="w-100 d-flex align-items-center">

                    <p class="m-0 menu_name">Menu name</p>
                </div>


            </div>
        </div>
        <div class="col-3 font-weight-bolder m-0 border align-middle text-right menu_total">0.00</div>
        <div class="col-1 text-center text-danger font-weight-bolder m-0 border">
            <a class="text-danger rem-item"><i class="fa fa-times"></i></a>
        </div>
    </div>
</noscript>
<!-- Variant Modal  modal -->
<div class="modal fade" id="variants_modal" tabindex="-1" role="dialog" aria-labelledby="variants_modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="var_title">
                    Variants
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id=variants_modal_body>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

            </div>
        </div>
    </div>
</div>
<!-- end Variant  modal -->




<script>


    function item_search() {
        var input = document.getElementById("Search");
        var filter = input.value.toLowerCase();
        var nodes = document.getElementsByClassName('menu-item');



        for (i = 0; i < nodes.length; i++) {

            nodes[i].style.display = "none";
            if (nodes[i].innerText.toLowerCase().includes(filter)) {
                nodes[i].classList.add("searched");


            } else {

                nodes[i].classList.remove("searched");
            }

        }
        if (filter == '') {

            for (i = 0; i < nodes.length; i++) {
                nodes[i].style.display = "block";
                nodes[i].classList.remove("searched");
            }
        }
        if ($('#item-list>.row>.col:visible').length > 0) {
            if ($('#item-list').hasClass('empty-data') == true) {
                $('#item-list').removeClass('empty-data');
            }
        } else {
            if ($('#item-list').hasClass('empty-data') == false) {
                $('#item-list').addClass('empty-data');
            }
        }
    }
    // end of search bar




    var button = document.getElementById('slide');
    button.onclick = function () {
        var container = document.getElementById('nav-tab');
        sideScroll(container, 'right', 25, 100, 5);
    };

    var back = document.getElementById('slideBack');
    back.onclick = function () {
        var container = document.getElementById('nav-tab');
        sideScroll(container, 'left', 25, 100, 5);
    };

    function sideScroll(element, direction, speed, distance, step) {
        scrollAmount = 0;
        var slideTimer = setInterval(function () {
            if (direction == 'left') {
                element.scrollLeft -= step;
            } else {
                element.scrollLeft += step;
            }
            scrollAmount += step;
            if (scrollAmount >= distance) {
                window.clearInterval(slideTimer);
            }
        }, speed);
    }


    function calc_total() {
   
      
        var coupon_percent = $('#coupon_percent').text();

        var gt = 0;
        $('#order-items-body .product-item').each(function () {
            var total = 0;
            var price = $(this).find('input[name="price[]"]').val()
            price = price > 0 ? price : 0;
            var qty = $(this).find('input[name="quantity[]"]').val()
            qty = qty > 0 ? qty : 0;
            total = parseFloat(price) * parseFloat(qty)

            gt += parseFloat(total)
            $(this).find('.menu_total').text(parseFloat(total).toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }))
        })


        $('[name="total_amount"]').val(gt).trigger('change')
        $('#grand_total').text(parseFloat(gt).toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }))

    }

    $(document).ready(function () {


        //     <?php if (isset($_GET['s']) && $_GET['s'] == 'complete') { ?>

            alert_toast("Order Complete", "success");
        <?php } ?>









        // add id form
        $('#add_id_frm').submit(function (e) {
            e.preventDefault();
            // alert('awws')
            $('.err-msg').remove();
            var _this = $(this)
            var data = $(this).serialize();
            var data_aray = $(this).serializeArray();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=discount",
                method: 'POST',
                data: data,
                dataType: 'json',
                error: function (err) {
                    console.log(err);
                    alert_toast("An error has occurred.", "error");
                },
                success: function (resp) {
                    if (resp.status == 'success') {
                        alert_toast(resp.msg, 'success');


                        // $( "#container_table" ).load(window.location.href + " #container_table" );
                        // $("#container_table").load();
                        console.log(data_aray)
                        var last_row = table.row(':last').data()
                        var new_id = data_aray[0]['value'];
                        var fname = data_aray[1]['value'];
                        var lname = data_aray[2]['value'] + ', ';
                        var mname = !data_aray[3]['value'] ? '' : ' ' + data_aray[3]['value'];
                        var sname = !data_aray[4]['value'] ? '' : ' ' + data_aray[4]['value'] + '.';
                        var new_name = lname + fname + mname + sname;
                        var new_address = data_aray[5]['value'];
                        var new_idType = data_aray[6]['value'];

                        table.row.add([Number(last_row[0]) + 1, new_id, new_name, new_address, new_idType])
                        table.draw();
                        $("#add_id_frm")[0].reset();
                        // Optionally reload the page
                        $('#addId').modal('hide');
                    } else if (resp.msg) {
                        var el = $('<div>')
                        el.addClass("alert alert-danger err-msg").text(resp.msg)
                        _this.prepend(el)
                        el.show('slow')
                        $('.modal').scrollTop(0);

                        // alert_toast(resp.msg, 'error');
                    } else {
                        alert_toast("An unknown error occurred.", 'error');
                    }
                }
            });

        });

        // Add Coupon
        var coupons_id = [];
        var coupons_code = [];
        var coupon_peso = 0;
        var coupon_percent = 0;
        $('#addCouponFrm').submit(function (e) {
            e.preventDefault();

            // alert('awws')
            $('.err-msg').remove();
            var _this = $(this)
            var data = $(this).serialize();
            var data_aray = $(this).serializeArray();


            if (coupons_code.length > 0) {
                if (coupons_code.indexOf($('#coupon_code').val()) != -1) {
                    var el = $('<div>')
                    el.addClass("alert alert-danger err-msg col-12").text('Coupon Is already use')
                    _this.prepend(el)
                    el.show('slow')
                    return false;
                }

            }


            $.ajax({
                url: _base_url_ + "classes/Master.php?f=add_coupon",
                method: 'POST',
                data: data,
                dataType: 'json',
                error: function (err) {
                    console.log(err);
                    alert_toast("An error has occurred.", "error");
                },
                success: function (resp) {
                    if (resp.status == 'success') {
                        alert_toast(resp.msg, 'success');




                        var last_row = table_coupon.row(':last').data()

                        if (last_row === undefined) {
                            count = 1;
                        } else {
                            var count = Number(last_row[0]) + 1
                        }


                        var id = resp.data['id'];
                        var coupon_code = resp.data['coupon_code'];
                        coupons_code.push(coupon_code);
                        var name = resp.data['name'];
                        var amount = resp.data['amount'];
                        var amount_type = resp.data['amount_type'];


                        if (amount_type == 1) {
                            coupon_percent = Number(coupon_percent + resp.data['amount'])
                            amount = amount + '% off'
                            $('#coupon_percent').text(coupon_percent + '% ')
                        } else {
                            coupon_peso = Number(coupon_peso) + Number(resp.data['amount'])

                            amount = '₱' + amount + ' off';
                            $('#coupon_peso').text('₱' + Number.parseFloat(coupon_peso).toFixed(2))

                        }

                        coupons_id.push(id);
                        $('#coupons').val(JSON.stringify(coupons_id));

                        table_coupon.row.add([count, id, coupon_code, name, amount])
                        table_coupon.draw();
                        calc_total()

                        $("#addCouponFrm")[0].reset();
                        // Optionally reload the page
                        // $('#addId').modal('hide');
                    } else if (resp.msg) {
                        var el = $('<div>')
                        el.addClass("alert alert-danger err-msg col-12").text(resp.msg)
                        _this.prepend(el)
                        el.show('slow')
                        $('.modal').scrollTop(0);

                        // alert_toast(resp.msg, 'error');
                    } else {
                        alert_toast("An unknown error occurred.", 'error');
                    }
                }
            });

        });










        let Is_void = false;

        // cattegory arrow 
        // is_overflow();

        const slider = document.querySelector('#nav-tab');
        let interactionDown = false;
        let startX, scrollLeft;

        let startInteraction = function (e) {
            interactionDown = true;
            if (e.type === 'touchstart') {
                startX = e.touches[0].pageX - slider.offsetLeft;
            } else {
                startX = e.pageX - slider.offsetLeft;
            }
            scrollLeft = slider.scrollLeft;
        };

        let stopInteraction = function (event) {
            interactionDown = false;
        };

        let performInteraction = function (e) {
            e.preventDefault();
            if (!interactionDown) {
                return;
            }
            const x = (e.type === 'touchmove') ? e.touches[0].pageX - slider.offsetLeft : e.pageX - slider.offsetLeft;
            const scroll = x - startX;
            slider.scrollLeft = scrollLeft - scroll;
        };

        // Add the event listeners for both mouse and touch events
        slider.addEventListener('mousedown', startInteraction, false);
        slider.addEventListener('touchstart', startInteraction, false);

        slider.addEventListener('mouseup', stopInteraction, false);
        slider.addEventListener('touchend', stopInteraction, false);

        slider.addEventListener('mousemove', performInteraction, false);
        slider.addEventListener('touchmove', performInteraction, false);

        slider.addEventListener('mouseleave', stopInteraction, false);



        $('body').addClass('sidebar-collapse')
        if ($('#item-list>.row>.col:visible').length > 0) {
            if ($('#item-list').hasClass('empty-data') == true) {
                $('#item-list').removeClass('empty-data');
            }
        } else {
            if ($('#item-list').hasClass('empty-data') == false) {
                $('#item-list').addClass('empty-data');
            }
        }
        $('.cat_btn').click(function () {
            document.getElementById("Search").value = '';
            item_search();
            $('.cat_btn.bg-gradient-warning').removeClass('bg-gradient-warning text-light').addClass('bg-gradient-light border')
            $(this).removeClass('bg-gradient-light border').addClass('bg-gradient-warning text-light')
            var id = $(this).attr('data-id')
            $('.menu-item').addClass('d-none')
            $('.menu-item[data-cat-id="' + id + '"]').removeClass('d-none')
            if ($('#item-list>.row>.col:visible').length > 0) {
                if ($('#item-list').hasClass('empty-data') == true) {
                    $('#item-list').removeClass('empty-data');
                }
            } else {
                if ($('#item-list').hasClass('empty-data') == false) {
                    $('#item-list').addClass('empty-data');
                }
            }
        })

        $('.variant_btn').click(function () {
            var id = $(this).attr("data-id");

            $.ajax({
                url: _base_url_ + "customer/sales/var_modal.php",
                type: "post",
                data: { id: id },
                success: function (data) {
                    $('#variants_modal_body').html(data);
                }

            });
        });

        $('.item-btn').click(function () {
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

            item.find('.rem-item').click(function () {

                item.remove()
                calc_total()

            })

        })

        $('#void_order').submit(function (e) {

            var id = $('#item_id').val();
            e.preventDefault();
            var data = $(this).serialize();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=void_order",
                method: 'POST',
                data: data,
                dataType: 'json',
                error: function (err) {
                    console.log(err);
                    alert_toast("An error has occurred.", "error");
                },
                success: function (resp) {
                    if (resp.status == 'success') {
                        alert_toast(resp.msg, 'success');
                        // // remove.remove()

                        if ($('#order-items-body .product-item').length <= 1) {

                            $('#show_idName').val('');
                            $("#show_idNum").val('');

                            $(".product-item").remove();
                            $("#grand_total").text("0.00");

                            $('input[name="tendered_amount"]').val(0)
                            $('#menu-note').val('');
                            $('#VoidModal').modal('toggle');

                        };
                        $('.product-item[data-id=' + id + ']').remove()
                        $("#void_order")[0].reset();
                        $('#VoidModal').modal('toggle');







                        calc_total()

                        // Optionally reload the page
                        // location.reload();
                    } else if (resp.msg) {
                        alert_toast(resp.msg, 'error');
                    } else {
                        alert_toast("An unknown error occurred.", 'error');
                    }
                }
            });

        });


        $('.clear_order').click(function () {
            // if walang item

            if ($('#order-items-body .product-item').length <= 0) {
                alert_toast("Please Add atleast 1 Item First.", "warning")
                return false;
            }

            $('#show_idName').val('');
            $("#show_idNum").val('');



            $(".product-item").remove();
            $("#grand_total").text("0.00");

            $('input[name="tendered_amount"]').val(0)
            $('#menu-note').val('');

            alert_toast('Order Cleared', 'success');




        })
        $('#cancel_note').click(function () {
            $('#menu-note').val('');
        })

        $('input[name="tendered_amount"], input[name="total_amount"]').on('input change', function () {
            var total = $('input[name="total_amount"]').val()
            var tendered = $('input[name="tendered_amount"]').val()
            total = total > 0 ? total : 0;
            tendered = tendered > 0 ? tendered : 0;
            var change = 0;
            // alert(tendered +'-'+ total)
            if (parseFloat(tendered) >= parseFloat(total)) {
                change = parseFloat(tendered) - parseFloat(total)
            }

            $('#change').text(parseFloat(change).toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }))


        })
        $('#sales-form').submit(function (e) {
            e.preventDefault()
            var data = $(this).serialize();
            // alert(data)
            // alert($('#menu-note').val())
            // console.log(data);
            // Add Note
            if (!$.trim($('#menu-note').val()).length < 1) {
                var data = $(this).serialize() + "&menu_note=" + $('#menu-note').val();
            }


            if ($('#order-items-body .product-item').length <= 0) {
                alert_toast("Please Add atleast 1 Item First.", "warning")
                return false;
            }
            if ($('input[name="tendered_amount"]').val() == '' || parseFloat($('input[name="tendered_amount"]').val()) < parseFloat($('input[name="total_amount"]').val())) {
                alert_toast("Invalid Tenedered Amount.", "error");

                return false;
            }

            start_loader()
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=customer_order",
                method: 'POST',
                data: data,
                dataType: 'json',
                error: err => {
                    console.log(err)
                    alert_toast("An error has occurred.", "error")
                    end_loader()
                },
                success: function (resp) {
                    if (resp.status == 'success') {
                        alert_toast(resp.msg, 'success')
                        window.open(_base_url_ + "customer/sales/receipt.php?id=" + resp.oid, '_self')

                    } else if (!!resp.msg) {
                        alert_toast(resp.msg, 'error')
                    } else {
                        alert_toast(resp.msg, 'error')
                    }
                    end_loader()
                }
            })
        });





        $('#add_coupon').click(function () {

            if ($('#order-items-body .product-item').length <= 0) {
                alert_toast("Please Add atleast 1 Item First.", "warning")
                return false;
            }
            $('#CouponModal').modal('show')
        })
        // $('#VoidModal').modal({

        //     backdrop: 'static'
        // })
    });

</script>