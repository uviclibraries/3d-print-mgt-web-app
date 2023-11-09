<?php
//Daily
chdir("/usr/local/apache2/htdocs-webapp/3dprint/jobs_cron");
require ('../db.php');


//date management numbers
$today_str = date("Y-m-d");
$today =  strtotime($today_str);
$day = 86400;

//Completed jobs query
$stm = $conn->query("SELECT id, completed_date FROM print_job WHERE status = 'completed'");
$job_com = $stm->fetchAll();

//Updating database preperation
$stm1 = $conn->prepare("UPDATE print_job SET status = :status, completed_date = :completed_date WHERE id = :job_id");

//Archiving
$archived = "archived";
foreach ($job_com as $job) {
  $days_passed = ($today-strtotime($job['completed_date']))/$day;
  //If older than 14 days
  if ($days_passed > 14) {
    $stm1->bindParam(':job_id', $job['id']);
    $stm1->bindParam(':status', $archived);
    $stm1->bindParam(':completed_date', $job['completed_date']);
    $stm1->execute();
  }
}
?>
