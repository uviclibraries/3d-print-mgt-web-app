<?php //need variable to check if admin wants to send email. case: updating notes but dont send email
  if ($_POST['status'] == "pending payment") {
    $d_priced = $current_date;
    $n_priced=$user;
    $status_email = "pending payment";

    //email user
    if (isset($_POST['email_enabaled']) && $_POST['email_enabaled'] == "enabled") {
      include('../general_partials/send_customer_email_partial');

    }
  } elseif($_POST['status'] == "paid"){
    //this is done automatically when payment is received.
    $d_paid = $current_date;
    $n_paid=$user;

  } elseif($_POST['status'] == "printing"){
    $d_printing = $current_date;
    $n_printing=$user;

  } elseif ($_POST['status'] == "delivered") {
    $d_delivered = $current_date;
    $n_delivered=$user;
    $status_email = "delivered";
    //email user
    if (isset($_POST['email_enabaled']) && $_POST['email_enabaled'] == "enabled") {
      include('../general_partials/send_customer_email_partial');
      
    }
  } elseif($_POST['status'] == "on hold"){
    $d_cancelled = $current_date;
    $n_cancelled = $user;

  } elseif($_POST['status'] == "completed"){
    $d_completed = $current_date;
    $n_completed=$user;

  } elseif($_POST['status'] == "cancelled"){
    $d_cancelled = $current_date;
    $n_cancelled = $user;
  }
  elseif($_POST['status'] == "archived"){
    $d_delivered = $current_date;
    $n_delivered = $user;

  }
?>