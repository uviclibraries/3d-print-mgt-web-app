<?php

$typeID="";
$typeTableName="";
switch($jobType){
  case "3d print":
    $typeID = '3d_print_id';
    $typeTableName = '3d_print_job';
    break;
  case "laser cut":
    $typeID = 'laser_cut_id';
    $typeTableName = 'laser_cut_job';
    break;
  case "large format print":
    $typeID = 'large_format_print_id';
    $typeTableName = 'large_format_print_job';
    break;
}

$stm="";
if($jobType != 'large format print'){
//get list of active jobs associated with the job's owner
$stm = $conn->prepare("SELECT web_job.id AS id, web_job.job_name AS name, web_job.status AS status, web_job.submission_date AS submission_date, web_job.priced_date AS priced_date, web_job.paid_date AS paid_date,web_job.printing_date AS printing_date,web_job.completed_date AS completed_date,web_job.delivered_date AS delivered_date,web_job.hold_date AS hold_date, web_job.archived_date AS archived_date, web_job.hold_signer AS hold_signer,web_job.cancelled_signer AS cancelled_signer, web_job.priced_signer AS priced_signer, web_job.paid_signer AS paid_signer, web_job.printing_signer AS printing_signer, web_job.completed_signer AS completed_signer, web_job.delivered_signer AS delivered_signer, web_job.archived_signer AS archived_signer, web_job.job_purpose AS job_purpose, web_job.academic_code AS academic_code, web_job.course_due_date AS course_due_date, $typeTableName.duration AS duration, web_job.parent_job_id AS parent_job_id , web_job.is_parent AS is_parent FROM web_job INNER JOIN $typeTableName ON web_job.id=$typeTableName.$typeID WHERE web_job.status NOT IN ('delivered', 'archived', 'cancelled') AND web_job.netlink_id = :netlink_id");
}else{
  $stm = $conn->prepare("SELECT web_job.id AS id, web_job.job_name AS name, web_job.status AS status, web_job.submission_date AS submission_date, web_job.priced_date AS priced_date, web_job.paid_date AS paid_date,web_job.printing_date AS printing_date,web_job.completed_date AS completed_date,web_job.delivered_date AS delivered_date,web_job.hold_date AS hold_date, web_job.archived_date AS archived_date, web_job.hold_signer AS hold_signer,web_job.cancelled_signer AS cancelled_signer, web_job.priced_signer AS priced_signer, web_job.paid_signer AS paid_signer, web_job.printing_signer AS printing_signer, web_job.completed_signer AS completed_signer, web_job.delivered_signer AS delivered_signer, web_job.archived_signer AS archived_signer,  web_job.job_purpose AS job_purpose, web_job.academic_code AS academic_code, web_job.course_due_date AS course_due_date, web_job.parent_job_id AS parent_job_id , web_job.is_parent AS is_parent FROM web_job INNER JOIN $typeTableName ON web_job.id=$typeTableName.$typeID WHERE web_job.status NOT IN ('delivered', 'archived', 'cancelled') AND web_job.netlink_id = :netlink_id");
}

  $stm->bindParam(':netlink_id', $job['netlink_id']);
  $stm->execute();
  $user_web_jobs = $stm->fetchAll();


  $parent=$job; //set self as parent if no other job has been assigned to this job as the parent.

  //will be used in admin_update_multiple_status_snippet.php to find the previous parent job, check if it's still a parent if different job selected in <select id="select_parent" name="select_parent">
  $prev_parent_id = $job['parent_job_id'];
  // echo('parent job id: '. $job['parent_job_id']."<br>");
  // echo ('my id: '. $job['id'].'<br>');
  $active_user_jobs = [];

  foreach ($user_web_jobs as $related_job) {
    if($related_job['id'] != $job['id']){
      array_push($active_user_jobs, $related_job);
    }
  }


$stm = $conn->prepare("SELECT web_job.id AS id, web_job.job_name AS name, web_job.status AS status, web_job.parent_job_id AS parent_job_id , web_job.is_parent AS is_parent FROM web_job INNER JOIN $typeTableName ON id = $typeID WHERE 
  status NOT IN ('delivered', 'archived', 'cancelled') 
  AND netlink_id = 'chloefarr' 
  AND id != {$job['id']}
  AND (
    parent_job_id = {$job['id']} OR 
    id = {$job['parent_job_id']} OR 
    (parent_job_id != 0 AND parent_job_id = {$job['parent_job_id']}) 
  )
");
$stm->execute();
$user_linked_jobs = $stm->fetchAll();

// echo ('count stm linked: '. count($user_linked_jobs)."<br>");

$linked_jobs = [];
foreach ($user_linked_jobs as $related_job) {
    if($related_job['id'] != $job['id']){
      array_push($linked_jobs, $related_job);
    }
  }

// if($job['parent_job_id']!= 0){
//   $stm1 = $conn->prepare("SELECT web_job.id AS id, web_job.job_name AS name, web_job.status AS status, web_job.parent_job_id AS parent_job_id , web_job.is_parent AS is_parent WHERE id = {$job['parent_job_id']}");
//   $stm1->execute();
//   $parent = $stm1->fetch();
// }
  // echo('parent job id: ' . $parent['id']. ' ; active jobs: ' . count($active_user_jobs) . " ; linked jobs: " . count($linked_jobs));
  $bundled = $active_user_jobs ? true : false; //user has other active jobs
?>