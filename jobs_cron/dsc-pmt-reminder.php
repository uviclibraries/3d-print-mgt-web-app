<?php
//Daily
require ('../db.php');


//date management numbers
$today_str = date("Y-m-d");
$today =  strtotime($today_str);
$day = 86400;

//pending_payment jobs query
$stm = $conn->query("SELECT print_job.id AS job_id, print_job.job_name AS job_name, print_job.model_name AS model_name, print_job.model_name_2 AS model_name_2, print_job.netlink_id AS netlink_id, print_job.status AS status, print_job.priced_date AS priced_date, users.email AS email, users.name AS user_name FROM print_job INNER JOIN users on users.netlink_id = print_job.netlink_id WHERE print_job.status = 'pending payment' ORDER BY priced_date ASC");
$job_pp = $stm->fetchAll();

//pending payment reminder & Cancelations
foreach ($job_pp as $job) {
  $days_passed = ($today-strtotime($job['priced_date']))/$day;
  //reminder email if is been 10 days
  if($days_passed == 10){
    $direct_link = "https://webapp.library.uvic.ca/3dprint/customer-job-information.php?job_id=". $job['job_id'];
    $direct_link2 = "https://onlineacademiccommunity.uvic.ca/dsc/how-to-3d-print/";
    $msg = "
    <html>
    <head>
    <title>HTML email</title>
    </head>
    <body>
    <p> Hello, ". $job['user_name'] .". This is an automated email from the DSC. </p>
    <p> Your 3D print job; " . $job['job_name'] . " has not been paid for. If you still wish to have your job printed, please complete the payment <a href=". $direct_link .">here</a>. If you no longer wish to print your job, ignore this email and your print job will automatically be cancelled.</p>
    <p>If you have any questions please review our <a href=". $direct_link2 .">FAQ</a> or email us at DSCommons@uvic.ca.</p>
    </body>
    </html>";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: dscommons@uvic.ca" . "\r\n";
    mail($job['email'],"Reminder-Your 3D Print is ready for payment",$msg,$headers);
  }

}
