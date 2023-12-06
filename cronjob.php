<?php
require ('db.php');

//daily
//Today's approved tranasactions
$stm = $conn->prepare("SELECT response_order_id, netlink_id, full_name, date_stamp, time_stamp, message, txn_num, cardholder, charge_total, card, bank_approval_code, bank_transaction_id, INVOICE, ISSCONF, ISSNAME, iso_code, avs_response_code, cavv_result_code, response_code, result, trans_name, f4l4 FROM moneris_fields WHERE date_stamp = :today_date AND response_code >= 0 AND response_code <= 27");
$today = date("Y-m-d");
$stm->bindParam(':today_date', $today, PDO::PARAM_STR);
$stm->execute();
$daily_results = $stm->fetchAll();


//Create csv file
$filename = $today . "_3Dprint_Moneris.csv";

$output = fopen("php://output", "w");
//
$header = array("Order ID", "Netlink ID", "Full Name", "Date", "Time", "Message", "Transaction Num", "Cardholder", "Charge", "Card", "Bank Approval Code", "Bank Transaction ID", "INVOICE", "ISSCONF", "ISSNAME", "ISO Code", "AVS Response Code", "CAVV Result Code", "Response Code");
$fix = array("Result", "Trans Name", "f4l4");
$header = array_merge($header, $fix);

foreach ($daily_results as $row) {
  fputcsv($output, $row);
}

$sum = 0;
$direct_link = "https://webapp.library.uvic.ca/3dprint/admin-reports.php?approved=on&searchdate_start=". $today ."&searchdate_end=". $today;
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
  <p>Paid transactions for ". $today .". </p>
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
  <p>For more reports, go <a href=". $direct_link .">here</a>.</p>
</body>
</html>";


$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: dscommons@uvic.ca" . "\r\n";
$subject = $today . " 3D print Moneris Report";

mail("kenziewong@gmail.com",$subject,$msg,$headers);

//-----------------------------------------------------





//date management numbers
$today_str = date("Y-m-d");
$today =  strtotime($today_str);
$day = 86400;
$signer = 'chronjobs';

//pending_payment jobs query
$stm = $conn->query("SELECT print_job.id AS job_id, print_job.job_name AS job_name, print_job.model_name AS model_name, print_job.netlink_id AS netlink_id, print_job.status AS status, print_job.priced_date AS priced_date, users.email AS email, users.name AS user_name FROM print_job INNER JOIN users on users.netlink_id = print_job.netlink_id WHERE print_job.status = 'pending payment' ORDER BY priced_date ASC");
$job_pp = $stm->fetchAll();

//Completed jobs query
$stm = $conn->query("SELECT id, job_name, netlink_id, status, completed_date, cancelled_date model_name, cancelled_signer = :cancelled_signer FROM print_job WHERE status = 'completed'");
$job_com = $stm->fetchAll();

//Updating database preperation
$stm1 = $conn->prepare("UPDATE print_job SET status = :status, completed_date = :completed_date cancelled_date = :cancelled_date , cancelled_signer = :cancelled_signer WHERE id = :job_id");

//for jobs that have gone unpaid
foreach ($job_pp as $job) {
  $days_passed = ($today-strtotime($job['priced_date']))/$day;
  //reminder email
  if($days_passed == 10){
    echo $job['job_id'] . " - " . $job['job_name'] . " has been emailed. \n";
    //EMAIL.
    $direct_link = "https://webapp.library.uvic.ca/3dprint/customer-job-information.php?job_id=". $job['job_id'];
    $msg = "
    <html>
    <head>
    <title>HTML email</title>
    </head>
    <body>
    <p> Hello, ". $job['user_name'] .". This is an automated email from the DSC. </p>
    <p> Your 3D print job; " . $job['job_name'] . " has not been paid for. If you still wish to have your job printed, please complete the payment <a href=". $direct_link .">here</a>. If you no longer wish to print your job, ignore this email and your print job will automatically be cancelled.</p>
    <p>If you have any questions please review our FAQ or email us at DSCommons@uvic.ca.</p>
    </body>
    </html>";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: dscommons@uvic.ca" . "\r\n";
    mail($job['email'],"Reminder-Your 3D Print is ready for payment",$msg,$headers);

  }
  //Cancelling *FIX 150->15*
  elseif ($days_passed > 150) {
    $delete = "uploads/" . $job['model_name'];

    if(is_file($delete)){
      unlink($delete);
    }
    $cancelled = "cancelled";
    $stm1->bindParam(':job_id', $job['job_id']);
    $stm1->bindParam(':status', $cancelled);
    $stm1->bindParam(':cancelled_date', $today_str);
    $stm1->bindParam(':cancelled_signer', $signer);
    $stm1->bindParam(':completed_date', $today_str);
    $stm1->execute();

    echo $job['id'] . " - " . $job['job_name'] . " has been cancelled & the file deleted. \n";
  }
}

//Archiving
foreach ($job_com as $job) {
  $days_passed = ($today-strtotime($job['completed_date']))/$day;
  if ($days_passed > 13) {
    echo $job['id'] . " - " . $job['job_name'] . " has been archived. \n";
    $archived = "archived";
    $stm1->bindParam(':job_id', $job['id']);
    $stm1->bindParam(':status', $archived);
    $stm1->bindParam(':completed_date', $job['completed_date']);
    $stm1->execute();
  }
}




/*
//Weekly deletion
require ('db.php');

//One semester ago & one semester + 1 week ago.
$today_120 = date("Y-m-d", strtotime(-120 days));
$today_134 = date("Y-m-d", strtotime(-134 days));

//Archived jobs
$stm = $conn->query("SELECT model_name FROM print_job WHERE status = 'archived' AND completed_date <= $today_120 AND completed_date >= $today_134");
$job_arh = $stm->fetchAll();
foreach ($job_arh as $job) {
  $delete = "uploads/" . $job['model_name'];
  if (is_file($delete)) {
    unlink($delete);
  }
}
*/

?>
