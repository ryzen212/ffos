<?php
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date("Y-01-01");
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date("Y-12-31");
$type = isset($_GET['type']) ? $_GET['type'] : 'All';


switch ($type) {
    case 'All':
        $stock = $conn->query("SELECT DATE_FORMAT(date_created, '%b %e, %Y %h:%i %p') as date_created ,ot.order_id,SUM(quantity) as quantity,code,total_amount
        FROM `order_list` as ol LEFT JOIN `order_items` as ot ON ol.id = ot.order_id where (date(date_created) BETWEEN '$from_date' AND '$to_date')  and  status = 1 and delete_flag != 1 GROUP BY code ORDER BY date_created ASC ");

        $b_seller = $conn->query("SELECT m.name ,m.id,ot.variants,sum(ot.price*ot.quantity) as sales,ot.price,DATE_FORMAT(ol.date_created, '%b %e, %Y')as date_created,ol.code,sum(ot.quantity)as quantity,m.var_price from `menu_list` m INNER join order_items ot on m.id = ot.menu_id LEFT JOIN `order_list` as ol ON ol.id = ot.order_id where m.delete_flag = 0 and ol.status = 1 and (date(ol.date_created) BETWEEN '$from_date' AND '$to_date')  GROUP BY ot.menu_id,ot.variants 
        ORDER BY quantity DESC,sales DESC,date_created ASC;");

        break;
    case 'Daily':
        $stock = $conn->query("SELECT 
        final_order_date AS order_date,
        MAX(date_created) as date_created,
        SUM(quantity) as quantity,
        SUM(total_amount) AS total_amount,
        user_id
    FROM (
        SELECT 
            DATE_FORMAT(ol.date_created, '%Y-%m-%d') as final_order_date,
            DATE_FORMAT(ol.date_created, '%b %e, %Y') as date_created,
            SUM(quantity) as quantity,
           total_amount,
            user_id
        FROM `order_list` as ol 
        LEFT JOIN `order_items` as ot ON ol.id = ot.order_id 
        WHERE (date(ol.date_created) BETWEEN '$from_date' AND '$to_date') 
            AND ol.status = 1 
            AND ol.delete_flag != 1 
        GROUP BY ol.code
    ) AS first_level_grouping
    GROUP BY final_order_date
    ORDER BY order_date DESC;");

        $b_seller = $conn->query("SELECT m.name ,m.id,ot.variants,sum(ot.price*ot.quantity) as sales,ot.price,DATE_FORMAT(ol.date_created, '%b %e, %Y') as date_created,ol.code,sum(ot.quantity) as quantity,m.var_price from `menu_list` m INNER join order_items ot on m.id = ot.menu_id LEFT JOIN `order_list` as ol ON ol.id = ot.order_id where m.delete_flag = 0 and ol.status = 1 and (date(ol.date_created) BETWEEN '$from_date' AND '$to_date') GROUP BY ot.menu_id,ot.variants,date_created 
            ORDER BY date_created ASC,quantity DESC,sales DESC;");
        break;
    case 'Weekly':
        $stock = $conn->query("SELECT
        CONCAT(STR_TO_DATE(CONCAT(YEARWEEK(date_created1, 2), ' Sunday'), '%X%V %W'),' ',STR_TO_DATE(CONCAT(YEARWEEK(date_created1, 2), ' Sunday'), '%X%V %W %m') + INTERVAL 6 DAY
      ) AS date_created,
          SUM(total_amount) AS total_amount,
          SUM(quantity) AS quantity
      FROM (
          SELECT 
              order_id,
              DATE(date_created) AS date_created1,
              total_amount AS total_amount,
              SUM(quantity) AS quantity
          FROM `order_list` AS ol
          LEFT JOIN `order_items` AS ot ON ol.id = ot.order_id
          WHERE DATE(date_created) BETWEEN '$from_date' AND '$to_date'
              AND status = 1
              AND delete_flag != 1
          GROUP BY order_id, DATE(date_created)
      ) AS subquery
      GROUP BY YEARWEEK(date_created1 , 2)
      ORDER BY date_created1 DESC;");



        $b_seller = $conn->query("SELECT 
         m.name,
         m.id,
         ot.variants,
         SUM(ot.price * ot.quantity) AS sales,
         ot.price,
         CONCAT(
           DATE_FORMAT(DATE_ADD(ol.date_created, INTERVAL(1-DAYOFWEEK(ol.date_created)) DAY), '%b %d'),
           '-',
           DATE_FORMAT(DATE_ADD(ol.date_created, INTERVAL(7-DAYOFWEEK(ol.date_created)) DAY), '%b %d, %Y')
         ) AS date_created,
         ol.code,
         SUM(ot.quantity) AS quantity,
         m.var_price
     FROM
         `menu_list` m
     INNER JOIN
         order_items ot ON m.id = ot.menu_id
     LEFT JOIN
         `order_list` AS ol ON ol.id = ot.order_id
     WHERE
         m.delete_flag = 0 and ol.status = 1 and (date(ol.date_created) BETWEEN '$from_date' AND '$to_date') 
GROUP BY
         ot.menu_id,
         ot.variants,
         YEARWEEK(ol.date_created, 2)
     ORDER BY
     date_created DESC,	
     quantity DESC,
         sales DESC;");


        break;
    case 'Monthly':
        $stock = $conn->query("SELECT
        CONCAT(DATE_FORMAT(MIN(date_created1), '%b %Y')) AS date_created,
        SUM(total_amount) AS total_amount,
        SUM(quantity) AS quantity
    FROM (
        SELECT 
            order_id,
            DATE(date_created) AS date_created1,
            total_amount AS total_amount,
            SUM(quantity) AS quantity
        FROM `order_list` AS ol
        LEFT JOIN `order_items` AS ot ON ol.id = ot.order_id
        WHERE DATE(date_created) BETWEEN '$from_date' AND '$to_date'
            AND status = 1
            AND delete_flag != 1
        GROUP BY order_id, DATE(date_created)
    ) AS subquery
    GROUP BY DATE_FORMAT(date_created1, '%Y-%m')
    ORDER BY MIN(date_created1) DESC;");

        $b_seller = $conn->query("SELECT m.name ,m.id,ot.variants,sum(ot.price*ot.quantity) as sales,ot.price,DATE_FORMAT(ol.date_created, '%b %Y') as date_created ,ol.code,SUM(ot.quantity) AS quantity,m.var_price from `menu_list` m INNER join order_items ot on m.id = ot.menu_id LEFT JOIN `order_list` as ol ON ol.id = ot.order_id where m.delete_flag = 0  and ol.status = 1 and (date(ol.date_created) BETWEEN '$from_date' AND '$to_date')  GROUP BY ot.menu_id,ot.variants,date_created 
        ORDER BY  date_created ASC ,quantity DESC,sales DESC;");
        break;

    default:
        $stock = $conn->query("SELECT DATE_FORMAT(date_created, '%b %e, %Y %h:%i %p') as date_created ,ot.order_id,SUM(quantity) as quantity,code,total_amount
    FROM `order_list` as ol LEFT JOIN `order_items` as ot ON ol.id = ot.order_id where (date(date_created) BETWEEN '$from_date' AND '$to_date')  and  status = 1 and delete_flag != 1 GROUP BY code ORDER BY date_created ASC ");

        $b_seller = $conn->query("SELECT m.name ,m.id,ot.variants,sum(ot.price*ot.quantity) as sales,ot.price,DATE_FORMAT(ol.date_created, '%b %e, %Y')as date_created,ol.code,sum(ot.quantity)as quantity,m.var_price from `menu_list` m INNER join order_items ot on m.id = ot.menu_id LEFT JOIN `order_list` as ol ON ol.id = ot.order_id where m.delete_flag = 0 and ol.status = 1 and (date(ol.date_created) BETWEEN '$from_date' AND '$to_date')  GROUP BY ot.menu_id,ot.variants 
    ORDER BY quantity DESC,sales DESC,date_created ASC;");

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

    .group {
        background: #ffc107 !important;
        text-align: center !important;
        font-weight: bold !important;
        cursor: pointer;
    }
</style>


<div class="content py-2 text-light px-3 bg-gradient-warning d-flex justify-content-between align-items-center">
    <h2>Sales Reports</h2>
    <form action="<?= base_url ?>admin/reports/finance_report_P.php" method='POST' class="ml-auto">
        <div class="form-group mb-0">
            <!-- <button id='print' type='submit' class="btn  btn-success bg-gradient-primary btn-lg"><i
                    class="fas fa-download"></i> Download Sales Reports</button> -->
        </div>
    </form>
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

                                        <option value="All" <?= $type == 'All' || empty($total) ? 'selected' : '' ?>>
                                            All</option>
                                        <option value="Daily" <?= $type == 'Daily' ? 'selected' : '' ?>>Daily</option>
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
                                <button type='submit' class="btn btn-sm btn-flat btn-primary bg-gradient-primary"><i
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
                    <table class="table table-striped" id="sales_table">

                        <thead>
                            <tr>
                                <?php if ($type == 'All') { ?>
                                    <th class="px-1 py-1 text-center">#</th>
                                    <th class="px-1 py-1 text-center">Date</th>
                                    <th class="px-1 py-1 text-center">Transaction Code</th>
                                    <th class="px-1 py-1 text-center">Items</th>
                                    <th class="px-1 py-1 text-center">Item Qty</th>
                                    <th class="px-1 py-1 text-center">Price</th>

                                <?php } else { ?>
                                    <th class="px-1 py-1 text-center">#</th>
                                    <th class="px-1 py-1 text-center">Date</th>
                                    <th class="px-1 py-1 text-center">Quantity</th>
                                    <th class="px-1 py-1 text-center">Total</th>

                                <?php } ?>
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
                                    $date_created[0] = date_format($date_created[0], "M d");
                                    $date_created[1] = date_format($date_created[1], "M d, Y");

                                    $row['date_created'] = $date_created[0] . '-' . $date_created[1];

                                }
                                $g_total += $row['total_amount'];
                                ?>
                                <?php if ($type == 'All') { ?>
                                    <tr>
                                        <td class="px-1 py-1 align-middle text-center">
                                            <?= $i++ ?>
                                        </td>
                                        <td class="px-1 py-1 align-middle">
                                            <?= $row['date_created'] ?>
                                        </td>

                                        <td class="px-1 py-1 align-middle">
                                            <?= $row['code'] ?>
                                        </td>
                                        <td class="px-1 py-1 align-left">

                                            <?php

                                            $items = $conn->query("SELECT oi.*, m.name as `item`,m.code,m.var_price FROM `order_items` oi inner join `menu_list` m on oi.menu_id = m.id where oi.order_id = '{$row['order_id']}'");
                                            while ($rowItem = $items->fetch_assoc()) {
                                                $rowItem['isVariants'] = false;
                                                $variants = json_decode(stripslashes($rowItem['var_price']), true);
                                                if (count($variants) > 1) {
                                                    $row['isVariants'] = true;
                                                }
                                                foreach ($variants as $values) {
                                                    if ($rowItem['variants'] == $values['row_id']) {
                                                        echo '<li>' . $rowItem['item'] . ' : ' . $values['var_name'] . '</li>';
                                                    }


                                                }

                                                // echo $row['price'];
                                    
                                            }

                                            ?>


                                        </td>
                                        <td class="px-1 py-1 align-middle text-center">
                                            <?= $row['quantity'] ?>
                                        </td>

                                        <td class="px-1 py-1 align-middle text-center">
                                            <?='₱' . $row['total_amount'] ?>
                                        </td>
                                    </tr>
                                <?php } else { ?>
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

                                <?php } ?>

                            <?php endwhile; ?>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-center">Total Sales:
                                    <?= $_SESSION['total_sales'] =  '₱' . format_num($g_total, 2) ?>

                            </tr>
                        </tfoot>
                    </table>
                </div>





            </div>

        </div>



        <div class="card rounded-0 mb-2 shadow">
            <div class="chartBox mb-2">
                <canvas id="myChart"></canvas>
            </div>
        </div>
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
                            <?= $type . ' Best Sellers' ?>
                        </h4>
                    </div>
                    <table class="table table-bordered" id='bseller_table'>

                        <thead>
                            <tr>

                                <th class="px-1 py-1 text-center">#</th>
                                <th class="px-1 py-1 text-center">Date</th>
                                <th class="px-1 py-1 text-center">Item Name</th>
                                <th class="px-1 py-1 text-center">Variants</th>
                                <th class="px-1 py-1 text-center">Price</th>

                                <th class="px-1 py-1 text-center">Total Qty</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $g_total = 0;
                            $i = 1;
                            $date = '';
                            while ($row = $b_seller->fetch_assoc()):

                                ?>
                                <?php if ($type != 'All') {
                                    if ($row['date_created'] != $date) {
                                        $i = 1;
                                    }
                                }
                                $date = $row['date_created']; ?>

                                <tr>
                                    <td class="px-1 py-1 align-middle text-center">
                                        <?= $i++ ?>
                                    </td>
                                    <td class="px-1 py-1 align-middle text-center">
                                        <?= $row['date_created'] ?>
                                    </td>
                                    <td class="px-1 py-1 align-middle">
                                        <?= $row['name'] ?>
                                    </td>
                                    <?php
                                    $jsonarray = json_decode(stripslashes($row['var_price']), true);
                                    foreach ($jsonarray as $values) {
                                        if ($values['row_id'] == $row['variants']) { ?>
                                            <td class="px-1 py-1 align-middle">
                                                <?= $values['var_name'] ?>
                                            </td>

                                            <?php break;
                                        }
                                    }
                                    ?>
                                    <td class="px-1 py-1 align-middle">
                                        <?= $row['price'] ?>
                                    </td>

                                    <td class="px-1 py-1 align-middle">
                                        <?= $row['quantity'] ?>
                                    </td>
                                </tr>


                            <?php endwhile; ?>

                        </tbody>

                    </table>
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
<script type="text/javascript" src="../plugins/chartjs/chart.umd.min.js"></script>

<script src="../plugins/chartjs/chartjs-adapter-date-fns.bundle.min.js"></script>
<script>
    // setup 
    load_chart()
    function load_chart() {
        <?php
        //    echo "SELECT MONTHNAME(date_created) as date_created, SUM(total_amount) as total_amount,SUM(quantity) as quantity FROM `order_list` as ol LEFT JOIN `order_items` as ot ON ol.id = ot.order_id where (date(date_created) BETWEEN '$from_date' AND '$to_date')  and status = 1   and delete_flag != 1  GROUP BY YEAR(date_created), MONTH(date_created)";
        //    exit;  
        
        switch ($type) {
            case 'All':
                $stock = $conn->query("SELECT DATE_FORMAT(date_created, '%b %e, %Y %h:%i %p') as date_created ,ot.order_id,SUM(quantity) as quantity,code,total_amount
                FROM `order_list` as ol LEFT JOIN `order_items` as ot ON ol.id = ot.order_id where (date(date_created) BETWEEN '$from_date' AND '$to_date')  and  status = 1 and delete_flag != 1 GROUP BY code ORDER BY date_created ASC ");


            case 'Daily':
                $stock = $conn->query("SELECT 
                final_order_date AS order_date,
                MAX(date_created) as date_created,
                SUM(quantity) as quantity,
                SUM(total_amount) AS total_amount,
                user_id
            FROM (
                SELECT 
                    DATE_FORMAT(ol.date_created, '%Y-%m-%d') as final_order_date,
                    DATE_FORMAT(ol.date_created, '%b %e, %Y') as date_created,
                    SUM(quantity) as quantity,
                   total_amount,
                    user_id
                FROM `order_list` as ol 
                LEFT JOIN `order_items` as ot ON ol.id = ot.order_id 
                WHERE (date(ol.date_created) BETWEEN '$from_date' AND '$to_date') 
                    AND ol.status = 1 
                    AND ol.delete_flag != 1 
                GROUP BY ol.code
            ) AS first_level_grouping
            GROUP BY final_order_date
            ORDER BY order_date DESC;");

                $b_seller = $conn->query("SELECT m.name ,m.id,ot.variants,sum(ot.price*ot.quantity) as sales,ot.price,DATE_FORMAT(ol.date_created, '%b %e, %Y') as date_created,ol.code,sum(ot.quantity) as quantity,m.var_price from `menu_list` m INNER join order_items ot on m.id = ot.menu_id LEFT JOIN `order_list` as ol ON ol.id = ot.order_id where m.delete_flag = 0 and ol.status = 1 and (date(ol.date_created) BETWEEN '$from_date' AND '$to_date') GROUP BY ot.menu_id,ot.variants,date_created 
                    ORDER BY date_created ASC,quantity DESC,sales DESC;");
                break;
            case 'Weekly':
                $stock = $conn->query("SELECT
                CONCAT(STR_TO_DATE(CONCAT(YEARWEEK(date_created1, 2), ' Sunday'), '%X%V %W'),' ',STR_TO_DATE(CONCAT(YEARWEEK(date_created1, 2), ' Sunday'), '%X%V %W %m') + INTERVAL 6 DAY
              ) AS date_created,
                  SUM(total_amount) AS total_amount,
                  SUM(quantity) AS quantity
              FROM (
                  SELECT 
                  DATE_FORMAT(date_created, '%Y-%m-%d') as final_order_date,
                      order_id,
                      DATE(date_created) AS date_created1,
                      total_amount AS total_amount,
                      SUM(quantity) AS quantity
                  FROM `order_list` AS ol
                  LEFT JOIN `order_items` AS ot ON ol.id = ot.order_id
                  WHERE DATE(date_created) BETWEEN '$from_date' AND '$to_date'
                      AND status = 1
                      AND delete_flag != 1
                  GROUP BY order_id, DATE(date_created)
              ) AS subquery
              GROUP BY YEARWEEK(date_created1 , 2)
              ORDER BY date_created1 ASC;");




                break;
            case 'Monthly':
                $stock = $conn->query("SELECT
                CONCAT(DATE_FORMAT(MIN(date_created1), '%b %Y')) AS date_created,
                SUM(total_amount) AS total_amount,
                SUM(quantity) AS quantity
            FROM (
                SELECT 
                    order_id,
                    DATE(date_created) AS date_created1,
                    total_amount AS total_amount,
                    SUM(quantity) AS quantity
                FROM `order_list` AS ol
                LEFT JOIN `order_items` AS ot ON ol.id = ot.order_id
                WHERE DATE(date_created) BETWEEN '2023-01-01' AND '2023-12-31'
                    AND status = 1
                    AND delete_flag != 1
                GROUP BY order_id, DATE(date_created)
            ) AS subquery
            GROUP BY DATE_FORMAT(date_created1, '%Y-%m')
            ORDER BY MIN(date_created1) DESC;");

                break;

            default:
                $stock = $conn->query("SELECT DATE_FORMAT(date_created, '%b %e, %Y %h:%i %p') as date_created ,ot.order_id,SUM(quantity) as quantity,code,total_amount
            FROM `order_list` as ol LEFT JOIN `order_items` as ot ON ol.id = ot.order_id where (date(date_created) BETWEEN '$from_date' AND '$to_date')  and  status = 1 and delete_flag != 1 GROUP BY code ORDER BY date_created ASC ");

                break;

        }

        ?>
        const sales = [<?php
        while ($row = $stock->fetch_assoc()) {
            if ($type == 'Weekly') {
                $date_created = explode(" ", $row['date_created']);
                $date_created[0] = date_create($date_created[0]);
                $date_created[1] = date_create($date_created[1]);
                $date_created[0] = date_format($date_created[0], "M d");
                $date_created[1] = date_format($date_created[1], "M d, Y");
                $row['date_created'] = $date_created[0] . '-' . $date_created[1];
            } ?>
                                                                                                                                                                                                                    {
                    x: '<?= $row['date_created'] ?>', y: Number(<?= $row['total_amount'] ?>).toFixed(2)
                },


            <?php }
        ?>
        ];

        const qty = [<?php



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



        // render init block
        const myChart = new Chart(
            document.getElementById('myChart'),
            config
        );


        // Instantly assign Chart.js version
        const chartVersion = document.getElementById('chartVersion');
        chartVersion.innerText = Chart.version;
    }
</script>

<script>


    $(document).ready(function () {

        $('#sales_table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'pageLength',
                {
                    extend: 'print',
                    title: '<table style="text-align:left;width:100% margin;">'
                        + '<tr>'
                        + '<th style="width:35%;height:80px;"><img src="<?php echo validate_image($_settings->info('logo')) ?>"  width="80" height="80"></th>'
                        + '<th style="width:65%;height:80px;align:center;"><h4><?php echo $type?> Sales Report </h4></th>'
                        + '</tr>'
                        + '</table><br>'
                        +'<table style="text-align:left;width:100% margin;">'
                        + '<tr>'
                        + '<th style="width:35%;height:80px;"><h5>Total Sales:   <?=  $_SESSION['total_sales'] ?></h5></th>'
                  
                        + '</tr>'
                        + '</table>'
                        ,
                    exportOptions: {
                  
                        modifier: {
                            page: 'current'
                        }
                    },
                    customize: function (win) {
                        $(win.document.body)
                            .css('font-size', '10pt')

                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css({ 'font-size': '10px', 'text-align': 'left', 'width': '100%' });

                    }
                }
            ],

        });







        <?php if ($type == 'Weekly') {
            ?>
            date_order = 'date-range';
        <?php } else { ?>
            date_order = 'date';
        <?php } ?>



        groupColumn = 1;

        <?php if ($type != 'All') { ?>
            var table = $('#bseller_table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                'pageLength',
                {
                    extend: 'print',
                    title: '<table style="text-align:left;width:100% margin;">'
                        + '<tr>'
                        + '<th style="width:35%;height:80px;"><img src="<?php echo validate_image($_settings->info('logo')) ?>"  width="80" height="80"></th>'
                        + '<th style="width:65%;height:80px;align:center;"><h4><?php echo $type?> Sales Report </h4></th>'
                        + '</tr>'
                        + '</table><br>'
                        +'<table style="text-align:left;width:100% margin;">'
                        + '<tr>'
                        + '<th style="width:35%;height:80px;"><h5>Total Sales:   <?=  $_SESSION['total_sales'] ?></h5></th>'
                  
                        + '</tr>'
                        + '</table>'
                        ,
                    exportOptions: {
                  
                        modifier: {
                            page: 'current'
                        }
                    },
                    customize: function (win) {
                        $(win.document.body)
                            .css('font-size', '10pt')

                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css({ 'font-size': '10px', 'text-align': 'left', 'width': '100%' });

                    }
                }
            ],

                columnDefs: [{ visible: false, type: date_order, targets: groupColumn }],
                order: [[groupColumn, 'DESC']],


                drawCallback: function (settings) {
                    var api = this.api();
                    var rows = api.rows({ page: 'current' }).nodes();
                    var last = null;

                    api.column(groupColumn, { page: 'current' })
                        .data()
                        .each(function (group, i) {
                            if (last !== group) {
                                $(rows)
                                    .eq(i)
                                    .before(
                                        '<tr class="group"><td colspan="5">' +
                                        group +
                                        '</td></tr>'
                                    );

                                last = group;
                            }
                        });
                }
            });

            $('#bseller_table tbody').on('click', 'tr.group', function () {
                var currentOrder = table.order()[0];
                if (currentOrder[0] === groupColumn && currentOrder[1] === 'asc') {
                    table.order([groupColumn, 'desc']).draw();
                }
                else {
                    table.order([groupColumn, 'asc']).draw();
                }
            });
        <?php } else { ?>
            $('#bseller_table').DataTable({

                "columnDefs": [
                    {
                        "targets": [1], // Index of the column you want to hide (here, column with index 1)
                        "visible": false, // Set visibility to false to hide the column
                    }
                ],
                dom: 'Bfrtip',
            buttons: [
                'pageLength',
                {
                    extend: 'print',
                    title: '<table style="text-align:left;width:100% margin;">'
                        + '<tr>'
                        + '<th style="width:35%;height:80px;"><img src="<?php echo validate_image($_settings->info('logo')) ?>"  width="80" height="80"></th>'
                        + '<th style="width:65%;height:80px;align:center;"><h4><?php echo $type?> Best Seller</h4></th>'
                        + '</tr>'
                        + '</table>'
                      
                        ,
                    exportOptions: {
                  
                        modifier: {
                            page: 'current'
                        }
                    },
                    customize: function (win) {
                        $(win.document.body)
                            .css('font-size', '10pt')

                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css({ 'font-size': '10px', 'text-align': 'left', 'width': '100%' });

                    }
                }
            ],
            })

        <?php } ?>
    });


    $(function () {
        $('#filter-form').submit(function (e) {
            e.preventDefault()
            location.href = './?page=reports&' + $(this).serialize()
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