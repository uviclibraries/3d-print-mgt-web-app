<?php
  //
  // approved.php  -  display Moneris transaction details/receipt
  //               -  Moneris returns user to this page on approved payment
  //
  session_start();
  include '../db.php';
  include 'moneris.inc.php';

  //Get moneris responses
  $input   = array();
  foreach ($moneris_response_fields as $field) {
    if (array_key_exists($field, $_POST)) {
      $value = $_POST[$field];
    }
    else {
      $value = '';
    }
    if ($value == '' OR $value == "" OR $value == NULL) {
      $input[$field] = "NULL";
    }else{
      $input[$field] = $value;
    }
  }



  //Split into:
  //0: netlinkid
  //1: full name(after search)
  //2: job_id
  $job_id = explode("-", $input['response_order_id']);

  //Change Status
  $current_date = date("Y-m-d");
  $current_status = "paid";
  $stmt = $conn->prepare("UPDATE print_job SET status = :status, paid_date = :paid_date WHERE id = :job_id");
  $stmt->bindParam(':status', $current_status);
  $stmt->bindParam(':paid_date', $current_date);
  $stmt->bindParam(':job_id', $job_id[2]);
  $stmt->execute();

  //get full name
  $stmt = $conn->prepare("SELECT name FROM users WHERE netlink_id = :netlink_id");
  $stmt->bindParam(':netlink_id', $job_id[0]);
  $stmt->execute();
  $getnetlink = $stmt->fetchAll();
  foreach ($getnetlink as $key) {
    $job_id[1] = $key["name"];
  }

  //add to moneris_fields
  $stm = $conn->prepare("INSERT INTO moneris_fields (response_order_id, netlink_id, full_name, response_code, date_stamp, time_stamp, result, trans_name, cardholder, card, charge_total, f4l4, message, iso_code, bank_approval_code, bank_transaction_id, txn_num, avs_response_code, cavv_result_code, INVOICE, ISSCONF, ISSNAME) VALUES (:response_order_id, :netlink_id, :full_name, :response_code, :date_stamp, :time_stamp, :result, :trans_name, :cardholder, :card, :charge_total, :f4l4, :message, :iso_code, :bank_approval_code, :bank_transaction_id, :txn_num, :avs_response_code, :cavv_result_code, :INVOICE, :ISSCONF, :ISSNAME)");

  $stm->bindParam(':response_order_id', $input['response_order_id']);
  $stm->bindParam(':netlink_id', $job_id[0]);
  $stm->bindParam(':full_name', $job_id[1]);
  $stm->bindParam(':response_code', $input['response_code']);
  $stm->bindParam(':date_stamp', $input['date_stamp']);
  $stm->bindParam(':time_stamp', $input['time_stamp']);
  $stm->bindParam(':result', $input['result']);
  $stm->bindParam(':trans_name', $input['trans_name']);
  $stm->bindParam(':cardholder', $input['cardholder']);
  $stm->bindParam(':card', $input['card']);
  $stm->bindParam(':charge_total', $input['charge_total']);
  $stm->bindParam(':f4l4', $input['f4l4']);
  $stm->bindParam(':message', $input['message']);
  $stm->bindParam(':iso_code', $input['iso_code']);
  $stm->bindParam(':bank_approval_code', $input['bank_approval_code']);
  $stm->bindParam(':bank_transaction_id', $input['bank_transaction_id']);
  $stm->bindParam(':txn_num', $input['txn_num']);
  $stm->bindParam(':avs_response_code', $input['avs_response_code']);
  $stm->bindParam(':cavv_result_code', $input['cavv_result_code']);
  $stm->bindParam(':INVOICE', $input['INVOICE']);
  $stm->bindParam(':ISSCONF', $input['ISSCONF']);
  $stm->bindParam(':ISSNAME', $input['ISSNAME']);
  $stm->execute();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <!--header link-->
  <link rel="stylesheet" href="../css/uvic_banner.css">
  <link rel="icon" href="https://www.uvic.ca/assets/core-4-0/img/favicon-32.png">
  <title>Moneris Transaction Approved</title>
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
<b>Moneris Transaction Approved</b>
</p>
<p>
Your payment has been approved.  A receipt should have been sent from Moneris.
</p>
<br>
<a href="../customer-dashboard.php">
<button type="button" type="submit">Return</button>
</a>

</body>
</html>
