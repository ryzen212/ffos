<?php
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date("Y-m-d");
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date("Y-m-d");
$type = isset($_GET['type']) ? $_GET['type'] : '';


switch ($type) {
    case 'Daily':
        $stock = $conn->query("SELECT DATE_FORMAT(date_created, '%W %M %e %Y') as date_created ,SUM(quantity) as quantity, SUM(total_amount) AS total_amount,user_id
FROM `order_list` as ol LEFT JOIN `order_items` as ot ON ol.id = ot.order_id where (date(date_created) BETWEEN '$from_date' AND '$to_date')  and status = 1   and delete_flag != 1 
GROUP BY YEAR(date_created), MONTH(date_created), DAY(date_created);");
        break;
    case 'Weekly':
        $stock = $conn->query("SELECT 
SUM(total_amount) AS total_amount,SUM(quantity) AS quantity, 
CONCAT(STR_TO_DATE(CONCAT(YEARWEEK(date_created, 2), ' Sunday'), '%X%V %W'),' ',STR_TO_DATE(CONCAT(YEARWEEK(date_created, 2), ' Sunday'), '%X%V %W %m') + INTERVAL 6 DAY
) AS date_created FROM `order_list` as ol LEFT JOIN `order_items` as ot ON ol.id = ot.order_id where (date(date_created) BETWEEN '$from_date' AND '$to_date')  and status = 1   and delete_flag != 1  GROUP BY YEARWEEK(date_created, 2) ORDER BY YEARWEEK(date_created, 2);");
        break;
    case 'Monthly':
        $stock = $conn->query("SELECT MONTHNAME(date_created) as date_created, SUM(total_amount) as total_amount,SUM(quantity) as quantity FROM `order_list` as ol LEFT JOIN `order_items` as ot ON ol.id = ot.order_id where (date(date_created) BETWEEN '$from_date' AND '$to_date')  and status = 1   and delete_flag != 1  GROUP BY YEAR(date_created), MONTH(date_created)");
        break;



    default:
        $stock = $conn->query("SELECT DATE_FORMAT(date_created, '%W %M %e %Y') as date_created ,SUM(quantity) as quantity, SUM(total_amount) AS total_amount,user_id
    FROM `order_list` as ol LEFT JOIN `order_items` as ot ON ol.id = ot.order_id where (date(date_created) BETWEEN '$from_date' AND '$to_date')  and status = 1   and delete_flag != 1 
    GROUP BY YEAR(date_created), MONTH(date_created), DAY(date_created);");
        break;
}

?>

<style>
    * {
        margin: 0;
        padding: 0;
        font-family: sans-serif;
    }

    .chartMenu {
        width: 100vw;
        height: 40px;
        background: #1A1A1A;
        color: #ffc107;
    }

    .chartMenu p {
        padding: 10px;
        font-size: 20px;
    }

    .chartCard {
        width: 100%;
        height: calc(100vh - 40px);
        background: rgba(54, 162, 235, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chartBox {
        width: 100%;
        padding: 20px;
        border-radius: 20px;
        border: solid 3px #ffc107;
        background: white;
    }
</style>


<div class="content py-2 text-light px-3 bg-gradient-warning">
    <h2>Sales Reports</h2>
</div>
<div class="row flex-column mt-4 justify-content-center align-items-center ">
    <div class="col-lg-11 col-md-11 col-sm-12 col-xs-12">
        <div class="card rounded-0 mb-2 shadow">
            <div class="card-body">
                <fieldset>
                    <legend>Filter</legend>

                    <div class="row ">
                        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                            <form action="" id="filter-form">
                                <div class="form-group">
                                    <label for="date" class="control-label">From Date</label>
                                    <input type="date" id='start_date' class="form-control form-control-sm rounded-0"
                                        name="from_date" id="date" value="<?= $from_date ?>" required="required">
                                </div>

                                <div class="form-group">
                                    <label for="exampleFormControlSelect2">Periodicity</label>
                                    <select class="form-control" name='type'>
                                        <option value="Daily" <?= $type == 'Daily' || empty($total) ? 'selected' : '' ?>>
                                            Daily</option>
                                        <option value="Weekly" <?= $type == 'Weekly' ? 'selected' : '' ?>>Weekly</option>
                                        <option value="Monthly" <?= $type == 'Monthly' ? 'selected' : '' ?>>Monthly
                                        </option>

                                    </select>
                                </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                            <div class="form-group mb-5">
                                <label for="date" class="control-label">To Date</label>
                                <input type="date" id='end_date' class="form-control form-control-sm rounded-0"
                                    name="to_date" id="date" value="<?= $to_date ?>" required="required">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-sm btn-flat btn-primary bg-gradient-primary"><i
                                        class="fa fa-filter"></i> Filter</button>
                            </div>
                        </div>
                        </form>
                    </div>

                </fieldset>
            </div>
        </div>
    </div>
    <div class="col-lg-11 col-md-11 col-sm-12 col-xs-12">
        <div class="card rounded-0 mb-2 shadow">
            <div class="card-header py-1">
                <div class="card-tools">
                    <!-- <button class="btn btn-flat btn-sm btn-light bg-gradient-light border text-dark" type="button"
                        id="print"><i class="fa fa-print"></i> Print</button> -->
                </div>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <div class="d-flex justify-content-center">
                        <h4>
                            <?= $type . ' Sales' ?>
                        </h4>
                    </div>

                    <table class="table table-bordered">

                        <thead>
                            <tr>
                                <th class="px-1 py-1 text-center">#</th>
                                <th class="px-1 py-1 text-center">Date</th>
                                <th class="px-1 py-1 text-center">Quantity</th>
                                <th class="px-1 py-1 text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $g_total = 0;
                            $i = 1;
                            // $stock = $conn->query("SELECT * FROM `order_list` where (date(date_created) BETWEEN '$from_date' AND '$to_date')  and status = 1  order by abs(unix_timestamp(date_created)) asc");
                            while ($row = $stock->fetch_assoc()):
                                if ($type == 'Weekly') {
                                    $date_created = explode(" ", $row['date_created']);
                                    $date_created[0] = date_create($date_created[0]);
                                    $date_created[1] = date_create($date_created[1]);
                                    $date_created[0] = date_format($date_created[0], "F d");
                                    $date_created[1] = date_format($date_created[1], "d Y");

                                    $row['date_created'] = $date_created[0] . '-' . $date_created[1];

                                }
                                $g_total += $row['total_amount'];
                                ?>
                                <tr>
                                    <td class="px-1 py-1 align-middle text-center">
                                        <?= $i++ ?>
                                    </td>
                                    <td class="px-1 py-1 align-middle">
                                        <?= $row['date_created'] ?>
                                    </td>

                                    <td class="px-1 py-1 align-middle text-center">
                                        <?= $row['quantity'] ?>
                                    </td>
                                    <td class="px-1 py-1 align-middle text-center">
                                        <?= '₱' . $row['total_amount'] ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if ($stock->num_rows <= 0): ?>
                                <tr>
                                    <td class="py-1 text-center" colspan="6">No records found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-center">Total Sales</th>
                                <th class="text-right">
                                    <?= '₱' . format_num($g_total, 2) ?>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="chartBox mb-2">
                    <canvas id="myChart"></canvas>
                </div>
                <div class="chartBox">
                    <canvas id="qtyChart"></canvas>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- <noscript id="print-header">
    <div>
        <style>
            html {
                min-height: unset !important;
            }
        </style>
        <div class="d-flex w-100 align-items-center">
            <div class="col-2 text-center">
                <img src="<?= validate_image($_settings->info('logo')) ?>" alt="" class="rounded-circle border"
                    style="width: 5em;height: 5em;object-fit:cover;object-position:center center">
            </div>
            <div class="col-8">
                <div style="line-height:1em">
                    <div class="text-center font-weight-bold h5 mb-0">
                        <large>
                            <?= $_settings->info('name') ?>
                        </large>
                    </div>
                    <div class="text-center font-weight-bold h5 mb-0">
                        <large>Sales Report</large>
                    </div>
                    <div class="text-center font-weight-bold h5 mb-0">as of
                        <?= date("F d, Y", strtotime($date)) ?>
                    </div>
                </div>
            </div>


        </div>
        <hr>

    </div>

</noscript> -->
<script type="text/javascript" src="http://localhost/ffos/plugins/chartjs/chart.umd.min.js"></script>

<script
    src="http://localhost/ffos/plugins/chartjs/chartjs-adapter-date-fns.bundle.min.js"></script>
<script>
    // setup 
    load_chart()
    function load_chart() {
        <?php
        //    echo "SELECT MONTHNAME(date_created) as date_created, SUM(total_amount) as total_amount,SUM(quantity) as quantity FROM `order_list` as ol LEFT JOIN `order_items` as ot ON ol.id = ot.order_id where (date(date_created) BETWEEN '$from_date' AND '$to_date')  and status = 1   and delete_flag != 1  GROUP BY YEAR(date_created), MONTH(date_created)";
        //    exit;  
        
        switch ($type) {
            case 'Daily':
                $stock = $conn->query("SELECT DATE_FORMAT(date_created, '%W %M %e %Y') as date_created ,SUM(quantity) AS quantity, SUM(total_amount) AS total_amount,user_id  FROM `order_list` as ol LEFT JOIN `order_items` as ot ON ol.id = ot.order_id where (date(date_created) BETWEEN '$from_date' AND '$to_date')  and status = 1   and delete_flag != 1   GROUP BY YEAR(date_created), MONTH(date_created), DAY(date_created);");
                break;
            case 'Weekly':
                $stock = $conn->query("SELECT SUM(total_amount) AS total_amount,SUM(quantity) AS quantity, CONCAT(STR_TO_DATE(CONCAT(YEARWEEK(date_created, 2), ' Sunday'), '%X%V %W'),' ',STR_TO_DATE(CONCAT(YEARWEEK(date_created, 2), ' Sunday'), '%X%V %W %m') + INTERVAL 6 DAY ) AS date_created FROM `order_list` as ol LEFT JOIN `order_items` as ot ON ol.id = ot.order_id where (date(date_created) BETWEEN '$from_date' AND '$to_date')  and status = 1   and delete_flag != 1  GROUP BY YEARWEEK(date_created, 2) ORDER BY YEARWEEK(date_created, 2);");
                break;
            case 'Monthly':
                $stock = $conn->query("SELECT MONTHNAME(date_created) as date_created, SUM(total_amount) as total_amount,SUM(quantity) as quantity FROM `order_list` as ol LEFT JOIN `order_items` as ot ON ol.id = ot.order_id where (date(date_created) BETWEEN '$from_date' AND '$to_date')  and status = 1   and delete_flag != 1  GROUP BY YEAR(date_created), MONTH(date_created)");
                break;
            default:
                $stock = $conn->query("SELECT DATE_FORMAT(date_created, '%W %M %e %Y') as date_created ,SUM(quantity) AS quantity, SUM(total_amount) AS total_amount,user_id  FROM `order_list` as ol LEFT JOIN `order_items` as ot ON ol.id = ot.order_id where (date(date_created) BETWEEN '$from_date' AND '$to_date')  and status = 1   and delete_flag != 1   GROUP BY YEAR(date_created), MONTH(date_created), DAY(date_created);");
                break;
        }

        ?>
        const sales = [<?php
        while ($row = $stock->fetch_assoc()) {
            if ($type == 'Weekly') {
                $date_created = explode(" ", $row['date_created']);
                $date_created[0] = date_create($date_created[0]);
                $date_created[1] = date_create($date_created[1]);
                $date_created[0] = date_format($date_created[0], "F d");
                $date_created[1] = date_format($date_created[1], "d Y");
                $row['date_created'] = $date_created[0] . '-' . $date_created[1];
            } ?>
                                                                                                                                        {
                    x: '<?= $row['date_created'] ?>', y: Number(<?= $row['total_amount'] ?>).toFixed(2)
                },


            <?php }
        ?>
        ];

        const qty = [<?php
        switch ($type) {
            case 'Daily':
                $stock = $conn->query("SELECT DATE_FORMAT(date_created, '%W %M %e %Y') as date_created ,SUM(quantity) AS quantity, SUM(total_amount) AS total_amount,user_id  FROM `order_list` as ol LEFT JOIN `order_items` as ot ON ol.id = ot.order_id where (date(date_created) BETWEEN '$from_date' AND '$to_date')  and status = 1  and delete_flag != 1 GROUP BY YEAR(date_created), MONTH(date_created), DAY(date_created);");
                break;
            case 'Weekly':
                $stock = $conn->query("SELECT SUM(total_amount) AS total_amount,SUM(quantity) AS quantity, CONCAT(STR_TO_DATE(CONCAT(YEARWEEK(date_created, 2), ' Sunday'), '%X%V %W'),' ',STR_TO_DATE(CONCAT(YEARWEEK(date_created, 2), ' Sunday'), '%X%V %W %m') + INTERVAL 6 DAY ) AS date_created FROM `order_list` as ol LEFT JOIN `order_items` as ot ON ol.id = ot.order_id where (date(date_created) BETWEEN '$from_date' AND '$to_date')  and status = 1   and delete_flag != 1  GROUP BY YEARWEEK(date_created, 2) ORDER BY YEARWEEK(date_created, 2);");
                break;
            case 'Monthly':
                $stock = $conn->query("SELECT MONTHNAME(date_created) as date_created, SUM(total_amount) as total_amount,SUM(quantity) as quantity FROM `order_list` as ol LEFT JOIN `order_items` as ot ON ol.id = ot.order_id where (date(date_created) BETWEEN '$from_date' AND '$to_date')  and status = 1   and delete_flag != 1  GROUP BY YEAR(date_created), MONTH(date_created)");
                break;
            default:
                $stock = $conn->query("SELECT DATE_FORMAT(date_created, '%W %M %e %Y') as date_created ,SUM(quantity) AS quantity, SUM(total_amount) AS total_amount,user_id  FROM `order_list` as ol LEFT JOIN `order_items` as ot ON ol.id = ot.order_id where (date(date_created) BETWEEN '$from_date' AND '$to_date')  and status = 1   and delete_flag != 1   GROUP BY YEAR(date_created), MONTH(date_created), DAY(date_created);");
                break;
        }


        while ($row = $stock->fetch_assoc()) {
            if ($type == 'Weekly') {
                $date_created = explode(" ", $row['date_created']);
                $date_created[0] = date_create($date_created[0]);
                $date_created[1] = date_create($date_created[1]);
                $date_created[0] = date_format($date_created[0], "F d");
                $date_created[1] = date_format($date_created[1], "d Y");
                $row['date_created'] = $date_created[0] . '-' . $date_created[1];
            } ?>
                                                                                                                                        {
                    x: '<?= $row['date_created'] ?>', y: Number(<?= $row['quantity'] ?>).toFixed(2)
                },


            <?php }
        ?>
        ];
        const data = {
            // labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Total Sales',
                data: sales,
                backgroundColor: [
                    'rgba(255, 26, 104, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(0, 0, 0, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 26, 104, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(0, 0, 0, 1)'
                ],
                borderWidth: 1
            }]
        };
        const data_qty = {
            // labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Total Product Sales',
                data: qty,
                backgroundColor: [
                    'rgba(255, 26, 104, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(0, 0, 0, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 26, 104, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(0, 0, 0, 1)'
                ],
                borderWidth: 1
            }]
        };

        // config 
        const config = {
            type: 'line',
            data,
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: '<?= $type . 'Sales Report' ?>'
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {

                    y: {
                        beginAtZero: true
                    }
                }
            },

        };

        const config_qty = {
            type: 'line',
            data: data_qty,
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: '<?= $type . ' Product Sales Report' ?>'
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {

                    y: {
                        beginAtZero: true
                    }
                }
            },

        };

        // render init block
        const myChart = new Chart(
            document.getElementById('myChart'),
            config
        );
        const qtyChart = new Chart(
            document.getElementById('qtyChart'),
            config_qty
        );

        // Instantly assign Chart.js version
        const chartVersion = document.getElementById('chartVersion');
        chartVersion.innerText = Chart.version;
    }
