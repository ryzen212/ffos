<script src="<?php echo base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="<?php echo base_url ?>plugins/chart.js/Chart.min.js"></script>
    <!-- Sparkline -->
    <script src="<?php echo base_url ?>plugins/sparklines/sparkline.js"></script>
    <!-- Select2 -->
    <script src="<?php echo base_url ?>plugins/select2/js/select2.full.min.js"></script>
    <!-- JQVMap -->
    <script src="<?php echo base_url ?>plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="<?php echo base_url ?>plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="<?php echo base_url ?>plugins/jquery-knob/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="<?php echo base_url ?>plugins/moment/moment.min.js"></script>
    <script src="<?php echo base_url ?>plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="<?php echo base_url ?>plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="<?php echo base_url ?>plugins/summernote/summernote-bs4.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables/dataTables.buttons.min.js"></script>
    <script src="<?php echo base_url ?>plugins/selectize/js_selectize.min.js"></script>
    <!-- overlayScrollbars -->
    <!-- <script src="<?php echo base_url ?>plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script> -->
    <!-- AdminLTE App -->
    <script src="<?php echo base_url ?>dist/js/adminlte.js"></script>
<script>
    var isDiscount = false;
    function discount_price(discount_price) {

        var discount = $('#discount_total').text();
        var grand_total = $('#grand_total').text();

        grand_total = grand_total.replace(/\,/g, '')
        var afdiscount = '0.00';
        if (discount_price) {
            afdiscount = (Number(grand_total) / 1.12) * .8;
            afdiscount = Number.parseFloat(afdiscount).toFixed(2);

        } else {
            afdiscount = (Number(grand_total) * 1.12) / .8;
            afdiscount = Number.parseFloat(afdiscount).toFixed(1);
            // afdiscount = Math.round(afdiscount)
        }


        $('[name="total_amount"]').val(afdiscount).trigger('change')
        // if(discount =='20%'){
        //     var afdiscount=grand_total;
        // }
        // alert('aw')



        return afdiscount;

    }
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
        var discount = $('#discount_total').text();

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

        if (discount != '') {

            gt = (parseFloat(gt) / 1.12) * .8;
            gt = Number.parseFloat(gt).toFixed(2);
        }
        $('[name="total_amount"]').val(gt).trigger('change')
        $('#grand_total').text(parseFloat(gt).toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }))

    }

    $(document).ready(function () {


        //     <?php if (isset($_GET['s']) && $_GET['s'] == 'complete') { ?>

            alert_toast("Order Complete", "success");
        <?php } ?>

        var table = $('#discount_table').DataTable({
            columnDefs: [
                { className: "text-center", "targets": [0] }
            ],
            dom: 'Bfrtip',

            buttons: [
                {
                    text: 'Add ID',
                    action: function (e, dt, node, config) {
                        $('#addId').modal('show');
                    }

                }
            ]

        });


        $('#discount_table tbody').on('click', 'tr', function () {



            var row = table.row($(this)).data();

            if ($(this).hasClass('selected')) {
                if (isDiscount) {
                    $('#grand_total').text(parseFloat(discount_price(false)).toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }))
                    isDiscount = false;
                }

                $(this).removeClass('selected');
                $('#discount_total').text('')
                $('#show_idNum').val('')
                $('#show_idName').val('')
            }
            else {


                table.$('tr.selected').removeClass('selected'); $(this).addClass('selected');
                if (!isDiscount) {
                    $('#grand_total').text(parseFloat(discount_price(true)).toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }))
                    isDiscount = true;
                }

                $('#discount_total').text(row[4])

                $('#show_idNum').val(row[1])
                $('#show_idName').val(row[2])




                // $('#grand_total').text(parseFloat(gt).toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }))
            }
            //   alert(row[1]);   //EmployeeId

        });



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
                
                        // table.row.add([Number(last_row[0]) + 1, new_id, new_name, new_address, new_idType])
                    
                        $("#add_id_frm")[0].reset();
                        // Optionally reload the page

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





    });

    $(function () {




        let Is_void = false;

        // cattegory arrow 
        // is_overflow();

        //  Draggable scroll Cattegory
        const slider = document.querySelector('#nav-tab');
        let mouseDown = false;
        let startX, scrollLeft;

        let startDragging = function (e) {
            mouseDown = true;
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        };
        let stopDragging = function (event) {
            mouseDown = false;
        };

        slider.addEventListener('mousemove', (e) => {
            e.preventDefault();
            if (!mouseDown) { return; }
            const x = e.pageX - slider.offsetLeft;
            const scroll = x - startX;
            slider.scrollLeft = scrollLeft - scroll;
        });

        // Add the event listeners
        slider.addEventListener('mousedown', startDragging, false);
        slider.addEventListener('mouseup', stopDragging, false);
        slider.addEventListener('mouseleave', stopDragging, false);

        // end Draggable scroll Cattegory



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
                url: _base_url_ + "admin/sales/var_modal.php",
                type: "post",
                data: { id: id },
                success: function (data) {
                    $('#variants_modal_body').html(data);
                }

            });
        });

        $('.item-btn').click(function () {
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
                $('#VoidModal').modal('show');
                // if (confirm("Are you sure to remove this item from list?") == true) {
                //     item.remove()
                //     calc_total()
                // }
                $('#void_orders').attr("id", "void_order");
                $('#void_order').submit(function (e) {
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
                                item.remove()


                                if ($('#order-items-body .product-item').length <= 0) {
                                    $('#discount_table').DataTable().$('tr.selected').removeClass('selected');
                                    $('#show_idName').val('');
                                    $("#show_idNum").val('');
                                    $("#discount_total").text('');
                                    isDiscount = false;
                                    $(".product-item").remove();
                                    $("#grand_total").text("0.00");
                                    $("#change").text("0.00");
                                    $('input[name="tendered_amount"]').val(0)
                                    $('#menu-note').val('');
                                    $('#VoidModal').modal('toggle');

                                };
                                calc_total()
                                $("#void_order")[0].reset();
                                $('#VoidModal').modal('toggle');
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
            })

        })

        $('.clear_order').click(function () {
            // if walang item
            if ($('#order-items-body .product-item').length <= 0) {
                alert_toast("Please Add atleast 1 Item First.", "warning")
                return false;
            }
            $('#VoidModal').modal('show');
            $('#void_order').attr("id", "void_orders");
            $('#void_orders').submit(function (e) {
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

                            $('#discount_table').DataTable().$('tr.selected').removeClass('selected');
                            $('#show_idName').val('');
                            $("#show_idNum").val('');
                            $("#discount_total").text('');
                            isDiscount = false;
                            alert_toast(resp.msg, 'success');
                            $("#void_orders")[0].reset();
                            $(".product-item").remove();
                            $("#grand_total").text("0.00");
                            $("#change").text("0.00");
                            $('input[name="tendered_amount"]').val(0)
                            $('#menu-note').val('');
                            $('#VoidModal').modal('toggle');

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
                url: _base_url_ + "classes/Master.php?f=place_order",
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
                        window.open(_base_url_ + "admin/sales/receipt.php?id=" + resp.oid + '&discount=' + isDiscount, '_self')

                    } else if (!!resp.msg) {
                        alert_toast(resp.msg, 'error')
                    } else {
                        alert_toast(resp.msg, 'error')
                    }
                    end_loader()
                }
            })
        });



        $('#add_note').click(function () {

            if ($('#order-items-body .product-item').length <= 0) {
                alert_toast("Please Add atleast 1 Item First.", "warning")
                return false;
            }
            $('#NotelModal').modal('show')
        })
        $('#add_discount').click(function () {

            if ($('#order-items-body .product-item').length <= 0) {
                alert_toast("Please Add atleast 1 Item First.", "warning")
                return false;
            }
            $('#DiscountModal').modal('show')
        })

        
    });

</script>