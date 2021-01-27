<?php
session_start();
require ('auth-sec.php'); //Gets CAS & db
//auth-sec includes: $user, $user_email, $user_type, $user_name
//Is user Admin check
if ($user_type == 1) {
  header("Location: customer-dashboard.php");
  die();
}

//record search parameters
$sql_line =array(); //sql builder
$getcheck = array_fill(0,4, FALSE);
if (isset($_GET['searchdate_start']) && ($_GET['searchdate_start'] != "" && $_GET['searchdate_start'] != NULL)) {
  $getcheck[0] = True;
  $sql_line[] = "date_stamp >= :searchdate_start";
}if (isset($_GET['searchdate_end']) && ($_GET['searchdate_end'] != "" && $_GET['searchdate_end'] != NULL)) {
  $getcheck[1] = True;
  $sql_line[] = "date_stamp <= :searchdate_end";
}if (isset($_GET['searchorder_id']) && ($_GET['searchorder_id'] != "" && $_GET['searchorder_id'] != NULL)) {
  $getcheck[2] = True;
  $sql_line[] = "response_order_id LIKE :searchorder_id";
}if (isset($_GET['approved'])) {
  $getcheck[3] = True;
  $sql_line[] = "response_code >= 0 AND response_code <= 27";
}
$dateline = ""; //Used for table description
//Check if parameters are empty
if ($getcheck[0]==FALSE && $getcheck[1]==FALSE && $getcheck[2]==FALSE && $getcheck[3]==FALSE) {
  $stm = $conn->query("SELECT response_order_id, netlink_id, full_name, date_stamp, time_stamp, message, txn_num, cardholder, charge_total, card, bank_approval_code, bank_transaction_id, INVOICE, ISSCONF, ISSNAME, iso_code, avs_response_code, cavv_result_code, response_code, result, trans_name, f4l4  FROM moneris_fields ORDER BY id");
  $dateline .= " Until " . date("Y-m-d");
}
//find out what parameters are being searched for
else{

  //build sql query line based on search parameters
  $searchline = "SELECT response_order_id, netlink_id, full_name, date_stamp, time_stamp, message, txn_num, cardholder, charge_total, card, bank_approval_code, bank_transaction_id, INVOICE, ISSCONF, ISSNAME, iso_code, avs_response_code, cavv_result_code, response_code, result, trans_name, f4l4  FROM moneris_fields WHERE " . implode(" AND ", $sql_line);
  $stm = $conn->prepare($searchline);
  //echo $searchline . "\n";

  //Bind search parameters & fill dateline to be displayed
  if ($getcheck[0] == TRUE) {
    $stm->bindParam(':searchdate_start', $_GET['searchdate_start'], PDO::PARAM_STR);
    $dateline .= " From " . $_GET["searchdate_start"];
  }
  if ($getcheck[1] == TRUE) {
    $stm->bindParam(':searchdate_end', $_GET['searchdate_end'], PDO::PARAM_STR);
    $dateline .= " Until " . $_GET["searchdate_end"];
  }
  else {
    $dateline .= " Until " . date("Y-m-d");
  }
  if ($getcheck[2] == TRUE) {
    $temp = $_GET['searchorder_id']."%";
    $stm->bindParam(':searchorder_id', $temp, PDO::PARAM_STR);
  }
  if ($getcheck[3] == TRUE) {
    $dateline .= " Only Paid Transaction. ";
  }

  $stm->execute();

}
$all_users = $stm->fetchAll(PDO::FETCH_ASSOC);

$sum = 0;
foreach ($all_users as $row) {
  $sum += $row["charge_total"];
}

