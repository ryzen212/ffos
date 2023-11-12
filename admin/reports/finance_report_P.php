<?php

require_once('../../config.php');


function generate_sales($type)
{
  global $conn;


  $contents = '';
  if ($type == 'day') {
    $query = "SELECT 
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
    WHERE ol.status = 1 
        AND ol.delete_flag != 1 
    GROUP BY ol.code
) AS first_level_grouping
GROUP BY final_order_date
ORDER BY order_date DESC;";
  }
  if ($type == 'all') {
    $query = "SELECT   DATE_FORMAT(date_created, '%Y-%m-%d') as final_order_date,DATE_FORMAT(date_created, '%b %e, %Y %h:%i %p') as date_created ,ot.order_id,SUM(quantity) as quantity,code,total_amount
    FROM `order_list` as ol LEFT JOIN `order_items` as ot ON ol.id = ot.order_id where status = 1 and delete_flag != 1 GROUP BY code ORDER BY final_order_date DESC  ";
  }
  if ($type == 'month') {
    $query = "SELECT
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
    WHERE
      status = 1
        AND delete_flag != 1
    GROUP BY order_id, DATE(date_created)
) AS subquery
GROUP BY DATE_FORMAT(date_created1, '%Y-%m')
ORDER BY MIN(date_created1) DESC;";

  }
  if ($type == 'week') {
    $query = "SELECT
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
      WHERE 
           status = 1
          AND delete_flag != 1
      GROUP BY order_id, DATE(date_created)
  ) AS subquery
  GROUP BY YEARWEEK(date_created1 , 2)
  ORDER BY date_created1 DESC";

  }

  $sql = $conn->query($query);

  $i = 1;
  $qty = 0;
  $total_amount = 0;
  while ($row = $sql->fetch_assoc()) {
    $contents .= '<tbody><tr>';
    if ($type == 'all') {
      $contents .= ' <td style="width:10%">' . $i . '</td>';
      $contents .= ' <td style="width:30%">' . $row['date_created'] . '</td>';
      $contents .= ' <td style="width:20%">' . $row['code'] . '</td>';
      $contents .= ' <td style="width:20%">' . $row['quantity'] . '</td>';
      $contents .= ' <td style="width:20%"> ₱' . $row['total_amount'] . '</td>';

    } else {
      $contents .= ' <td style="width:10%">' . $i . '</td>';
      $contents .= ' <td  style="width:40%">' . $row['date_created'] . '</td>';
      $contents .= ' <td style="width:25%">' . $row['quantity'] . '</td>';
      $contents .= ' <td style="width:25%"> ₱' . $row['total_amount'] . '</td>';

    }


    $contents .= "</tr></tbody>";
    $i++;
    $qty = $qty + $row['quantity'];
    $total_amount = $total_amount + $row['total_amount'];
  }
  $contents .= "<tfoot><tr>";


  if ($type == 'all') {
    $contents .= ' <td></td>';
    $contents .= ' <td colspan="2"><b>Total Item QTY: ' . $qty . '</b></td>';
    $contents .= ' <td colspan="2"><b>Total Price: ₱' . $total_amount . '</b></td>';
  } else {
    $contents .= ' <td colspan="2"><b>Total Item QTY: ' . $qty . '</b></td>';
    $contents .= ' <td colspan="2"><b>Total Price: ₱' . $total_amount . '</b></td>';
  }

  $contents .= "</tr></tfoot>";

  return $contents;
}
function generate_best_seller($type)
{
  global $conn;


  $contents = '';
  if ($type == 'all') {
    $query = "SELECT DATE_FORMAT(ol.date_created, '%Y-%m-%d') as final_order_date, m.name ,m.id,ot.variants,SUM(ot.price * ot.quantity) AS sales,ot.price,ol.date_created,ol.code,sum(ot.quantity) as quantity,m.var_price from `menu_list` m INNER join order_items ot on m.id = ot.menu_id LEFT JOIN `order_list` as ol ON ol.id = ot.order_id where ol.delete_flag = 0  and ol.status = 1 GROUP BY ot.menu_id,ot.variants 
    ORDER BY quantity DESC,sales DESC";
  }
  if ($type == 'daily') {
    $query = "SELECT DATE_FORMAT(ol.date_created, '%Y-%m-%d') as final_order_date, m.name ,m.id,ot.variants,sum(ot.price*ot.quantity) as sales,ot.price,DATE_FORMAT(ol.date_created, '%b %e, %Y') as date_created,ol.code,sum(ot.quantity) as quantity,m.var_price from `menu_list` m INNER join order_items ot on m.id = ot.menu_id LEFT JOIN `order_list` as ol ON ol.id = ot.order_id where m.delete_flag = 0 and ol.status = 1 GROUP BY ot.menu_id,ot.variants,date_created 
    ORDER BY final_order_date DESC,quantity DESC,sales DESC;";
  }
  if ($type == 'month') {
    $query = "SELECT m.name ,m.id,ot.variants,sum(ot.price*ot.quantity) as sales,ot.price,DATE_FORMAT(ol.date_created, '%b %Y') as date_created ,ol.code,SUM(ot.quantity) AS quantity,m.var_price from `menu_list` m INNER join order_items ot on m.id = ot.menu_id LEFT JOIN `order_list` as ol ON ol.id = ot.order_id where m.delete_flag = 0  and ol.status = 1 GROUP BY ot.menu_id,ot.variants,date_created 
    ORDER BY  date_created ASC ,quantity DESC,sales DESC;";

  }
  if ($type == "weekly") {
    $query = "SELECT 
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
    m.delete_flag = 0 and ol.status = 1
GROUP BY
    ot.menu_id,
    ot.variants,
    YEARWEEK(ol.date_created, 2)
ORDER BY
date_created DESC,	
quantity DESC,
    sales DESC;";

  }

  $sql = $conn->query($query);

  $i = 1;
  $qty = 0;
  $total_amount = 0;
  $date = '';
  while ($row = $sql->fetch_assoc()) {
    $contents .= '<tbody>';
    if ($type != 'all') {

      if ($row['date_created'] != $date) {
        $contents .= '<tr style="background-color: #ffc107 ;font-weight: bold;"><td colspan="5">' . $row['date_created'] . '</td></tr>';
        $i = 1;
      }

    }
    $date = $row['date_created'];

    $contents .= '<tr><td style="width:15%">' . $i . '</td>';
    $contents .= ' <td style="width:30%">' . $row['name'] . '</td>';
    $jsonarray = json_decode(stripslashes($row['var_price']), true);


    foreach ($jsonarray as $values) {
      if ($values['row_id'] == $row['variants']) {
        $contents .= ' <td style="width:20%">' . $values['var_name'] . '</td>';
        break;
      }
    }


    $contents .= ' <td style="width:20%">₱' . $row['price'] . '</td>';

    $contents .= ' <td style="width:15%"> ' . $row['quantity'] . '</td>';

    $contents .= "</tr></tbody>";
    $i++;

  }

  return $contents;
}
// echo generate_sales();
// exit;

