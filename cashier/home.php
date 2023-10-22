<?php
$from_date = date("Y-m-01");
$to_date = date("Y-m-t");
$type = '';
?>

<style>
  #system-cover {
    width: 100%;
    height: 45em;
    object-fit: cover;
    object-position: center center;
  }

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

<h1 class="">Welcome,
  <?php echo $_settings->userdata('firstname') . " " . $_settings->userdata('lastname') ?>!
</h1>
<hr>
<div class="row">
  <div class="col-12 col-sm-4 col-md-4">

      <div class="info-box">

        <span class="info-box-icon bg-gradient-light elevation-1"><i class="fas fa-th-list"></i></span>

        <div class="info-box-content">
          <span class="info-box-text">Categories</span>

          <span class="info-box-number text-right h5">
            <?php
            $category = $conn->query("SELECT * FROM category_list where delete_flag = 0 and `status` = 1")->num_rows;
            echo format_num($category);
            ?>
            <?php ?>
          </span>
        </div>

        <!-- /.info-box-content -->
      </div>

    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <div class="col-12 col-sm-4 col-md-4">

      <div class="info-box">
        <span class="info-box-icon bg-gradient-warning elevation-1"><i class="fas fa-hamburger"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Menu</span>
          <span class="info-box-number text-right h5">
            <?php
            $menus = $conn->query("SELECT id FROM menu_list where delete_flag = 0 and `status` = 1")->num_rows;
            echo format_num($menus);
            ?>
            <?php ?>
          </span>
        </div>
        <!-- /.info-box-content -->
      </div>

    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <?php if ($_settings->userdata('type') != 2): ?>
    <div class="col-12 col-sm-4 col-md-4">
      <a href="./?page=orders" class="link-danger" style='color:black;'>
        <div class="info-box">
          <span class="info-box-icon bg-gradient-dark elevation-1"><i class="fas fa-table"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Queued Order</span>
            <span class="info-box-number text-right h5">
              <?php
              $orders = $conn->query("SELECT id FROM order_list where `status` = 0")->num_rows;
              echo format_num($orders);
              ?>
              <?php ?>
            </span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </a>
    </div>
    <!-- /.col -->
  <?php endif; ?>

  <?php if ($_settings->userdata('type') == 1): ?>
    <div class="col-12 col-sm-4 col-md-4">
      <a href="./?page=reports" class="link-danger" style='color:black;'>
        <div class="info-box">
          <span class="info-box-icon bg-gradient-warning elevation-1"><i class="fas fa-th-list"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Total Sales Today</span>
            <span class="info-box-number text-right h5">
              <?php
              $orders = $conn->query("SELECT coalesce(SUM(total_amount),0) FROM order_list where date(`date_created`) = '" . (date('Y-m-d')) . "'")->fetch_array()[0];
              $orders = $orders > 0 ? $orders : 0;
              echo format_num($orders, 2);
              ?>
              <?php ?>
            </span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </a>
    </div>
    <!-- /.col -->
  <?php endif; ?>

  <?php if ($_settings->userdata('type') == 2): ?>
    <div class="col-12 col-sm-4 col-md-4">
      <div class="info-box">
        <span class="info-box-icon bg-gradient-warning elevation-1"><i class="fas fa-th-list"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total Sales Today</span>
          <span class="info-box-number text-right h5">
            <?php
            $orders = $conn->query("SELECT coalesce(SUM(total_amount),0) FROM order_list where date(`date_created`) = '" . (date('Y-m-d')) . "' and user_id = '{$_settings->userdata('id')}'")->fetch_array()[0];
            $orders = $orders > 0 ? $orders : 0;
            echo format_num($orders, 2);
            ?>
            <?php ?>
          </span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
  <?php endif; ?>

</div>
<div class="container-fluid text-center">
  <!-- <img src="<?= validate_image($_settings->info('cover')) ?>" alt="system-cover" id="system-cover" class="img-fluid"> -->
  <div class="chartBox mb-2">
    <canvas id="myChart"></canvas>
  </div>
  <div class="chartBox">
    <canvas id="qtyChart"></canvas>
  </div>
</div>

<script type="text/javascript" src="http://localhost/ffos/plugins/chartjs/chart.umd.min.js"></script>

<script src="http://localhost/ffos/plugins/chartjs/chartjs-adapter-date-fns.bundle.min.js"></script>
<script>
  // setup 
  load_chart()
  function load_chart() {
    <?php

    $stock = $conn->query("SELECT DATE_FORMAT(date_created, '%W %M %e %Y') as date_created ,SUM(quantity) AS quantity, SUM(total_amount) AS total_amount,user_id  FROM `order_list` as ol LEFT JOIN `order_items` as ot ON ol.id = ot.order_id where (date(date_created) BETWEEN '$from_date' AND '$to_date')  and status = 1   and delete_flag != 1   GROUP BY YEAR(date_created), MONTH(date_created), DAY(date_created);");

    ?>
    const sales = [<?php
    while ($row = $stock->fetch_assoc()) {
     ?>
                                                                                                                                  {
          x: '<?= $row['date_created'] ?>', y: Number(<?= $row['total_amount'] ?>).toFixed(2)
        },


      <?php }
    ?>
    ];

    const qty = [<?php


    $stock = $conn->query("SELECT DATE_FORMAT(date_created, '%W %M %e %Y') as date_created ,SUM(quantity) AS quantity, SUM(total_amount) AS total_amount,user_id  FROM `order_list` as ol LEFT JOIN `order_items` as ot ON ol.id = ot.order_id where (date(date_created) BETWEEN '$from_date' AND '$to_date')  and status = 1  and delete_flag != 1 GROUP BY YEAR(date_created), MONTH(date_created), DAY(date_created);");


    while ($row = $stock->fetch_assoc()) {
      ?>
                                                                                                                                  {
          x: '<?= $row['date_created'] ?>', y: Number(<?= $row['quantity'] ?>).toFixed(2)
        },


      <?php }
    ?>
    ];
    const data = {
      // labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
      datasets: [{
        label: 'Total Sales  ₱',
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
            text: '<?= date("Y F") . ' Sales Report' ?>'
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
            text: '<?= date("Y F") . ' Product Sales Report' ?>'
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