$get_line = array();
//Seach button clicked$_SERVER['REQUEST_METHOD'] === 'POST'
if(isset($_POST["Search"])){
  if (isset($_POST["searchdate_start"])) {
    $get_line[] = "searchdate_start=" . $_POST["searchdate_start"];
  }
  if (isset($_POST["searchdate_end"])) {
    $get_line[] = "searchdate_end=" . $_POST["searchdate_end"];
  }
  if (isset($_POST["searchorder_id"])) {
    $get_line[] = "searchorder_id=" . $_POST["searchorder_id"];
  }if (isset($_POST["approved"])) {
    $get_line[] = "approved=" . $_POST["approved"];
  }
  header("Location: admin-reports.php?". implode("&", $get_line));
}
//If clicked download CSV
if (isset($_POST["getCSV"])) {
  $filename = "3Dprint_Moneris_report.csv";
  header("Content-Type: text/csv;");
  header("Content-Disposition: attachment; filename=".$filename);

  $fp = fopen("php://output", "w");
  $column = array("Order ID", "Netlink ID", "Full Name", "Date", "Time", "Message", "Transaction Num", "Cardholder", "Charge", "Card", "Bank Approval Code", "Bank Transaction ID", "INVOICE", "ISSCONF", "ISSNAME", "ISO Code", "AVS Response Code", "CAVV Result Code",);
  $fix = array("Response Code", "Result", "Trans Name", "f4l4");
  $column = array_merge($column, $fix);
  fputcsv($fp, $column);
  foreach ($all_users as $row) {
    fputcsv($fp, $row);
  }
  fclose($fp);
  exit();
}
 ?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v4.0.1">
    <title>User Management</title>

    <!--header link-->
    <link rel="stylesheet" href="css/uvic_banner.css">
    <link rel="icon" href="https://www.uvic.ca/assets/core-4-0/img/favicon-32.png">
    <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/checkout/">

    <!-- Bootstrap core CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <!-- Favicons -->
