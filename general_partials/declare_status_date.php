<?php
//Sets the date to appear in `echo "Status changed: <br>" .$status_date;`
  $status_date = ""; // To display the date that the current status was set
  $status_signer = "";
  switch ($job['status']) {
    case "submitted":
      $status_date = $job["submission_date"];
      $status_signer = $job_owner["name"];
      break;
    case "on hold":
      $status_date = $job["hold_date"];
      $status_signer = $job["hold_signer"];
      break;
    case "pending payment":
      $status_date = $job["priced_date"];
      $status_signer=$job["priced_signer"];
      break;
    case "paid":
      $status_date = $job["paid_date"];
      $status_signer=$job["paid_signer"];
      break;
    case "printing":
      $status_date = $job["printing_date"];
      $status_signer=$job["printing_signer"];
      break;
    case "completed":
      $status_date = $job["completed_date"];
      $status_signer=$job["completed_signer"];
      break;
    case "delivered":
      $status_date = $job["delivered_date"];
      $status_signer=$job["delivered_signer"];
      break;
    case "cancelled":
      $status_date = $job["cancelled_date"];
      $status_signer = $job["cancelled_signer"];
      break;
    case "archived":
      $status_date = $job["archived_date"];
      $status_signer=$job["archived_signer"];
      break;
  }

  $job['status_date'] = $status_date;
  $job['status_signer'] = $status_signer;
?>