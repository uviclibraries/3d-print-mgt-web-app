<?php 

$faq_href ="";
$job_href ="";
$library_href= "https://www.uvic.ca/library/locations/home/mearns/#calendar";
$test_domain = "https://devwebapp.library.uvic.ca/demo/3dwebapp/";
$production_domain = "https://webapp.library.uvic.ca/3dprint/";
$dsc_map_href="https://onlineacademiccommunity.uvic.ca/dsc/hours-and-location/";
switch($jobType){
  case ("3d print"):
    $faq_href = 'https://onlineacademiccommunity.uvic.ca/dsc/how-to-3d-print/';
    $job_href = 'customer-3d-job-information.php?job_id=';
    break;
  case("laser cut"):
    $faq_href = 'https://onlineacademiccommunity.uvic.ca/dsc/how-to-laser-cut/';
    $job_href = 'customer-laser-job-information.php?job_id=';
    break;
  case("large format print"):
    $faq_href = 'https://onlineacademiccommunity.uvic.ca/dsc/tools-tech/large-format-printer-and-scanner/';
    $job_href = 'customer-large-format-print-job-information.php?job_id=';
    break;
}
$job_href = $production_domain.$job_href.$job['id'];

$current_date = date("Y-m-d");
$empty_date = '0000-00-00';
//Get user's name and email
$userSQL = $conn->prepare("SELECT * FROM users WHERE netlink_id = :netlink_id");
$userSQL->bindParam(':netlink_id', $job['netlink_id']);
$userSQL->execute();
$job_owner = $userSQL->fetch();

      
  if ($_POST['status'] == "pending payment" && $_POST["price"] > 0) {
    $d_priced = $current_date;
    $n_priced = $user;
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
        <p>Hello, ". $job_owner['name'] .".</p>
        <p>This is an automated email from the DSC.</p>
        <p>Your ".$jobType." job (".$job['job_name']. ") has been evaluated at a cost of $" . (number_format((float)$_POST["price"], 2, '.','')) . ".</p>
        <p>Please make your payment <a href=". $job_href .">here</a> for it to be placed in our printing queue.</p>
        <p>If you have any questions please review our <a href=". $faq_href .">FAQ</a> or email us at <a href='mailto:dscommons@uvic.ca'>dscommons@uvic.ca</a>.</p>
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

    if(!$job['priced_date'] || $job['priced_date'] == $empty_date){
      $d_priced = $current_date;
      $n_priced = $user;
    }

  } elseif($_POST['status'] == "printing"){

    $d_printing = $current_date;
    $n_printing= $user;

  } elseif ($_POST['status'] == "delivered") {
    $d_delivered = $current_date;
    $n_delivered=$user;

      if(!$job['printing_date'] || $job['printing_date'] == $empty_date){
        $d_printing = $current_date;
        $n_printing = $user;
      }
      if(!$job['completed_date'] || $job['completed_date'] == $empty_date){
        $d_completed = $current_date;
        $n_completed = $user;
      }
      // $status_email = "delivered";
      //email user
      if (isset($_POST['email_enabaled']) && $_POST['email_enabaled'] == "enabled") {
          if($jobType === "large format print"){// include('../general_partials/send_customer_email_partial');
            $msg = "
            <html>
            <head>
            <title>HTML email</title>
            </head>
            <body>
            <p>Hello, ". $job_owner['name'] .".</p>
            <p>This is an automated email from the DSC.</p>
            <p>Your ".$jobType." job (".$job['job_name']. ") has been completed. You can pick it up from the Digital Scholarship Commons. It is on the DSC welcome desk and labeled with your name.</p>
            <p>The Digital Scholarship Commons is located on the 3rd floor of the Mearns Centre - McPherson Library, at room A308. Directions are available <a href=". $dsc_map_href .">here.</a></p>
            <p>Please check up to date library hours by checking the library website <a href=". $library_href .">here</a></p>          
            </body>
            </html>";
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: dscommons@uvic.ca" .  "\r\n";
            mail($job_owner['email'], "Your ".$jobType." is ready for collection",$msg,$headers);
          }
          else{
            $msg = "
              <html>
              <head>
              <title>HTML email</title>
              </head>
              <body>
              <p>Hello, ". $job_owner['name'] .".</p>
              <p>This is an automated email from the DSC.</p>
              <p>Your ".$jobType." job (".$job['job_name']. ") has been completed. You can pick it up from the front desk at the Mearns Centre - McPherson Library.</p>
              <p>Please check up to date library hours by checking the library website <a href=". $library_href .">here.</a></p>
              </body>
              </html>";
              $headers = "MIME-Version: 1.0" . "\r\n";
              $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
              $headers .= "From: dscommons@uvic.ca" .  "\r\n";
              mail($job_owner['email'], "Your ".$jobType." is ready for collection",$msg,$headers);
          }
        }

  } elseif($_POST['status'] == "on hold"){
    $d_hold = $current_date;
    $n_hold = $user;


  } elseif($_POST['status'] == "completed"){
    $d_completed = $current_date;
    $n_completed=$user;

    if(!$job['printing_date'] || $job['printing_date'] == $empty_date){
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

    if(!$job['completed_date'] || $job['completed_date'] == $empty_date){
      $d_completed = $current_date;
      $n_completed = $user;
    }
    if(!$job['delivered_date'] || $job['delivered_date'] == $empty_date){
      $d_delivered = $current_date;
      $n_delivered = $user;
    }
  }
?>