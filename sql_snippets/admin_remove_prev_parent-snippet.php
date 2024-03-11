<?php

//checks if the job was previously assigned a parent. If true, and if parent changed, loops through user_active_jobs to check if previous parent is still a parent of other jobs. If false, changes prev parent to is_parent=false.
//Run last to allow for the current job's parent_id to have changed in db.

//still_parent is true if job had a parent previously and parent_job_id wasn't set to 0, false if wasn't previously
$n_parent = false;
if(isset($_POST['checked_jobs']) && $_POST['checked_jobs'] && count($_POST['checked_jobs'])>0){
    $checked_jobs = $_POST['checked_jobs'];
    $compare_linked = array_diff($linked_jobs, $checked_jobs);

    forEach ($compare_linked as $linked) {
        if($linked['parent_job_id']){
            $n_parent = true;
            break;
        }
        else{$n_parent = false;}
    }

    //check if unlink checkbox is visible and was checked
    if(!$n_parent){
        $parent = $prev_parent_id;
        $set_isParent = $conn->prepare("UPDATE web_job SET is_parent = FALSE WHERE id = {$parent}");
        $set_isParent->execute();

    }
}

?>
