<?php

//Weekly
require ('db.php');

//One semester ago & one semester + 2 weeks ago.
$today_120 = date("Y-m-d", strtotime(-120 days));
$today_134 = date("Y-m-d", strtotime(-134 days));

//Archived jobs
$stm = $conn->query("SELECT model_name, model_name_2 FROM print_job WHERE status = 'archived' AND completed_date <= $today_120 AND completed_date >= $today_134");
$job_arh = $stm->fetchAll();
foreach ($job_arh as $job) {
  $delete = "uploads/" . $job['model_name'];

  if (is_file($delete)) {
    unlink($delete);
  }
  if ($job["model_name_2"] != NULL) {
    $delete2 = "uploads/" . $job['model_name_2'];
    if (is_file($delete2)) {
      unlink($delete2);
    }
  }
}

 ?>