require_once('tcpdf/tcpdf.php');




$pdf = new TCPDF('P', PDF_UNIT, 'LETTER', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle(" Sales Report");

$pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont('helvetica');
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(TRUE, 10);

$pdf->SetFont('dejavusans', '', 10);
$pdf->AddPage();

$toolcopy = '<table style="text-align:left;width:100% margin;">
<tr>
<th style="width:30%;height:80px;"><img src="' . validate_image($_settings->info('logo')) . '"  width="80" height="80"></th>
<th style="width:70%;height:80px;"><br><h1>' . $_settings->info('name') . '</h1></th>
</tr>
</table>';



$content = '';
$content = '
    
    
      	<h2 align="center">Sales Report</h2>
   
      	<table border="1" style="text-align:center; width:100%">  
        <thead>
              <tr style="background-color: #ffc107 ;font-weight: bold;">  
              <th style="width:10%">#</th>
              <th style="width:30%">Date</th>
              <th style="width:20%">Transaction Code</th>
              <th style="width:20%">Item QTY</th>
              <th style="width:20%">Price</th>     
           </tr>  
           </thead>
     
      ';
$content .= generate_sales('all');
$content .= '</table> <br pagebreak="true"/>';

$pdf->writeHTML($toolcopy, true, 0, true, 0);
$pdf->writeHTML($content);





// Daily sales Report
$toolcopy = '<table style="text-align:left;width:100%;">
<tr>
<th style="width:30%;height:80px;"><img src="' . validate_image($_settings->info('logo')) . '"  width="80" height="80"></th>
<th style="width:70%;height:80px;"><br><h1>' . $_settings->info('name') . '</h1></th>
</tr>
</table>';



$content = '';
$content = '
    
    
      	<h2 align="center">Daily Sales Report</h2>
   
      	<table border="1" style="text-align:center;width:100%;margin:auto;">  
        <thead>
          <tr style="background-color: #ffc107 ;font-weight: bold;">  
                <th style="width:10%">#</th>
				        <th style="width:40%">Date</th>
                <th style="width:25%">Item QTY</th>
                <th style="width:25%">Price</th>     
           </tr>  
           </thead>
     
      ';
$content .= generate_sales('day');
$content .= '</table> <br pagebreak="true"/>';

$pdf->writeHTML($toolcopy, true, 0, true, 0);
$pdf->writeHTML($content);




// weekly sales report
$content = '';
$content = '
    
    
      	<h2 align="center">Weekly Sales Report</h2>
   
      	<table border="1" style="text-align:center;width:100%;margin:auto;">  
        <thead>
          <tr style="background-color: #ffc107 ;font-weight: bold;">  
                <th style="width:10%">#</th>
				        <th style="width:40%">Date</th>
                <th style="width:25%">Item QTY</th>
                <th style="width:25%">Price</th>     
           </tr>  
           </thead>
     
      ';
$content .= generate_sales('week');
$content .= '</table> <br pagebreak="true"/>';

$pdf->writeHTML($toolcopy, true, 0, true, 0);
$pdf->writeHTML($content);




// //Month Profit Total

$content = '';
$content .= '
    
    
  <h2 align="center">Monthly Sales Report</h2>

  <table border="1" style="text-align:center; width:100%">  
<thead>
  <tr style="background-color: #ffc107 ;font-weight: bold;">  
  <th style="width:10%">#</th>
  <th style="width:40%">Date</th>
  <th style="width:25%">Item QTY</th>
  <th style="width:25%">Price</th>     
   </tr>  
   </thead>

';
$content .= generate_sales('month');
$content .= '</table> <br pagebreak="true"/>';
$pdf->writeHTML($toolcopy, true, 0, true, 0);
$pdf->writeHTML($content);


// //Best Seller Item
$toolcopy = '<table style="text-align:left;width:100%;">
<tr>
<th style="width:30%;height:80px;"><img src="' . validate_image($_settings->info('logo')) . '"  width="80" height="80"></th>
<th style="width:70%;height:80px;"><br><h1>' . $_settings->info('name') . '</h1></th>
</tr>
</table>';

$content = '';
$content .= '
    
    
  <h2 align="center">Best Sellers</h2>

  <table border="1" style="text-align:center; width:100%">  
<thead>
  <tr style="background-color: #ffc107 ;font-weight: bold;">  
  <th style="width:15%">#</th>
  <th style="width:30%">Item Name</th>
  <th style="width:20%">Variants</th>
  <th style="width:20%">Price</th>      
  <th style="width:15%">Total Qty</th>     
   </tr>  
   </thead>

';
$content .= generate_best_seller('all');
$content .= '</table> <br pagebreak="true"/>';
$pdf->writeHTML($toolcopy, true, 0, true, 0);
$pdf->writeHTML($content);



// //Best Daily Sellers Item
$toolcopy = '<table style="text-align:left;width:100%;">
<tr>
<th style="width:30%;height:80px;"><img src="' . validate_image($_settings->info('logo')) . '"  width="80" height="80"></th>
<th style="width:70%;height:80px;"><br><h1>' . $_settings->info('name') . '</h1></th>
</tr>
</table>';

$content = '';
$content .= '
    
    
  <h2 align="center">Daily Best Sellers</h2>

  <table border="1" style="text-align:center; width:100%">  
<thead>
  <tr style="background-color: #ffc107 ;font-weight: bold;">  
  <th style="width:15%">#</th>
  <th style="width:30%">Item Name</th>
  <th style="width:20%">Variants</th>
  <th style="width:20%">Price</th>     

  <th style="width:15%">Total Qty</th>     
   </tr>  
   </thead>

';
$content .= generate_best_seller('daily');
$content .= '</table> <br pagebreak="true"/>';
$pdf->writeHTML($toolcopy, true, 0, true, 0);
$pdf->writeHTML($content);


// //Best Weekly Sellers Item
$toolcopy = '<table style="text-align:left;width:100%;">
<tr>
<th style="width:30%;height:80px;"><img src="' . validate_image($_settings->info('logo')) . '"  width="80" height="80"></th>
<th style="width:70%;height:80px;"><br><h1>' . $_settings->info('name') . '</h1></th>
</tr>
</table>';

$content = '';
$content .= '
    
    
  <h2 align="center">Weekly Best Sellers</h2>

  <table border="1" style="text-align:center; width:100%">  
<thead>
  <tr style="background-color: #ffc107 ;font-weight: bold;">  
  <th style="width:15%">#</th>
  <th style="width:30%">Item Name</th>
  <th style="width:20%">Variants</th>
  <th style="width:20%">Price</th>     
 
  <th style="width:15%">Total Qty</th>     
   </tr>  
   </thead>

';

$content .= generate_best_seller('weekly');
$content .= '</table> <br pagebreak="true"/>';
$pdf->writeHTML($toolcopy, true, 0, true, 0);
$pdf->writeHTML($content);


$toolcopy = '<table style="text-align:left;width:100%;">
<tr>
<th style="width:30%;height:80px;"><img src="' . validate_image($_settings->info('logo')) . '"  width="80" height="80"></th>
<th style="width:70%;height:80px;"><br><h1>' . $_settings->info('name') . '</h1></th>
</tr>
</table>';

$content = '';
$content .= '
    
    
  <h2 align="center">Monthly Best Sellers</h2>

  <table border="1" style="text-align:center; width:100%">  
<thead>
  <tr style="background-color: #ffc107 ;font-weight: bold;">  
  <th style="width:15%">#</th>
  <th style="width:30%">Item Name</th>
  <th style="width:20%">Variants</th>
  <th style="width:20%">Price</th>     

  <th style="width:15%">Total Qty</th>     
   </tr>  
   </thead>

';
$content .= generate_best_seller('month');
$content .= '</table> ';
$pdf->writeHTML($toolcopy, true, 0, true, 0);
$pdf->writeHTML($content);


$pdf->Output('Sales_Report.pdf', 'I');