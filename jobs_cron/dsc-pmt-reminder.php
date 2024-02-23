<?php
//Daily
// chdir("/usr/local/apache2/htdocs-webapp/3dprint/jobs_cron");
chdir("/usr/local/apache2/htdocs-webapp/demo/3dwebapp/jobs_cron");

require ('../db.php');

echo('running chron');
//date management numbers
$today_str = date("Y-m-d");
$today =  strtotime($today_str);
$day = 86400;

$jobTypes=['3d print', 'laser cut', 'large format print'];
$jobTypeTables=['3d_print_job', 'laser_cut_job', 'large_format_print_job'];
$jobTypeIDs=['3d_print_id', 'laser_cut_id', 'large_format_print_id'];
$faq_hrefs=['https://onlineacademiccommunity.uvic.ca/dsc/how-to-3d-print/', 'https://onlineacademiccommunity.uvic.ca/dsc/how-to-laser-cut/', 'https://onlineacademiccommunity.uvic.ca/dsc/tools-tech/large-format-printer-and-scanner/'];
$job_hrefs=['https://webapp.library.uvic.ca/3dprint/customer-3d-job-information?job_id=', 'https://webapp.library.uvic.ca/3dprint/customer-laser-job-information.php?job_id=', 'https://webapp.library.uvic.ca/3dprint/customer-large-format-print-job-information?job_id='];

for ($type = 0; $type <=2; $type++){
  echo($jobTypeIDs[$type]."<br>");
  //pending_payment jobs query
  $stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.netlink_id AS netlink_id, web_job.status AS status, web_job.priced_date AS priced_date, users.email AS email, users.name AS user_name FROM web_job INNER JOIN users ON users.netlink_id = web_job.netlink_id INNER JOIN $jobTypeTables[$type] ON web_job.id = $jobTypeTables[$type].$jobTypeIDs[$type] WHERE web_job.status = 'pending payment' AND web_job.netlink_id = 'chloefarr' ORDER BY priced_date ASC");
  $job_pp = $stm->fetchAll();

  //Updating database preperation
  // $stm1 = $conn->prepare("UPDATE web_job SET status = :status, delivered_date = :delivered_date INNER JOIN $jobTypeTables[$type] ON web_job.id=$jobTypeTables[$type].$jobTypeIDs[$type] WHERE id = :job_id");

  echo("type: ". $jobTypes[$type]."; length job_pp: " . count($job_pp)."<br>");

  //pending payment reminder & Cancelations
  foreach ($job_pp as $job) {
    print($job['id']);
    echo($job['id']."<br>");
    $days_passed = ($today-strtotime($job['priced_date']))/$day;
    //reminder email if is been 10 days
    if($days_passed == 12){
      $msg = "
      <html>
      <head>
      <title>HTML email</title>
      </head>
      <body>
      <p> Hello, ". $job['user_name'] .". This is an automated email from the DSC. </p>
      <p> Your ".$jobTypes[$type]." job (" . $job['job_name'] . ") has not been paid for. If you still wish to go ahead with your job, please complete the payment <a href=". $job_hrefs[$type].$job['id'] .">here</a>. If you no longer wish to go ahead with your job, ignore this email and your job will automatically be cancelled.</p>
      <p>If you have any questions please review our <a href=". $faq_hrefs[$type] .">FAQ</a> or email us at DSCommons@uvic.ca.</p>
      </body>
      </html>";
      $headers = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers .= "From: dscommons@uvic.ca" . "\r\n";
      mail($job['email'],"Reminder-Your 3D Print is ready for payment",$msg,$headers);
      // print('id: '. $job['id'] . ' ; job name: ' . $job['job_name'] . ' -- 10 days have passed<br>');
    }
  
    // elseif($days_passed > 20){
    //   //update satus to cancelled
    //   $cancelled = "cancelled";
    //   $stm1->bindParam(':job_id', $job['job_id']);
    //   $stm1->bindParam(':status', $cancelled);
    //   $stm1->bindParam(':delivered_date', $today_str);

    //   $stm1->execute();

    //   //deleting 3d file
    //   $delete = "uploads/" . $job['model_name'];
    //   if(is_file($delete)){
    //     unlink($delete);
    //   }

    //   //check if secondary file exists. If so delete
    //   if ($job["model_name_2"] != NULL) {
    //     $delete2 = "uploads/" . $job['model_name_2'];
    //     if (is_file($delete2)) {
    //       unlink($delete2);
    //     }
    //   }
    //   print('id: '. $job['id'] . ' ; job name: ' . $job['job_name'] . '20 days have passed<br>');
    // }
    else{
      print('id: '. $job['id'] . ' ; job name: ' . $job['job_name'] . ' -- failed condition<br>');
    }
  }
}
?>

<!--enter in url bar when on Triton: https://devwebapp.library.uvic.ca/demo/3dwebapp/jobs_cron/dsc-pmt-reminder.php-->