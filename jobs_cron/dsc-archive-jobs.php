<?php
//Daily
chdir("/usr/local/apache2/htdocs-webapp/3dprint/jobs_cron");
require ('../db.php');


//search for all jobs with:
  // current `status` as either 'archived' or 'delivered'
  // where a date has been assigned for either the date they were set to cancelled or date they were set to delivered (`cancelled_date` or `archived_date`) for cautious redundancy of appropriate status set.
  // They were either cancelled or archived (can't be both, or one would be redundant) more than 14 days ago
  // where netlink id is 'chloefarr' <- only for testing
//set all of their `status` values to 'archived'
//set all of their `archived_date` values to date cron_job ran
//set all of their `archived_signer` values to 'cron_job' to indicate it was automatically archived

$stm1 = $conn->query("UPDATE web_job 
  SET 
    status = 'archived', 
    archived_date = CURRENT_DATE, 
    archived_signer = 'cron_job' 
  WHERE 
    status IN ('delivered','cancelled') 
    AND (cancelled_date IS NOT NULL 
      AND (cancelled_date < DATE_ADD(NOW(), INTERVAL -14 DAY)) 
    OR (delivered_date IS NOT NULL 
      AND delivered_date < DATE_ADD(NOW(), INTERVAL -14 DAY)))");

$stm1->execute();

?>
