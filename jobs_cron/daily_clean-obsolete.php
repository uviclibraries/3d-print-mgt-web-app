<?php
//Daily
require ('db.php');


//date management numbers
$today_str = date("Y-m-d");
$today =  strtotime($today_str);
$day = 86400;

//pending_payment jobs query
$stm = $conn->query("SELECT web_job.id AS job_id, web_job.job_name AS job_name, print_job.model_name AS model_name, print_job.model_name_2 AS model_name_2, web_job.netlink_id AS netlink_id, web_job.status AS status, web_job.priced_date AS priced_date, users.email AS email, users.name AS user_name FROM web_job INNER JOIN users on users.netlink_id = web_job.netlink_id WHERE web_job.status = 'pending payment' ORDER BY priced_date ASC");
$job_pp = $stm->fetchAll();

//delivered jobs query
$stm = $conn->query("SELECT id, delivered_date FROM web_job WHERE status = 'delivered'");
$job_com = $stm->fetchAll();

//Updating database preperation
$stm1 = $conn->prepare("UPDATE web_job SET status = :status, delivered_date = :delivered_date WHERE id = :job_id");

//pending payment reminder & Cancelations
foreach ($job_pp as $job) {
  $days_passed = ($today-strtotime($job['priced_date']))/$day;
  //reminder email
  if($days_passed == 10){

    $direct_link = "https://webapp.library.uvic.ca/3dprint/customer-dashboard.php";
    $direct_link2 = "https://onlineacademiccommunity.uvic.ca/dsc/how-to-3d-print/";
    $msg = "
    <html>
    <head>
    <title>HTML email</title>
    </head>
    <body>
    <p> Hello, ". $job['user_name'] .". This is an automated email from the DSC. </p>
    <p> Your 3D print or laser cutting job; " . $job['job_name'] . " has not been paid for. If you still wish to have your job printed, please complete the payment <a href=". $direct_link .">online</a>. If you no longer wish to print your job, ignore this email and your print job will automatically be cancelled.</p>
    <p>If you have any questions please review our <a href=". $direct_link2 .">FAQ</a> or email us at DSCommons@uvic.ca.</p>
    </body>
    </html>";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: dscommons@uvic.ca" . "\r\n";
    mail($job['email'],"Reminder-Your 3D Print is ready for payment",$msg,$headers);

  }
  //Cancelling
  elseif ($days_passed > 15) {
    //update satus
    $cancelled = "cancelled";
    $stm1->bindParam(':job_id', $job['job_id']);
    $stm1->bindParam(':status', $cancelled);
    $stm1->bindParam(':delivered_date', $today_str);
    $stm1->execute();

    //deleting 3d file
    $delete = "uploads/" . $job['model_name'];
    if(is_file($delete)){
      unlink($delete);
    }
    //check if secondary file exists. If so delete
    if ($job["model_name_2"] != NULL) {
      $delete2 = "uploads/" . $job['model_name_2'];
      if (is_file($delete2)) {
        unlink($delete2);
      }
    }
  }

}

//Archiving
foreach ($job_com as $job) {
  $days_passed = ($today-strtotime($job['delivered_date']))/$day;
  if ($days_passed > 14) {
    $archived = "archived";
    $stm1->bindParam(':job_id', $job['id']);
    $stm1->bindParam(':status', $archived);
    $stm1->bindParam(':delivered_date', $job['delivered_date']);
    $stm1->execute();
  }
}
?>
