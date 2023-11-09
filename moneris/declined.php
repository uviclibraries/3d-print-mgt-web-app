<?php
  //
  // declined.php  -  display Moneris transaction details/receipt
  //               -  Moneris returns user to this page on declined payment
  //
  session_start();
  include '../db.php';
  require ('../auth-sec.php'); //Gets CAS & db
  include 'moneris.inc.php';
  $ticket = $_GET["ticket"];
  moneris_receipt($ticket);
  $job_id = $_SESSION['job_id'];
  $order_id = $_GET["order_id"];
  $amount = $_GET["amount"];

  //get full name
  $stmt = $conn->prepare("SELECT name FROM users WHERE netlink_id = :netlink_id");
  $stmt->bindParam(':netlink_id', $user);
  $stmt->execute();
  $getnetlink = $stmt->fetchAll();
  foreach ($getnetlink as $key) {
    $name = $key["name"];
  }

  $stm = $conn->prepare("INSERT INTO moneris_fields (response_order_id, netlink_id, full_name, response_code, date_stamp, time_stamp, result, trans_name, cardholder, card, charge_total, f4l4, message, iso_code, bank_approval_code, bank_transaction_id, txn_num, avs_response_code, cavv_result_code, INVOICE, ISSCONF, ISSNAME) VALUES (:response_order_id, :netlink_id, :full_name, :response_code, :date_stamp, :time_stamp, :result, :trans_name, :cardholder, :card, :charge_total, :f4l4, :message, :iso_code, :bank_approval_code, :bank_transaction_id, :txn_num, :avs_response_code, :cavv_result_code, :INVOICE, :ISSCONF, :ISSNAME)");

  $empty_string = '';
  $empty_number = 0;
  $message = 'DECLINED';
  $iso = 1;
  $response_code = 0;
  $stm->bindParam(':response_order_id', $order_id);
  $stm->bindParam(':netlink_id', $user);
  $stm->bindParam(':full_name', $name);
  $stm->bindParam(':response_code', $response_code);
  $stm->bindParam(':date_stamp', $current_date);
  $stm->bindParam(':time_stamp', $current_time);
  $stm->bindParam(':result', $empty_string);
  $stm->bindParam(':trans_name', $empty_string);
  $stm->bindParam(':cardholder', $empty_string);
  $stm->bindParam(':card', $empty_string);
  $stm->bindParam(':charge_total', $amount);
  $stm->bindParam(':f4l4', $empty_string);
  $stm->bindParam(':message', $message);
  $stm->bindParam(':iso_code', $iso);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <!--header link-->
  <link rel="stylesheet" href="../css/uvic_banner.css">
  <link rel="icon" href="https://www.uvic.ca/assets/core-4-0/img/favicon-32.png">
  <title>Moneris Transaction Declined</title>
</head>
<body>

      <!--Header-->
      <div id="custom_header"><div class="wrapper" id="banner">
       <div style="position:absolute; left: 5px; top: 26px;">
        <a href="http://www.uvic.ca/" id="logo"><span>University of Victoria</span></a>
       </div>
       <div style="position:absolute; left: 176px; top: 26px;">
        <a href="http://www.uvic.ca/library/" id="unit"><span>Libraries</span></a>
       </div>
       <div class="edge" style="position:absolute; margin: 0px;right: 0px; top: 0px; height: 96px; width:200px;">&nbsp;</div>
      </div>
      <!--Header end-->

<p>
<b>Moneris Transaction Declined</b>
</p>
<p>
Your payment attempt has been declined.
</p>
<br>
<a href="../customer-dashboard.php">
<button type="button" type="submit">Return</button>
</a>

</body>
</html>
