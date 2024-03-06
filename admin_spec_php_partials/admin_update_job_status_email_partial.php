<?php 

$faq_href ="";
$job_href ="";
$library_href= "https://www.uvic.ca/library/";
switch($jobType){
  case ("3d print"):
    $faq_href = 'https://onlineacademiccommunity.uvic.ca/dsc/how-to-3d-print/';
    $job_href = 'customer-3d-job-information?job_id=';
    break;
  case("laser cut"):
    $faq_href = 'https://onlineacademiccommunity.uvic.ca/dsc/how-to-laser-cut/';
    $job_href = 'customer-laser-job-information.php?job_id=';
    break;
  case("large format print"):
    $faq_href = 'https://onlineacademiccommunity.uvic.ca/dsc/tools-tech/large-format-printer-and-scanner/';
    $job_href = 'customer-large-format-print-job-information?job_id=';
    break;
}
$job_href = $job_href.$job['id'];

//Get user's name and email
$userSQL = $conn->prepare("SELECT * FROM users WHERE netlink_id = :netlink_id");
$userSQL->bindParam(':netlink_id', $job['netlink_id']);
$userSQL->execute();
$job_owner = $userSQL->fetch();

      
  if ($_POST['status'] == "pending payment") {
    $d_priced = $current_date;
    $n_priced=$user;
    // $status_email = "pending payment";

    //email user
    if (isset($_POST['email_enabaled']) && $_POST['email_enabaled'] == "enabled") {
      // include('../general_partials/send_customer_email_partial');
      $msg = "
        <html>
        <head>
        <title>HTML email</title>
        </head>
        <body>
        <p> Hello, ". $job_owner['name'] .". This is an automated email from the DSC. </p>
        <p> Your ".$jobType." job (".$job['job_name']. ") has been evaluated at a cost of $" . (number_format((float)$_POST["price"], 2, '.','')) . " </p>
        <p> Please make your payment <a href=". $job_href .">here</a> for it to be placed in our printing queue.</p>
        <p>If you have any questions please review our <a href=". $faq_href .">FAQ</a> or email us at ".$dsc_email. ".</p>
        </body>
        </html>";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: dscommons@uvic.ca" .  "\r\n";
        mail($job_owner['email'],"Your ".$jobType." is ready for payment",$msg,$headers);
    }

  } elseif($_POST['status'] == "paid"){
    //this is done automatically when payment is received.
    $d_paid = $current_date;
    $n_paid=$user;
    if(!$job['priced_date']){
      $d_priced = $current_date;
      $n_priced = $user;
    }

  } elseif($_POST['status'] == "printing"){
    $d_printing = $current_date;
    $n_printing=$user;

  } elseif ($_POST['status'] == "delivered") {
    $d_delivered = $current_date;
    $n_delivered=$user;

    if(!$job['printing_date']){
      $d_printing = $current_date;
      $n_printing = $user;
    }
    if(!$job['completed_date']){
      $d_completed = $current_date;
      $n_completed = $user;
    }
    // $status_email = "delivered";
    //email user
    if (isset($_POST['email_enabaled']) && $_POST['email_enabaled'] == "enabled") {
      // include('../general_partials/send_customer_email_partial');
      $msg = "
      <html>
      <head>
      <title>HTML email</title>
      </head>
      <body>
      <p>Hello, ". $job_owner['name'] .". This is an automated email from the DSC. </p>
      <p> Your ".$jobType." job (".$job['job_name']. ") has been completed. You can pick it up from the front desk at the McPherson Library.</p>
      <p>Please check up to date library hours by checking the library website <a href=". $library_href .">here</a></p>
      </body>
      </html>";
      $headers = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers .= "From: dscommons@uvic.ca" .  "\r\n";
      mail($job_owner['email'], "Your ".$jobType." is ready for collection",$msg,$headers);
    }

  } elseif($_POST['status'] == "on hold"){
    $d_cancelled = $current_date;
    $n_cancelled = $user;

  } elseif($_POST['status'] == "completed"){
    $d_completed = $current_date;
    $n_completed=$user;

    if(!$job['printing_date']){
      $d_printing = $current_date;
      $n_printing = $user;
    }

  } elseif($_POST['status'] == "cancelled"){
    $d_cancelled = $current_date;
    $n_cancelled = $user;
  }
  elseif($_POST['status'] == "archived"){
    $d_archived = $current_date;
    $n_archived = $user;

    if(!$job['completed_date']){
      $d_completed = $current_date;
      $n_completed = $user;
    }
    if(!$job['delivered_date']){
      $d_delivered = $current_date;
      $n_delivered = $user;
    }
  }
?>