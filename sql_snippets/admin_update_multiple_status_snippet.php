<?php //Set status details for associated jobs selected from associated jobs table
  if (isset($_POST['checked_jobs'])) {
    $checked_jobs = $_POST['checked_jobs'];
   
    if(count($checked_jobs)>0){
    
      $checked_jobs = array_map(function($item) {
        return "'" . $item . "'";
      }, $checked_jobs);

      $checkedIDs_sql = implode(',', $checked_jobs);//to create comma separated list for update query

      $stm = $conn->prepare("UPDATE web_job INNER JOIN laser_cut_job ON id=laser_cut_id SET status = :status, priced_date = :priced_date, paid_date = :paid_date, printing_date = :printing_date, completed_date = :completed_date, delivered_date = :delivered_date, priced_signer=:priced_signer,  paid_signer= :paid_signer, printing_signer=:printing_signer, completed_signer=:completed_signer, delivered_signer=:delivered_signer, hold_date = :hold_date, hold_signer= :hold_signer, cancelled_date=:cancelled_date, cancelled_signer = :cancelled_signer WHERE id IN ($checkedIDs_sql)");

      $stm->bindParam(':status', $_POST["status"]);

      $stm->bindParam(':priced_date', $d_priced);
      $stm->bindParam(':paid_date', $d_paid);
      $stm->bindParam(':printing_date', $d_printing);
      $stm->bindParam(':completed_date', $d_completed);
      $stm->bindParam(':hold_date', $d_hold);
      $stm->bindParam(':delivered_date', $d_delivered);
      $stm->bindParam(':cancelled_date', $d_cancelled);
     
      $stm->bindParam(':hold_signer', $n_hold);
      $stm->bindParam(':cancelled_signer', $n_cancelled);
      $stm->bindParam(':priced_signer', $n_priced);
      $stm->bindParam(':paid_signer', $n_paid);
      $stm->bindParam(':printing_signer', $n_printing);
      $stm->bindParam(':completed_signer', $n_completed);
      $stm->bindParam(':delivered_signer', $n_delivered);

      $stm->execute();
    }
  } //end set associated jobs status
?>