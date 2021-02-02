<?php
//Daily
chdir("/usr/local/apache2/htdocs-webapp/3dprint/jobs_cron");
require ('../db.php');


//date management numbers
$today_str = date("Y-m-d");
$today =  strtotime($today_str);
$day = 86400;

//pending_payment jobs query
$stm = $conn->query("SELECT print_job.id AS job_id, print_job.job_name AS job_name, print_job.model_name AS model_name, print_job.model_name_2 AS model_name_2, print_job.netlink_id AS netlink_id, print_job.status AS status, print_job.priced_date AS priced_date, users.email AS email, users.name AS user_name FROM print_job INNER JOIN users on users.netlink_id = print_job.netlink_id WHERE print_job.status = 'pending payment' ORDER BY priced_date ASC");
$job_pp = $stm->fetchAll();

//Updating database preperation
$stm1 = $conn->prepare("UPDATE print_job SET status = :status, completed_date = :completed_date WHERE id = :job_id");

//pending payment reminder & Cancelations
$cancelled = "cancelled";
foreach ($job_pp as $job) {
  $days_passed = ($today-strtotime($job['priced_date']))/$day;

  if ($days_passed > 15) {
    $stm1->bindParam(':job_id', $job['job_id']);
    $stm1->bindParam(':status', $cancelled);
    $stm1->bindParam(':completed_date', $today_str);
    $stm1->execute();

    //deleting 3d file
    $delete = "../uploads/" . $job['model_name'];
    if(is_file($delete)){
      unlink($delete);
    }
    //check if secondary file exists. If so delete
    if ($job["model_name_2"] != NULL) {
      $delete2 = "../uploads/" . $job['model_name_2'];
      if (is_file($delete2)) {
        unlink($delete2);
      }
    }
  }
