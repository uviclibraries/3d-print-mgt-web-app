<?php //Set status details for associated jobs selected from associated jobs table
  if (isset($_POST['checked_jobs'])) {
    $checked_jobs = $_POST['checked_jobs'];
   
    if(count($checked_jobs)>0){
    
      $checked_jobs = array_map(function($item) {
        return "'" . $item . "'";
      }, $checked_jobs);

      $checkedIDs_sql = implode(',', $checked_jobs);//to create comma separated list for update query

      if(isset($_POST['set-statuses-checkbox']) && $_POST['set-statuses-checkbox'] == 'set_statuses'){

        $status_updates = $conn->prepare("UPDATE web_job SET status = :status, priced_date = :priced_date, paid_date = :paid_date, printing_date = :printing_date, completed_date = :completed_date, delivered_date = :delivered_date, priced_signer=:priced_signer,  paid_signer= :paid_signer, printing_signer=:printing_signer, completed_signer=:completed_signer, delivered_signer=:delivered_signer, hold_date = :hold_date, hold_signer= :hold_signer, cancelled_date=:cancelled_date, cancelled_signer = :cancelled_signer WHERE id IN ($checkedIDs_sql)");

        $status_updates->bindParam(':status', $_POST["status"]);

        $status_updates->bindParam(':priced_date', $d_priced);
        $status_updates->bindParam(':paid_date', $d_paid);
        $status_updates->bindParam(':printing_date', $d_printing);
        $status_updates->bindParam(':completed_date', $d_completed);
        $status_updates->bindParam(':hold_date', $d_hold);
        $status_updates->bindParam(':delivered_date', $d_delivered);
        $status_updates->bindParam(':cancelled_date', $d_cancelled);
       
        $status_updates->bindParam(':hold_signer', $n_hold);
        $status_updates->bindParam(':cancelled_signer', $n_cancelled);
        $status_updates->bindParam(':priced_signer', $n_priced);
        $status_updates->bindParam(':paid_signer', $n_paid);
        $status_updates->bindParam(':printing_signer', $n_printing);
        $status_updates->bindParam(':completed_signer', $n_completed);
        $status_updates->bindParam(':delivered_signer', $n_delivered);

        $status_updates->execute();
      }

      if (isset($_POST['set-children-checkbox']) && $_POST['set-children-checkbox'] == 'set_children') {
    
      $query = "UPDATE web_job SET parent_job_id = :parent_job_id WHERE id IN ($checkedIDs_sql) AND is_parent = 0";

        $link_children = $conn->prepare($query);

        // Ensure $job['id'] is defined and valid
        $parent_job_id = $job['id']; // Assigning to a variable is good for clarity
        $link_children->bindParam(':parent_job_id', $parent_job_id, PDO::PARAM_INT);

        $link_children->execute();
      }
    }
  } //end set associated jobs status
?>
