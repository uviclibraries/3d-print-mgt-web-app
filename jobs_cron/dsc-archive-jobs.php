<?php
//Daily
chdir("/usr/local/apache2/htdocs-webapp/3dprint/jobs_cron");
// Triton path: chdir("/usr/local/apache2/htdocs-webapp/demo/3dwebapp/jobs_cron");
require ('../db.php');


//date management numbers
$today_str = date("Y-m-d");
$today =  strtotime($today_str);
$day = 86400;

//delivered jobs query
$stm = $conn->query("SELECT id, delivered_date, job_name FROM web_job WHERE status = 'delivered'");
$job_com = $stm->fetchAll();

//Updating database preperation
$stm1 = $conn->prepare("UPDATE web_job SET status = :status, delivered_date = :delivered_date WHERE id = :job_id");

//Archiving
$archived = "archived";
foreach ($job_com as $job) {
  $days_passed = ($today-strtotime($job['delivered_date']))/$day;
  //If older than 14 days
  if ($days_passed > 14) {
    $stm1->bindParam(':job_id', $job['id']);
    $stm1->bindParam(':status', $archived);
    $stm1->bindParam(':delivered_date', $job['delivered_date']);
    $stm1->execute();
    // print('job_id: '. $job['id'] . " ; name: ". $job['job_name'].'1 day has passed.<br>');
  }
  // print('job_id: '. $job['id'] . " ; name: ". $job['job_name'].'<br>');
}
// print('done run chron job dsc-archive-jobs.php');
?>
<!--enter in url bar when on Triton: https://devwebapp.library.uvic.ca/demo/3dwebapp/jobs_cron/dsc-archive-jobs.php-->