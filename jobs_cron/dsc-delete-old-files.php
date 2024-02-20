<?php

//Weekly
chdir("/usr/local/apache2/htdocs-webapp/3dprint/jobs_cron");
require ('../db.php');

//One semester ago & one semester + 2 weeks ago.
$today_120 = date('Ymd', strtotime("-120 days"));
$today_134 = date('Ymd', strtotime("-134 days"));

//Search archived jobs
$stm = $conn->query("SELECT model_name, model_name_2 FROM web_job WHERE status = 'archived' AND (delivered_date BETWEEN $today_134 AND $today_120)");
$job_arh = $stm->fetchAll();

//Deleting files
foreach ($job_arh as $job) {
  $delete = "../uploads/" . $job['model_name'];
  if (is_file($delete)) {
    unlink($delete);
  }
  if ($job["model_name_2"] != NULL) {
    $delete2 = "../uploads/" . $job['model_name_2'];
    if (is_file($delete2)) {
      unlink($delete2);
    }
  }
}

 ?>
