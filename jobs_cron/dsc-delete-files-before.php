<?php

//Weekly
chdir("/usr/local/apache2/htdocs-webapp/3dprint/jobs_cron");

require ('../db.php');


//Can add job types to each array as new types are built in to webapp
$jobTypes=['3d print', 'laser cut', 'large format print'];
$jobTypeTables=['3d_print_job', 'laser_cut_job', 'large_format_print_job'];
$jobTypeIDs=['3d_print_id', 'laser_cut_id', 'large_format_print_id'];


//One semester ago & one semester + 2 weeks ago.
$today_120 = date('Ymd', strtotime("-120 days"));
$today_134 = date('Ymd', strtotime("-134 days"));

//Search archived jobs
for ($type = 0; $type < count($jobTypes); $type++){
  $table = $jobTypeTables[$type];
  $table_id = $jobTypeIDs[$type];
  $id_on_table = $table .'.'. $table_id;

  $stm = $conn->query("SELECT 
    {$table}.model_name, 
    {$table}.model_name_2 
  FROM 
    web_job 
  INNER JOIN 
    {$table} 
  ON web_job.id = {$id_on_table} 
  WHERE web_job.status = 'archived' 
  AND web_job.delivered_date < '$today_120'");
  $job_arh = $stm->fetchAll();

  // print(count($job_arh)."<br>");

  //Deleting files
  foreach ($job_arh as $job) {
    $delete = "../uploads/" . $job['model_name'];
    // print("job type: " .$jobTypes[$type]. "; file to delete: ".$delete."<br>");
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
}

?>