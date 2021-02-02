<?php
//daily
chdir("/usr/local/apache2/htdocs-webapp/3dprint/jobs_cron");
require ('../db.php');

//yesterday's approved transactions
$stm = $conn->prepare("SELECT response_order_id, netlink_id, full_name, date_stamp, time_stamp, message, txn_num, cardholder, charge_total, card, bank_approval_code, bank_transaction_id, INVOICE, ISSCONF, ISSNAME, iso_code, avs_response_code, cavv_result_code, response_code, result, trans_name, f4l4 FROM moneris_fields WHERE date_stamp = :yesterday_date AND response_code >= 0 AND response_code <= 27");
$yesterday = date("Y-m-d", strtotime("-1 days"));
$stm->bindParam(':yesterday_date', $yesterday, PDO::PARAM_STR);
$stm->execute();
$daily_results = $stm->fetchAll();

$header = array("Order ID", "Netlink ID", "Full Name", "Date", "Time", "Message", "Transaction Num", "Cardholder", "Charge", "Card", "Bank Approval Code", "Bank Transaction ID", "INVOICE", "ISSCONF", "ISSNAME", "ISO Code", "AVS Response Code", "CAVV Result Code", "Response Code");
$fix = array("Result", "Trans Name", "f4l4");
$header = array_merge($header, $fix);

foreach ($daily_results as $row) {
  fputcsv($output, $row);
}

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
  <td>" . $row["txn_num"] . "</td>
  <td>" . $row["cardholder"] . "</td>
  <td>" . $row["charge_total"] . "</td>
  <td>" . $row["card"] . "</td>
  <td>" . $row["bank_approval_code"] . "</td>
  <td>" . $row["bank_transaction_id"] . "</td>
  <td>" . $row["INVOICE"] . "</td>
  <td>" . $row["ISSCONF"] . "</td>
  <td>" . $row["ISSNAME"] . "</td>
  <td>" . $row["iso_code"] . "</td>
  <td>" . $row["avs_response_code"] . "</td>
  <td>" . $row["cavv_result_code"] . "</td>
  <td>" . $row["response_code"] . "</td>
  <td>" . $row["result"] . "</td>
  <td>" . $row["trans_name"] . "</td>
  <td>" . $row["f4l4"] . "</td>
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

$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: dscommons@uvic.ca" . "\r\n";
$subject = $yesterday . " 3D print Moneris Report";

//Get mailing list
$stm = $conn->query("SELECT email FROM users WHERE cron_report = 1 && user_type = 0");
$cron_report_email = $stm->fetchAll();
foreach ($cron_report_email as $admin) {
  mail($admin["email"],$subject,$msg,$headers);
}
?>