</script>
<script>
    function print_r() {
        var h = $('head').clone()
        var el = $('#printout').clone()
        var ph = $($('noscript#print-header').html()).clone()
        h.find('title').text("Daily Sales Report - Print View")
        var nw = window.open("", "_blank", "width=" + ($(window).width() * .8) + ",left=" + ($(window).width() * .1) + ",height=" + ($(window).height() * .8) + ",top=" + ($(window).height() * .1))
        nw.document.querySelector('head').innerHTML = h.html()
        nw.document.querySelector('body').innerHTML = ph[0].outerHTML
        nw.document.querySelector('body').innerHTML += el[0].outerHTML
        nw.document.close()
        start_loader()
        setTimeout(() => {
            nw.print()
            setTimeout(() => {
                nw.close()
                end_loader()
            }, 200);
        }, 300);
    }
    $(function () {
        $('#filter-form').submit(function (e) {
            e.preventDefault()
            location.href = './?page=reports&' + $(this).serialize()
        })
        $('#print').click(function () {
            print_r()
        })

    })

    //   date validation
    var start = document.getElementById('start_date');
    var end = document.getElementById('end_date');

    start.addEventListener('change', function () {
        if (start.value > end.value) {

            end.value = start.value;

        }
        end.min = start.value;

    }, false);

    end.addEventListener('change', function () {
        if (end.value < start.value) {

            end.value = start.value;
        }
        // if (end.value) {
        //     start.max = end.value;
        // }
    }, false);
</script>