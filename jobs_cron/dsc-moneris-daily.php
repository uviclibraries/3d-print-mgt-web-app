<?php
//daily
chdir("/usr/local/apache2/htdocs-webapp/3dprint/jobs_cron");
// Triton path: chdir("/usr/local/apache2/htdocs-webapp/demo/3dwebapp/jobs_cron");
 
require ('../db.php');

//yesterday's approved transactions
$stm = $conn->prepare("SELECT response_order_id, netlink_id, full_name, date_stamp, time_stamp, message, txn_num, cardholder, charge_total, card, bank_approval_code, bank_transaction_id, INVOICE, ISSCONF, ISSNAME, iso_code, avs_response_code, cavv_result_code, response_code, result, trans_name, f4l4 FROM moneris_fields WHERE date_stamp = :yesterday_date AND response_code >= 0 AND response_code <= 27");
$yesterday = date("Y-m-d", strtotime("-1 days"));
$stm->bindParam(':yesterday_date', $yesterday, PDO::PARAM_STR);
$stm->execute();
$daily_results = $stm->fetchAll();

// Debugging Code *************
#print("num transactions: ". count($daily_results).'<br>');
#exit;
// **************

$header = array("Order ID", "Netlink ID", "Full Name", "Date", "Time", "Message", "Cardholder", "Charge", "Card", "Bank Transaction ID", "ISO Code", "Response Code");
//$fix = array("Result", "Trans Name", "f4l4");
//$header = array_merge($header, $fix);

$sum = 0;
$direct_link = "https://webapp.library.uvic.ca/3dprint/admin-reports.php?approved=on&searchdate_start=". $yesterday ."&searchdate_end=". $yesterday;
$msg = "
<html>
<head>
  <title>daily paid transactions</title>
  <style>
    table, th, td {
      border: 1px solid black;
      }
  </style>
</head>
<body>
  <p>This is an automated email from the DSC.</p>
  <p>Paid transactions for ". $yesterday .". </p>
  <table>
    <thead>
    <tr>";
foreach ($header as $column) {
  $msg .= "<th>" . $column . "</th>";
}
$msg .=
  "<thead>
  </tr>
  <tbody>";
foreach ($daily_results as $row) {
  $msg .= "<tr>
  <td>" . $row["response_order_id"] . "</td>
  <td>" . $row["netlink_id"] . "</td>
  <td>" . $row["full_name"] . "</td>
  <td>" . $row["date_stamp"] . "</td>
  <td>" . $row["time_stamp"] . "</td>
  <td>" . $row["message"] . "</td>
  <td>" . $row["cardholder"] . "</td>
  <td>" . $row["charge_total"] . "</td>
  <td>" . $row["card"] . "</td>
  <td>" . $row["bank_transaction_id"] . "</td>
  <td>" . $row["iso_code"] . "</td>
  <td>" . $row["response_code"] . "</td>
  </tr>";
  $sum += $row["charge_total"];
}
$msg .= "
  <tr>
    <th>Daily Sum</th>
    <th>". number_format((float)$sum, 2, '.','') . "</th>
  </tr>
  </tbody>
  </table>
  <p>To download the CSV, please go to the <a href=". $direct_link .">reports</a> page.</p>
</body>
</html>";

// Debugging code *************
#print ($msg.'<br>');
#exit;
// ***************s

$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: dscommons@uvic.ca" . "\r\n";
$subject = $yesterday . " 3D print Moneris Report";

//Get mailing list
$stm = $conn->query("SELECT email FROM users WHERE cron_report = 1 && user_type = 0");
$cron_report_email = $stm->fetchAll();

#mail('rmccue@uvic.ca',$subject,$msg,$headers);
foreach ($cron_report_email as $admin) {
  #print($admin['email'].'<br>');
  mail($admin['email'],$subject,$msg,$headers);
}

// enter in url bar when on Triton: https://devwebapp.library.uvic.ca/demo/3dwebapp/jobs_cron/dsc-moneris-daily.php 
?>