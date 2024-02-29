<?php

//checks if the job was previously assigned a parent. If true, and if parent changed, loops through user_active_jobs to check if previous parent is still a parent of other jobs. If false, changes prev parent to is_parent=false.
//Run last to allow for the current job's parent_id to have changed in db.

//still_parent is true if job had a parent previously and parent_job_id wasn't set to 0, false if wasn't previously
$still_parent = ($prev_parent_id == $_POST["select_parent"] && $prev_parent_id != 0) ? 1 : 0;

//check if parent-select dropdown has any selected value. If passes, assumes there are other active_user_jobs
if(isset($_POST["select_parent"])){
    //if the dropdown value was changed
    if(intval($_POST["select_parent"]) != $prev_parent_id){

        //loop through active jobs to check if previous_parent_id is parent_job_id of any other jobs still, not including
        foreach ($active_user_jobs as $user_job) {
            if ($user_job['parent_job_id'] == $prev_parent_id){
                $still_parent = 1;
                break;
            }
        }
    }

    $prev_set_isParent = $conn->prepare("UPDATE web_job SET is_parent = $still_parent WHERE id = $prev_parent_id");
    $prev_set_isParent->execute();

}

?>
