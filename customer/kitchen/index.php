<style>
    #order-field {
        height: 54em;
        overflow: auto;
    }

    .order-list {
        height: 18em;
        overflow: auto;
        position: relative;
    }

    .order-list-header {
        position: sticky;
        top: 0;
        z-index: 2 !important;
    }

    .order-body {
        position: relative;
        z-index: 1 !important;
    }

    #order-field:empty {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #order-field:empty:after {
        content: "No order has been queued yet.";
        color: #b7b4b4;
        font-size: 1.7em;
        font-style: italic;
    }


    #que_tab.active {
        background: #dc3545 !important;
        color: #fff !important;
    }

    #prepare_tab.active {
        background: #17a2b8 !important;
        color: #fff !important;
    }

    #serve_tab.active {
        background: #28a745 !important;
        color: #fff !important;
    }

    .quebtn {
        background: #dc3545 !important;
        color: #fff !important;
    }

    .prebtn {
        background: #17a2b8 !important;
        color: #fff !important;
    }

    .serbtn {
        background: #28a745 !important;
        color: #fff !important;
    }

    .quecard {
        border-top: 3px solid #dc3545 ;
    }

    .precard {
        border-top: 3px solid #17a2b8;
    }

    .sercard {
        border-top: 3px solid #28a745;
    }



    .nav-link {
        color: black
    }
</style>
<div class="content bg-gradient-warning py-3 px-4">
    <h3 class="font-weight-bolder text-light">Orders</h3>
</div>


<div class="row mt-1 justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

        <div class="card rounded-0">
            <div class="card-body">
                <ul class="nav nav-tabs nav-pills nav-fill" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="que_tab" data-toggle="tab" href="#order_que" role="tab"
                            aria-controls="order_que" aria-selected="true">
                            <h5>Queuing <span id='badge-que' class="badge badge-danger"></span></h5>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="prepare_tab" data-toggle="tab" href="#order_prepare" role="tab"
                            aria-controls="order_prepare" aria-selected="false">
                            <h5>Prepare <span id='badge-prepare' class="badge badge-danger"></span></h5>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="serve_tab" data-toggle="tab" href="#order_serve" role="tab"
                            aria-controls="order_serve" aria-selected="false">
                            <h5>Serve <span id='badge-serve' class="badge badge-danger"></span></h5>
                        </a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent" style="height:48em;overflow:auto;">
                    <div class="tab-pane fade show active" id="order_que" role="tabpanel" aria-labelledby="que_tab">
                        <div id="orderQue-field" class="row row-cols-lg-3 rol-cols-md-2 row-cols-sm-1 gx-2 py-1"></div>
                    </div>
                    <div class="tab-pane fade" id="order_prepare" role="tabpanel" aria-labelledby="prepare-tab">
                        <div id="orderPrepare-field" class="row row-cols-lg-3 rol-cols-md-2 row-cols-sm-1 gx-2 py-1">
                        </div>
                    </div>

                    <div class="tab-pane fade" id="order_serve" role="tabpanel" aria-labelledby="serve_tab">
                        <div id="orderServe-field" class="row row-cols-lg-3 rol-cols-md-2 row-cols-sm-1 gx-2 py-1">
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
<noscript id="order-clone">
    <div class="col order-item">
        <div class="card rounded-0 shadow card-outline">
            <div class="card-header py-1">
                <div class="card-title"><b>Queue Code: 10001</b></div>
            </div>
            <div class="card-header py-1">
                <div class="menu_note row"><b>Note:</b></div>
            </div>
            <div class="card-body">
                <div class="order-list">
                    <div class="d-flex w-100 order-list-header">
                        <div class="col-6 m-0 border"><b>Product</b></div>
                        <div class="col-3 m-0 border text-center">QTY</div>
                        <div class="col-3 m-0 border text-center">Price</div>
                    </div>
                    <div class="order-body">
                    </div>
                </div>
            </div>
           
        </div>
    </div>
