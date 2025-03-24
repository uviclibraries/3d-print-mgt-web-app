<?php
//Daily
chdir("/usr/local/apache2/htdocs-webapp/3dprint/jobs_cron");

require ('../db.php');

//date management numbers
$today_str = date("Y-m-d");
$today =  strtotime($today_str);
$day = 86400;


//Can add job types to each array as new types are built in to webapp
$jobTypes=['3d print', 'laser cut', 'large format print'];
$jobTypeTables=['3d_print_job', 'laser_cut_job', 'large_format_print_job'];
$jobTypeIDs=['3d_print_id', 'laser_cut_id', 'large_format_print_id'];


$faq_hrefs=['https://onlineacademiccommunity.uvic.ca/dsc/how-to-3d-print/', 'https://onlineacademiccommunity.uvic.ca/dsc/how-to-laser-cut/', 'https://onlineacademiccommunity.uvic.ca/dsc/tools-tech/large-format-printer-and-scanner/'];
$job_hrefs=['../customer-3d-job-information.php?job_id=', '../customer-laser-job-information.php?job_id=', '../customer-large-format-print-job-information.php?job_id='];

//FOR JOBS THAT HAVE BEEN LEFT UNPAID FOR 7+ DAYS    
for ($type = 0; $type < count($jobTypes); $type++){
  $table = $jobTypeTables[$type];
  $table_id = $jobTypeIDs[$type];
  $id_on_table = $table .'.'. $table_id;

  $stm = $conn->query("SELECT 
    web_job.id AS id, 
    web_job.job_name AS job_name, 
    web_job.netlink_id AS netlink_id, 
    web_job.status AS status, 
    web_job.priced_date AS priced_date, 
    users.email AS email, 
    users.name AS user_name 
  FROM 
    web_job 
  INNER JOIN 
    users ON users.netlink_id = web_job.netlink_id 
  INNER JOIN 
    {$table} ON web_job.id = {$id_on_table} 
  WHERE 
    web_job.status = 'pending payment'
    AND web_job.price > 0
    AND web_job.priced_date = CURDATE() - INTERVAL 1 DAY");


  $job_pp = $stm->fetchAll();

  //pending payment reminder
  if($job_pp && count($job_pp)>0){
    foreach ($job_pp as $job) {
      // echo($job['id']."<br>");
      //reminder email if is been 10 days
      
      $msg = "
      <html>
      <head>
      <title>HTML email</title>
      </head>
      <body>
      <p> Hello, ". $job['user_name'] .". This is an automated email from the DSC.</p>
      <p> Your ".$jobTypes[$type]." job (" . $job['job_name'] . ") has not been paid for. If you still wish to go ahead with your job, please complete the payment <a href=". $job_hrefs[$type].$job['id'] .">online</a>. If you no longer wish to go ahead with your job, ignore this email and your job will automatically be cancelled.</p>
      <p>If you have any questions please review our <a href=". $faq_hrefs[$type] .">FAQ</a> or email us at <a href='mailto:dscommons@uvic.ca'>dscommons@uvic.ca</a>.</p>
      </body>
      </html>";
      $headers = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers .= "From: dscommons@uvic.ca" . "\r\n";
      mail($job['email'],"Reminder-Your 3D Print is ready for payment",$msg,$headers);
    }
  }
}


//FOR JOBS THAT HAVE BEEN LEFT UNPAID FOR 21+ DAYS    
for ($type = 0; $type < count($jobTypes); $type++){
  $table = $jobTypeTables[$type];
  $table_id = $jobTypeIDs[$type];
  $id_on_table = $table .'.'. $table_id;

  //pending_payment jobs query
  //SELECT and UPDATE are separate to ease debugging due to the selection of jobs across multiple tables (job types) and only updating on the main web_job table.
  //Deleted "delivered_date Jan10, 2025"
  $stm = $conn->query("SELECT 
    web_job.id AS id, 
    web_job.status AS status, 
    web_job.priced_date AS priced_date, 
    web_job.cancelled_date as cancelled_date, 
    {$table}.model_name AS model_name, 
    {$table}.model_name_2 AS model_name_2 
  FROM 
    web_job 
  INNER JOIN 
    {$table} 
  ON 
    web_job.id = {$id_on_table} 
  WHERE 
    web_job.status = 'pending payment'
    AND web_job.price >= 0 
    AND web_job.priced_date = CURDATE() - INTERVAL 2 DAY");

  $job_pp = $stm->fetchAll();

  if($job_pp && count($job_pp)>0){
    foreach ($job_pp as $job) {
      $cur_jobID = (int)$job['id'];
      $cancelled = "cancelled";
      $job_past_due = $conn->prepare("UPDATE web_job SET status = :status, cancelled_date = CURDATE() WHERE id = :id");
      //update status to cancelled

      //bind for security's sake
      $job_past_due->bindParam(':id', $cur_jobID, PDO::PARAM_INT); 
      $job_past_due->bindParam(':status', $cancelled, PDO::PARAM_STR);
      $job_past_due->execute();
    }
  }
}
?>