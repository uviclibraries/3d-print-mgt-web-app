<?php
//Daily
chdir("/usr/local/apache2/htdocs-webapp/3dprint/jobs_cron");
require ('../db.php');


//date management numbers
$today_str = date("Y-m-d");
$today =  strtotime($today_str);
$day = 86400;

//pending_payment jobs query
$stm = $conn->query("SELECT web_job.id AS job_id, web_job.job_name AS job_name, web_job.model_name AS model_name, web_job.model_name_2 AS model_name_2, web_job.netlink_id AS netlink_id, web_job.status AS status, web_job.priced_date AS priced_date, users.email AS email, users.name AS user_name FROM web_job INNER JOIN users on users.netlink_id = web_job.netlink_id WHERE web_job.status = 'pending payment' ORDER BY priced_date ASC");
$job_pp = $stm->fetchAll();

//Updating database preperation
$stm1 = $conn->prepare("UPDATE web_job SET status = :status, delivered_date = :delivered_date WHERE id = :job_id");

//pending payment reminder & Cancelations
$cancelled = "cancelled";
foreach ($job_pp as $job) {
  $days_passed = ($today-strtotime($job['priced_date']))/$day;

  if ($days_passed > 15) {
    $stm1->bindParam(':job_id', $job['job_id']);
    $stm1->bindParam(':status', $cancelled);
    $stm1->bindParam(':delivered_date', $today_str);
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
}
