<?php 
  $isParent = $job['is_parent'];

  $stmt->bindParam(':job_id', $job['id']);

  $price = floatval(number_format((float)$_POST["price"], 2, '.',''));
  $stmt->bindParam(':price', $price);
  
  $stmt->bindParam(':staff_notes', $_POST["staff_notes"]);

  $copies = intval($_POST["copies"]);
  $stmt->bindParam(':copies', $copies , PDO::PARAM_INT);
  
  $stmt->bindParam(':model_name_2', $modify_value);

  $new_status = isset($_POST["status"]) ? $_POST["status"] : $job['status'];
  $stmt->bindParam(':status', $new_status, PDO:: PARAM_STR);
  

  $new_parent_id = $job['parent_job_id'];
  if($_POST["status"] = "cancelled" && $job['parent_job_id']!=0){
    $new_parent_id = 0;
    include('remove_as_parent-snippet.php');
  }
  $stmt->bindParam(':parent_job_id', $new_parent_id, PDO:: PARAM_INT);


  if($jobType == "laser cut" || $jobType == "3d print"){
    $stmt->bindParam(':material_type', $_POST["material_type"]);
    $duration = intval($_POST["duration"]);  
    $stmt->bindParam(':duration', $duration , PDO::PARAM_INT);
  }

  if(isset($_POST['checked_jobs']) && $_POST['checked_jobs'] && count($_POST['checked_jobs'])>0){

    //if job(s) was selected from within any <div class="user_jobs_container"> and 'set selected jobs as children' checkbox selected, change "is_parent" field val to true
    if(isset($_POST['set-children-checkbox']) && $_POST['set-children-checkbox'] == 'set_children'){
      $isParent = true;
    }

    //if job(s) was selected from within any <div class="user_jobs_container"> and 'unlink selected jobs as children' checkbox selected, loop through linked jobs and compare against checked jobs. if any remain whose parent id is the current job's id, change "is_parent" field val to true
    elseif(isset($_POST['unlink-children-checkbox']) && $_POST['unlink-children-checkbox'] == 'unlink_children'){
      forEach ($linked_jobs as $linked) {
        //if the previously linked job's parent id is set to current job, and isn't in the list of jobs set to be unlinked
        if($linked['parent_job_id'] == $job['id'] && !in_array($linked['id'], $_POST['checked_jobs'])) {
            $isParent = true;
            break;
        }
        else{$isParent = false;}
      }
    }
  }

  $stmt->bindParam(':is_parent', $isParent, PDO:: PARAM_BOOL);

  /*
  should dates be removed if steps are reverted: eg printing->paid
    not yet implemented
  */

  $stmt->bindParam(':priced_date', $d_priced);
  $stmt->bindParam(':paid_date', $d_paid);
  $stmt->bindParam(':printing_date', $d_printing);
  $stmt->bindParam(':completed_date', $d_completed);
  $stmt->bindParam(':delivered_date', $d_delivered);
  $stmt->bindParam(':cancelled_date', $d_cancelled);
  $stmt->bindParam(':hold_date', $d_hold);
  $stmt->bindParam(':archived_date',$d_archived);

  $stmt->bindParam(':priced_signer', $n_priced);
  $stmt->bindParam(':paid_signer', $n_paid);
  $stmt->bindParam(':printing_signer', $n_printing);
  $stmt->bindParam(':completed_signer', $n_completed);
  $stmt->bindParam(':delivered_signer', $n_delivered);
  $stmt->bindParam(':cancelled_signer', $n_cancelled);
  $stmt->bindParam(':hold_signer', $n_hold);
  $stmt->bindParam(':archived_signer',$n_archived);
  
?>