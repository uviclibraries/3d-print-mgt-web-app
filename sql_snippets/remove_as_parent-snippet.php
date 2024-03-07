<?php 
$myId = $job['id']; //value of the current job's, to compare with other job's parents
$myParentId=$job['parent_job_id']; //

//count all jobs that have the same parent as the current job excluding the current job
$a_children = $conn->prepare("SELECT COUNT(*) FROM web_job WHERE 
  parent_job_id = $myId 
  AND id != $myParentId
");
$a_children->execute();
$childCount = $a_children->fetchColumn();
$otherIsParent = $childCount > 0 ? 1 : 0;

//updates parent's 'is_parent' field to 0/false if no other jobs have it assigned as the parent
$p=$conn->prepare("UPDATE web_job SET is_parent = $otherIsParent WHERE id = $myParentId");
$p->execute();
?>