</noscript>
<script>
    var que_count = 1;

    function get_customer_order() {
        var $id = '';

        listed = []
        $('.order-item').each(function () {
            listed.push($(this).attr('data-id'))

        })
        var tab = ''
        var process = ''
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=get_customer_order",
            method: 'POST',
            data: { listed: listed },
            dataType: 'json',
            error: err => {
                console.log(err)
                alert_toast("An error occurred", "error")
            },
            success: function (resp) {

                $('.order-item[data-id="' + resp.deleted + '"]').remove();

                // $('.order-item[data-id="' + resp.preparing + '"]').remove();

                if (resp.status == 'success') {
                    $('#badge-que').text(resp.count_que);
                    $('#badge-prepare').text(resp.count_prepare);
                    $('#badge-serve').text(resp.count_serve);


                    Object.keys(resp.data).map(k => {

                        var data = resp.data[k]
                        console.log(data);


                        var card = $($('noscript#order-clone').html()).clone()
                        card.attr('data-id', data.id)

                        if (data.status == 2) {
                            tab = '#orderQue-field';
                       
                            process = 'Prepare';
                   
                            card.find('.order-list-header').addClass("quebtn");
                            card.find('.card-outline').addClass("quecard");


                        }
                        if (data.status == 3) {
                            tab = '#orderPrepare-field';
                  
                            process = 'Serve';
                    
                            card.find('.order-list-header').removeClass("quebtn").addClass("prebtn");
                            card.find('.card-outline').removeClass("quecard").addClass("precard");
                        }
                        if (data.status == 4) {
                            tab = '#orderServe-field';
                         
                            process = 'Collect';
                  
                            card.find('.order-list-header').removeClass("prebtn").addClass("serbtn");
                            card.find('.card-outline').removeClass("precard").addClass("sercard");
                        }
                        if (resp.item_status == 2) {
                            tab = '#orderQue-field';
               
                            process = 'Prepare';
                       
                            card.find('.order-list-header').addClass("quebtn");
                            card.find('.card-outline').addClass("quecard");
                        }
                        if (resp.item_status == 3) {
                            tab = '#orderPrepare-field';
                    
                            process = 'Serve';
                       
                            card.find('.order-list-header').removeClass("quebtn").addClass("prebtn");
                            card.find('.card-outline').removeClass("quecard").addClass("precard");
                        }
                        if (resp.item_status == 4) {
                            tab = '#orderServe-field';
                    
                            process = 'Collect';
                         
                            card.find('.order-list-header').removeClass("quebtn").addClass("prebtn");
                            card.find('.card-outline').removeClass("precard").addClass("sercard");
                        }


                        if (data.menu_note != null) {
                            data.menu_note = data.menu_note.replace(/\n/g, "<br />");
                        } else {
                            data.menu_note = '';
                        }



                        card.find('.card-title').text('Queue #' + data.queue)
                        card.find('.menu_note').html('<div class="col-xl-2 col-l-4 col-md-12"><b>Note</b>:</div><div class="col-xl-10 col-l-8 col-md-12">' + data.menu_note + '</div>');
                        Object.keys(data.item_arr).map(i => {
                            var row = card.find('.order-list-header').clone().removeClass('order-list-header quebtn serbtn prebtn')
                            row.find('div').first().text(data.item_arr[i].item + data.item_arr[i].var_price)
                            row.find('div').eq(1).text(parseInt(data.item_arr[i].quantity).toLocaleString())
                            row.find('div').last().text('₱'+parseInt(data.item_arr[i].price).toLocaleString())
                     
                            card.find('.order-body').append(row)
                        })
                        console.log(data)
                        $(tab).append(card)

                        card.find('.order-served').click(function () {
                            _conf("Are you sure to " + process + " <b>Queue #: " + data.queue + "</b>?", 'serve_order', [data.id, data.status])
                        })
                    })
                }
            }

        })



    }


    $(function () {
        $('body').addClass('sidebar-collapse')
        var load_data = setInterval(() => {
            get_customer_order()
        }, 500);
    })
    function serve_order($id, $order_status) {

        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=serve_order",
            method: "POST",
            data: {
                id: $id,
                order_status: $order_status
            },
            dataType: "json",
            error: err => {
                console.log(err)
                alert_toast("An error occured.", 'error');
                end_loader();
            },
            success: function (resp) {
                if (typeof resp == 'object' && resp.status == 'success') {
                    $('.modal').modal('hide')
                    alert_toast("Order has been served.", 'success');
                    $('.order-item[data-id="' + $id + '"]').remove()
                } else {
                    alert_toast("An error occured.", 'error');
                }
                end_loader();
            }
        })
    }
</script>