<link rel="apple-touch-icon" href="/docs/4.5/assets/img/favicons/apple-touch-icon.png" sizes="180x180">
<link rel="icon" href="/docs/4.5/assets/img/favicons/favicon-32x32.png" sizes="32x32" type="image/png">
<link rel="icon" href="/docs/4.5/assets/img/favicons/favicon-16x16.png" sizes="16x16" type="image/png">
<link rel="manifest" href="/docs/4.5/assets/img/favicons/manifest.json">
<link rel="mask-icon" href="/docs/4.5/assets/img/favicons/safari-pinned-tab.svg" color="#563d7c">
<link rel="icon" href="/docs/4.5/assets/img/favicons/favicon.ico">
<meta name="msapplication-config" content="/docs/4.5/assets/img/favicons/browserconfig.xml">
<meta name="theme-color" content="#563d7c">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

    </style>
    <!-- Custom styles for this template -->

  </head>
  <body class="bg-light">

    <!--Header-->
    <div id="custom_header"><div class="wrapper" style="min-height: 6em;" id="banner">
     <div style="position:absolute; left: 5px; top: 26px;">
      <a href="http://www.uvic.ca/" id="logo"><span>University of Victoria</span></a>
     </div>
     <div style="position:absolute; left: 176px; top: 26px;">
      <a href="http://www.uvic.ca/library/" id="unit"><span>Libraries</span></a>
     </div>
     <div class="edge" style="position:absolute; margin: 0px;right: 0px; top: 0px; height: 96px; width:200px;">&nbsp;</div>
    </div>
    <!--Header end-->

  <div class="row">

  <div class="container">
  <div class="py-3 text-left">

    <div class="row">
      <div class="col-md-4">
        <form method="POST">
          <div>
            <label for = "searchdate_start">Start date:</label>
            <input type="date" id= "searchdate_start" name="searchdate_start"
            <?php if ($getcheck[0]){ ?> value = <?php echo $_GET["searchdate_start"]; } ?> >
          </div>
          <div class="">
            <label for = "searchdate_end">End date:</label>
            <input type="date" id= "searchdate_end"  name="searchdate_end"
            <?php if ($getcheck[1]){ ?> value = <?php echo $_GET["searchdate_end"]; } ?> >
          </div>
          <div class="">
            <label for = "searchorder_id">order_id:</label>
            <input type="text" id= "searchorder_id" name="searchorder_id"
            <?php if ($getcheck[2]){ ?> value = <?php echo $_GET["searchorder_id"]; } ?> >
          </div>
          <div class="">
            <label for = "approved">Paid: </label>
            <input type="checkbox" id= "approved" name="approved"
            <?php if ($getcheck[3]){ ?> checked <?php } ?> >
          </div>
          <input type="submit" name="Search" value="Search">
          <!--<input type="submit" name="getCSV" value="getCSV" class="btn btn-md btn-danger btn-">-->

      </div>
      <div class="col-md-4 offset-md-4">
        <a class="btn btn-md btn-primary btn-" href="admin-dashboard.php" role="button">Back to Dashboard</a>
      </div>
    </div>

  <br>
  <h3>Moneris Report: <?php echo date("Y/m/d-h:ia"); ?></h3>
  <div class="row">
    <div class="col align-self-start">
    <p><?php echo $dateline . " Sum: "?><b><?php echo "$" . number_format((float)$sum, 2, '.',''); ?></b></p>
    </div>
    <div class="col-md-4 align-self-end">
      <input type="submit" name="getCSV" value="Download CSV" class="btn btn-md btn-danger btn-">
    </div>
  </div>
  </form>
  <br>
  <div class="table-responsive">
    <table class="table table-striped table-md">
      <tbody>
        <tr>
          <thread>
            <th>Order ID</th>
            <th>netlink ID</th>
            <th>Full Name</th>
            <th>Date</th>
            <th>Time</th>
            <th>Message</th>
            <th>TRXN Num</th>
            <th>Cardholder</th>
            <th>Charge</th>
            <th>Card</th>
            <th>Bank Approval Code</th>
            <th>Bank Transaction ID</th>
            <th>INVOICE</th>
            <th>ISSCONF</th>
            <th>ISSNAME</th>
            <th>ISO Code</th>
            <th>AVS Response Code</th>
            <th>CAVV Result Code</th>
            <th>Response Code</th>
            <th>Result</th>
            <th>Trans Name</th>
            <th>f4l4</th>
          </thread>
        </tr>
        <!------------------------------------------->
        <?php foreach ($all_users as $row) {
        ?>
        <tr>
          <td><?php echo $row["response_order_id"]; ?></td>
          <td><?php echo $row["netlink_id"]; ?></td>
          <td><?php echo $row["full_name"]; ?></td>
          <td><?php echo $row["date_stamp"]; ?></td>
          <td><?php echo $row["time_stamp"]; ?></td>
          <td><?php echo $row["message"]; ?></td>
          <td><?php echo $row["txn_num"]; ?></td>
          <td><?php echo $row["cardholder"]; ?></td>
          <td><?php echo $row["charge_total"]; ?></td>
          <td><?php echo $row["card"]; ?></td>
          <td><?php echo $row["bank_approval_code"]; ?></td>
          <td><?php echo $row["bank_transaction_id"]; ?></td>
          <td><?php echo $row["INVOICE"]; ?></td>
          <td><?php echo $row["ISSCONF"]; ?></td>
          <td><?php echo $row["ISSNAME"]; ?></td>
          <td><?php echo $row["iso_code"]; ?></td>
          <td><?php echo $row["avs_response_code"]; ?></td>
          <td><?php echo $row["cavv_result_code"]; ?></td>
          <td><?php echo $row["response_code"]; ?></td>
          <td><?php echo $row["result"]; ?></td>
          <td><?php echo $row["trans_name"]; ?></td>
          <td><?php echo $row["f4l4"]; ?></td>
        </tr>
        <?php
        }
        ?>
      <!------------------------------------------->
      </tbody>
    </table>
  </div>

<hr class="mb-12">

</div>
</div>
</div>

</body>
</html>
