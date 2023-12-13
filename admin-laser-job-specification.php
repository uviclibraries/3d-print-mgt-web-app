<?php
session_start();
require ('auth-sec.php'); //Gets CAS & db
//auth-sec includes: $user, $user_email, $user_type, $user_name
//Is user Admin check
if ($user_type == 1) {
  header("Location: customer-dashboard.php");
  die();
}

$stm = $conn->prepare("SELECT * FROM web_job INNER JOIN laser_cut_job ON id=laser_cut_id WHERE id=?");
$stm->execute([$_GET["job_id"]]);
$job=$stm->fetch();
// echo $job['job_name']; 

//Get users name & email
$userSQL = $conn->prepare("SELECT * FROM users WHERE netlink_id = :netlink_id");
$userSQL->bindParam(':netlink_id', $job['netlink_id']);
$userSQL->execute();
$job_owner = $userSQL->fetch();

//get list of active jobs associated with the job's owner
$stm = $conn->prepare("SELECT web_job.id AS id, web_job.job_name AS name, web_job.status AS status, web_job.submission_date AS submission_date, web_job.priced_date AS priced_date, web_job.paid_date AS paid_date,web_job.printing_date AS printing_date,web_job.completed_date AS completed_date,web_job.delivered_date AS delivered_date,web_job.hold_date AS hold_date,web_job.hold_signer AS hold_signer,web_job.cancelled_signer AS cancelled_signer, web_job.priced_signer AS priced_signer, web_job.paid_signer AS paid_signer, web_job.printing_signer AS printing_signer, web_job.completed_signer AS completed_signer, web_job.delivered_signer AS delivered_signer, web_job.job_purpose AS job_purpose, web_job.academic_code AS academic_code FROM web_job INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status NOT IN ('delivered', 'archived', 'cancelled') AND web_job.netlink_id = :netlink_id");
  $stm->bindParam(':netlink_id', $job['netlink_id']);
  $stm->execute();
  $user_web_jobs = $stm->fetchAll();

  $active_user_jobs = [];
  foreach ($user_web_jobs as $related_job) {
    $active_user_jobs[] = $related_job;
  }
  // echo 'row 35 active user jobs: ';
  // print_r(count($active_user_jobs));
  // echo '<br>';
    // print_r($active_user_jobs);

  // echo '<br>';
    

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // echo 'method is post<br>';
  //used if modify is not updated.
  $modify_value = $job["model_name_2"];
  if (isset($_FILES["modify"]['name'])) {
    // Check $_FILES["3d_model"]['error'] value.
    switch ($_FILES["modify"]['error']) {
        case UPLOAD_ERR_OK:
          $file_name = $_FILES["modify"]['name'];
          $file_array = explode(".",$file_name);
          $ext = end($file_array);
          $modify_value = "job" . $job['id'] . "_modify." .$ext;
          $savefilename = sprintf('./uploads/%s', $modify_value,);
          if (is_file($savefilename)) {
            unlink($savefilename);
          }
          if (!move_uploaded_file($_FILES["modify"]['tmp_name'], $savefilename)) {
              throw new RuntimeException('Failed to move uploaded file.');
            }
        case UPLOAD_ERR_NO_FILE:
            break;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown errors.');
    }
  }


  $stmt = $conn->prepare("UPDATE web_job INNER JOIN laser_cut_job ON id=laser_cut_id SET price = :price, copies=:copies, material_type = :material_type, staff_notes = :staff_notes, status = :status, priced_date = :priced_date,  paid_date = :paid_date, printing_date = :printing_date, completed_date = :completed_date,cancelled_date = :cancelled_date, delivered_date = :delivered_date, priced_signer=:priced_signer,  paid_signer= :paid_signer, printing_signer=:printing_signer, completed_signer=:completed_signer, delivered_signer=:delivered_signer,hold_date = :hold_date, hold_signer= :hold_signer, cancelled_signer = :cancelled_signer, model_name_2 =:model_name_2 WHERE id = :job_id");
  
  $current_date = date("Y-m-d");

  $stmt->bindParam(':job_id', $job['id']);
  $price = floatval(number_format((float)$_POST["price"], 2, '.',''));
  $stmt->bindParam(':price', $price);
  $copies = intval($_POST["copies"]);
  $stmt->bindParam(':copies', $copies , PDO::PARAM_INT);
  $stmt->bindParam(':material_type', $_POST["material_type"]);
  $stmt->bindParam(':staff_notes', $_POST["staff_notes"]);
  $stmt->bindParam(':status', $_POST["status"]);
  $stmt->bindParam(':model_name_2', $modify_value);
  /*
  should dates be removed if steps are reverted: eg printing->paid
  */
  $d1 = $job['priced_date'];
  $d2 = $job['paid_date'];
  $d3 = $job['printing_date'];
  $d4 = $job['delivered_date'];
  $d5 = $job['hold_date'];
  $d6 = $job['completed_date'];
  $d7 = $job['cancelled_date'];

  $stmt->bindParam(':priced_date', $d1);
  $stmt->bindParam(':paid_date', $d2);
  $stmt->bindParam(':printing_date', $d3);
  $stmt->bindParam(':delivered_date', $d4);
  $stmt->bindParam(':hold_date', $d5);
  $stmt->bindParam(':completed_date', $d6);
  $stmt->bindParam(':cancelled_date', $d7);

  $hs = $job['hold_signer'];
  $cs = $job['cancelled_signer'];
  $priceds = $job['priced_signer'];
  $paids = $job['paid_signer'];
  $printings = $job['printing_signer'];
  $completes = $job['completed_signer'];
  $ds = $job['delivered_signer'];

  $stmt->bindParam(':hold_signer', $hs);
  $stmt->bindParam(':cancelled_signer', $cs);
  $stmt->bindParam(':priced_signer', $priceds);
  $stmt->bindParam(':paid_signer', $paids);
  $stmt->bindParam(':printing_signer', $printings);
  $stmt->bindParam(':completed_signer', $completes);
  $stmt->bindParam(':delivered_signer', $ds);

  //need variable to check if admin wants to send email. case: updating notes but dont send email
  if ($_POST['status'] == "pending payment") {
    $d1 = $current_date;

    //email user
    if (isset($_POST['email_enabaled']) && $_POST['email_enabaled'] == "enabled") {
      //get job owner details
      // $userSQL = $conn->prepare("SELECT * FROM users WHERE netlink_id = :netlink_id");
      // $userSQL->bindParam(':netlink_id', $job['netlink_id']);
      // $userSQL->execute();
      // $job_owner = $userSQL->fetch();
      $direct_link = "https://webapp.library.uvic.ca/3dprint/customer-laser-job-information.php?job_id=". $job['id'];
      $direct_link2 = "https://onlineacademiccommunity.uvic.ca/dsc/how-to-laser-cut/";
      $msg = "
      <html>
      <head>
      <title>HTML email</title>
      </head>
      <body>
      <p> Hello, ". $job_owner['name'] .". This is an automated email from the DSC. </p>
      <p> Your laser cutting job; " . $job['job_name'] . " has been evaluated at a cost of $" . (number_format((float)$_POST["price"], 2, '.','')) . " </p>
      <p> Please make your payment <a href=". $direct_link .">here</a> for it to be placed in our printing queue.</p>
      <p>If you have any questions please review our <a href=". $direct_link2 .">FAQ</a> or email us at dscommons@uvic.ca.</p>
      </body>
      </html>";
      $headers = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers .= "From: dscommons@uvic.ca" . "\r\n";
      mail($job_owner['email'],"Your laser cut is ready for payment",$msg,$headers);
    }
  } elseif($_POST['status'] == "paid"){
    //this is done automatically when payment is received.
    $d2 = $current_date;

  } elseif($_POST['status'] == "printing"){
    $d3 = $current_date;

  } elseif ($_POST['status'] == "delivered" OR $_POST['status'] == "archived") {
    $d4 = $current_date;

    //email user
    if (isset($_POST['email_enabaled']) && $_POST['email_enabaled'] == "enabled") {
      //Get users name & email
      $userSQL = $conn->prepare("SELECT * FROM users WHERE netlink_id = :netlink_id");
      $userSQL->bindParam(':netlink_id', $job['netlink_id']);
      $userSQL->execute();
      $job_owner = $userSQL->fetch();
      $direct_link = "https://www.uvic.ca/library/";

      $msg = "
      <html>
      <head>
      <title>HTML email</title>
      </head>
      <body>
      <p>Hello, ". $job_owner['name'] .". This is an automated email from the DSC. </p>
      <p> Your laser cutting job; " . $job['job_name'] . " has been completed. You can pick it up from the front desk at the McPherson Library.</p>
      <p>Please check up to date library hours and safety guidelines by checking the library website <a href=". $direct_link .">here</a></p>
      </body>
      </html>";
      $headers = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers .= "From: dscommons@uvic.ca" . "\r\n";
      mail($job_owner['email'], "Your laser cut is ready for collection",$msg,$headers);
    }
  } elseif($_POST['status'] == "on hold"){
    $d5 = $current_date;
    $hs = $user;

  } elseif($_POST['status'] == "completed"){
    $d6 = $current_date;

  } elseif($_POST['status'] == "cancelled"){
    $d7 = $current_date;
    $cs = $user;
  }

  $stmt->execute();

  
//Set status details for associated jobs selected from associated jobs table
  print_r($_POST);
  if (isset($_POST['checked_jobs'])) {
    $checked_jobs = $_POST['checked_jobs'];
    // echo 'line190 checked jobs exists';
    // print_r($checked_jobs);echo '<br>';

    if(count($checked_jobs)>0){
    
      $checked_jobs = array_map(function($item) {
        return "'" . $item . "'";
      }, $checked_jobs);
      // print_r($checked_jobs);

      $checkedIDs_sql = implode(',', $checked_jobs);//to create comma separated list for update query
      // echo $checkedIDs_sql;

      $stm = $conn->prepare("UPDATE web_job INNER JOIN laser_cut_job ON id=laser_cut_id SET status = :status, priced_date = :priced_date, paid_date = :paid_date, printing_date = :printing_date, completed_date = :completed_date, delivered_date = :delivered_date, priced_signer=:priced_signer,  paid_signer= :paid_signer, printing_signer=:printing_signer, completed_signer=:completed_signer, delivered_signer=:delivered_signer, hold_date = :hold_date, hold_signer= :hold_signer, cancelled_date=:cancelled_date, cancelled_signer = :cancelled_signer WHERE id IN ($checkedIDs_sql)");

      $stm->bindParam(':status', $_POST["status"]);

      $stm->bindParam(':priced_date', $d1);
      $stm->bindParam(':paid_date', $d2);
      $stm->bindParam(':printing_date', $d3);
      $stm->bindParam(':completed_date', $d6);
      $stm->bindParam(':hold_date', $d5);
      $stm->bindParam(':delivered_date', $d4);
      $stm->bindParam(':cancelled_date', $d7);
     
      $stm->bindParam(':hold_signer', $hs);
      $stm->bindParam(':cancelled_signer', $cs);
      $stm->bindParam(':priced_signer', $priceds);
      $stm->bindParam(':paid_signer', $paids);
      $stm->bindParam(':printing_signer', $printings);
      $stm->bindParam(':completed_signer', $completes);
      $stm->bindParam(':delivered_signer', $ds);

      $stm->execute();
    }
  } else {
  // echo 'no checked jobs';
  }//end set associated jobs status
  //exit to dashboard after saving
  header("location: admin-dashboard.php");
}//end form execution



  

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
      $status_signer=$job["completed_signer"];
      break;
  }

  $job['status_date'] = $status_date;
  $job['status_signer'] = $status_signer;

?>



<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v4.0.1">
    <title>Job id: <?php echo $job["id"] ?></title>

    <!--header link-->
    <link rel="stylesheet" href="css/uvic_banner.css">
    <link rel="icon" href="https://www.uvic.ca/assets/core-4-0/img/favicon-32.png">
    <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/checkout/">

    <!-- Bootstrap core CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <!-- Favicons -->
<link rel="apple-touch-icon" href="/docs/4.5/assets/img/favicons/apple-touch-icon.png" sizes="180x180">
<link rel="icon" href="/docs/4.5/assets/img/favicons/favicon-32x32.png" sizes="32x32" type="image/png">
<link rel="icon" href="/docs/4.5/assets/img/favicons/favicon-16x16.png" sizes="16x16" type="image/png">
<link rel="manifest" href="/docs/4.5/assets/img/favicons/manifest.json">
<link rel="mask-icon" href="/docs/4.5/assets/img/favicons/safari-pinned-tab.svg" color="#563d7c">
<link rel="icon" href="/docs/4.5/assets/img/favicons/favicon.ico">
<meta name="msapplication-config" content="/docs/4.5/assets/img/favicons/browserconfig.xml">
<meta name="theme-color" content="#563d7c">


    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      /* Style for the container div */
      .user_jobs_container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        overflow: scroll;      
        white-space: nowrap;
        gap: 20px; /* Adjust the space between the items */
        border: 2px lightgrey;
        border-width: 0.5px;
        border-style: solid;
        border-radius: 5px;
        padding: 7px;

      }

      /* Style for each checkbox */
      .job-checkbox {
        display: inline-block;
        margin: 3px;
      }

      .job-item {
        flex: 1 1 calc(33.333% - 10px); 
        box-sizing: border-box;
      }

      .popup {
          position: relative;
          display: inline;  /* Unchanged */
          cursor: pointer;
          padding-left: 5px;
      }

      .popup .popuptext {
          visibility: hidden;
          width: 500px;
          background-color: #555;
          color: #fff;
          text-align: center;
          border-radius: 6px;
          padding: 8px 0;
          position: absolute;
          z-index: 1;
          top: 0;
          left: 110%;
          margin-left: 20px;
      }

      .popup .popuptext::after {
          content: "";
          position: absolute;
          top: 50%;
          left: 0;
          margin-top: -5px;
          border-width: 5px 0 5px 5px;
          border-style: solid;
          border-color: transparent transparent transparent #555;
      }

      
      .popup:hover .popuptext, .popup .popuptext:hover {
          visibility: visible; /* Show or keep the popup when hovering */
          -webkit-animation: fadeIn 1s;
          animation: fadeIn 1s;
      }

      
      .popup .show {
        visibility: visible;
        -webkit-animation: fadeIn 1s;
        animation: fadeIn 1s;
      }


      /* Adjust for medium screens to 2 columns */
      @media (max-width: 800px) {
          .job-item {
              flex: 1 1 calc(50% - 10px); /* Adjust the calc() as necessary */
          }
      }

      /* Adjust for smaller screens to 1 column */
      @media (max-width: 600px) {
          .job-item {
              flex: 1 1 100%;
          }
        }

      input[type="checkbox"] + label {
          margin-left: 10px; /* Adjust the value to suit your needs */
      }

      #selectJobsButton{
        padding: 3px 7px;
        margin: 2px;
        background-color: white; /* white background */
        color: black; /* black text */
        border: solid;
        border-color: blue; /*blue borders*/
        border-radius: 4px; /* Rounded corners */
        border-width: 0.5px;
/*        cursor: pointer; /* Mouse pointer on hover */*/
        font-size: 6px; /* Larger font size */
      }
    </style>

    <!-- Custom styles for this template -->
    <link href="form-validation.css" rel="stylesheet">
  </head>
  <body class="bg-light">

    <!--Header-->
    <div id="custom_header"><div class="wrapper" style="min-height: 6em;" id="banner">
     <div style="position:absolute; left: 5px; top: 26px;">
      <a href="http://www.uvic.ca/" id="logo"><span>University of Victoria</span></a>
     </div>
     <div style="position:absolute; left: 176px; top: 26px;">
      <a href="http://www.uvic.ca/library/" id="unit"><span>Libraries</span></a>
     </div>
     <div class="edge" style="position:absolute; margin: 0px;right: 0px; top: 0px; height: 96px; width:200px;">&nbsp;</div>
    </div>
    <!--Header end-->

    <div class="container">
    <form method="POST" enctype="multipart/form-data">
    <div class="py-5 text-center">
      <h1><?php echo " Job name: " . $job["job_name"];?></h1>
      <h2><?php echo "Customer: " . $job_owner["name"];?></h2>
    </div>
        <div class="col-md-12 order-md-1">
          <h4 class="mb-3">Submission Date</h4>
          <div class="row">
            <div class="col-md-3 mb-3">
              <div class="input-group">
                <div class="input-group">
                  <input type="text" class="form-control" value="<?php echo $job["submission_date"]; ?>" readonly>
                </div>
              </div>
              <div class="invalid-feedback" style="width: 100%;">
              Status is required.
              </div>
            </div>
            <!--Job Purpose // academic vs. personal-->
            <div class="col-md-3 mb-3">
              <p><?php echo "Job purpose: <br>" .$job['job_purpose'];?></p>
            </div>
            <!--If Academic Purpose: course code-->
            <div class="col-md-3 mb-3">
              <p><?php 
                if ($job["job_purpose"] == "academic"){
                  echo "Course Code: <br>" . $job['academic_code'];}
              ?></p>
            </div>
          </div>

        </div>

        <div class="col-md-12 order-md-1">
        <h4 class="mb-3">Status</h4>
          <div class="row">
            <div class="col-md-3 mb-3">
              <select class="custom-select d-block w-100" name="status" id="layer-height">
                <?php 
                  if ($job["status"] == "cancelled") {?> 
                    <option value="cancelled" selected readonly>cancelled</option> 
                <?php } 
                  else { ?>
                  <option value="submitted" <?php if ($job["status"]== "submitted"){echo "selected";} ?>>Not Priced</option>
                  <option value="pending payment" <?php if ($job["status"]== "pending payment"){echo "selected";} ?>>Pending Payment</option>
                  <option value="on hold" <?php if ($job["status"]== "on hold"){echo "selected";} ?>>On Hold</option>
                  <option value="paid" <?php if ($job["status"]== "paid"){echo "selected";} ?>>Paid</option>
                  <option value="printing" <?php if ($job["status"]== "printing"){echo "selected";} ?>>Printing</option>
                  <option value="printed" <?php if ($job["status"]== "completed"){echo "selected";} ?>>Completed</option>
                  <option value="completed" <?php if ($job["status"]== "delivered"){echo "selected";} ?>>Delivered</option>
                  <option value="archived" <?php if ($job["status"]== "archived"){echo "selected";} ?>>Archived</option>
                <?php } ?>
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <p><?php echo "Status changed: <br>" .$status_date;?></p>
            </div>
            <div class="col-md-3 mb-3">
              <p><?php echo "Status changed by: <br>" . $status_signer;?></p>
            </div>
          </div>
          
          <script>
          function checkAll() {
            var checkboxes = document.querySelectorAll('.job-checkbox');
            console.log(checkboxes.length);
            checkboxes.forEach(function(checkbox) {
              checkbox.checked = true;
            });
          }

          function uncheckAll() {
            var checkboxes = document.querySelectorAll('.job-checkbox');
            console.log(checkboxes.length);
            checkboxes.forEach(function(checkbox) {
              checkbox.checked = false;
            });
          }

          // function populatePopup(other_job) {
          //   const popupElement = document.querySelector('.popuptext');

          //   // Clear existing content
          //   popupElement.innerHTML = '';
          //   //Fill popup with job-specific details
          //   popupElement.innerHTML = '${other_job['name']}<br>${other_job['status']}<br>${other_job['submission_date']}';
          // }

        </script>    

          <!-- container with a 4-column list of the user's active web jobs. Used for batch status changes -->
          <div class="col-md-12 order-md-1">
            <h4 class="mb-3">Other Active Jobs</h4>
            <?php 
            echo '<button type="button" id="selectJobsButton" onclick="checkAll()">Check All</button>';
            echo '<button type="button" id="selectJobsButton" onclick="uncheckAll()">Uncheck All</button>'; 
            ?>
          <div class="user_jobs_container">

          <?php

            // Iterate through the $active_user_jobs array
            foreach ($active_user_jobs as $other_active_job) {
              if($job['id'] != $other_active_job['id']){
                echo '<div class="job-item">';
                echo '<input type="checkbox" class ="job-checkbox" id="' . $other_active_job['id'] . '" name="checked_jobs[]" value="' . $other_active_job['id'] . '">';
                echo '<label for="' . $other_active_job['id'] . '">';
                if ($other_active_job['status'] == "on hold") {
                    echo "  On hold -";
                }
                // Check if 'name' index is set
                if (isset($other_active_job['name'])) {
                    echo "  " . $other_active_job['name'];
                } else {
                    echo "No id available"; 
                }
                echo '</label>';
                echo '</div>';
                }
              }
            ?>
              </div>
            </div> <!--End of associated jobs list-->
<!-- ! Popup div not working for related jobs
                  echo '<span class="popup">';
                    echo '<span class="popuptext" id="myPopup">';
                      echo '<p>${other_active_job["name"]}<br>${other_active_job["status"]}<br>${other_active_job["submission_date"]}';
                    echo '</span>';
                  echo '</span>'; 
 -->
        
          <div class="col-md-12 order-md-1">
            <h4 class="mb-3">Price</h4>
              <div class="row">
                  <div class="col-md-3 mb-3">
                      <div class="input-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                              <!-- ** catch non floatable input-->
                                <span class="input-group-text">$</span>
                                <input type="text" name="price" autocomplete="off" class="form-control" value="<?php echo number_format((float)$job["price"], 2, '.','');?>"
                                <?php if ($job["status"] != "submitted" && $job["status"] != "pending payment" && $job["status"] != "on hold"): ?>
                                  readonly
                                <?php endif; ?>
                                >
                            </div>
                      </div>
                      <small class="text-muted">Reminder: Minimum payment is $2.00.</small>
                      <div class="invalid-feedback" style="width: 100%;">
                      Status is required.
                      </div>
                      </div>
                  </div>
              </div>

    <hr class="mb-6">

    <h3 class="mb-3">Drawing</h3>
        <?php
        if (is_file(("uploads/" . $job['model_name']))) {
            ?>
            <!--Grabs file and renames it to the job name when downloaded-->
            <a href="<?php echo "uploads/" . $job['model_name']; ?>" download="<?php
                $filetype = explode(".", $job['model_name']);
                echo $job['job_name'] . "." . $filetype[1]; ?>">
                Download file
            </a>
        <?php
        }
        else{ ?>
          <p>File Deleted</p>
        <?php } ?>
      <br>
      <hr class="mb-6">

      <h3 class="mb-3">Modified Drawing</h3>

    <?php //checks if there is a modify file
    if ($job['model_name_2'] != NULL && is_file(("uploads/" . $job['model_name_2']))) { ?>
      <a href="<?php echo "uploads/" . $job['model_name_2']; ?>" download>Download Drawing file</a>
    <?php } ?>
    <br/>

    <small class="text-muted">(Max 200MB)</small>
      <input type="file" id="myFile" name="modify">
    
      <br>
      <hr class="mb-6">

      <h5 class="mb-2">Drawing Description</h5>
        <div class="input-group">
            <textarea rows="5" cols="50" class="form-control" aria-label="additional-comments" readonly><?php echo $job["specifications"]; ?></textarea>
        </div>
      <hr class="mb-4">
      <h5 class="mb-2">Copies</h5>
      <div class="col-md-3 mb-3">
          <label for="copies">Copies</label>
          <input type="number" class="form-control" name="copies" min="1" max="100" step="1" value="1" id="supports" placeholder="<?php if ($job["copies"]!= ""){echo "{$job["copies"]}";} else{"Enter # of copies";}?>" required />
          <div class="invalid-feedback">
            Please provide a valid response.
          </div>
        </div>
      </div>

        
        <hr class="mb-4">
        <h5 class="mb-2">Material Type</h5>
        <div class="d-block my-3">
        <div class="custom-control custom-radio">
            <input id="plywood_3mm" name="material_type" value="Plywood 3mm" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "Plywood 3mm"){echo "checked";} ?>>
            <label class="custom-control-label" for="plywood_3mm">Plywood 3mm</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="plywood_6mm" name="material_type" value="Plywood 6mm" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "Plywood 6mm"){echo "checked";} ?>>
            <label class="custom-control-label" for="plywood_6mm">Plywood 6mm</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="mdf_3mm" name="material_type" value="MDF 3mm" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "MDF 3mm"){echo "checked";} ?>>
            <label class="custom-control-label" for="mdf_3mm">MDF 3mm</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="mdf_6mm" name="material_type" value="MDF 6mm" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "MDF 6mm"){echo "checked";} ?>>
            <label class="custom-control-label" for="mdf_6mm">MDF 6mm</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="laser_cut_other" name="material_type" value="Other" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "Other"){echo "checked";} ?>>
            <label class="custom-control-label" for="other">Other</label>
            <!-- <small class="text-muted"> - Elaborate in Additional Comments section</small> -->
          </div>
        </div>
      
        <hr class="mb-4">
        <h5 class="mb-2">Additional Comments</h5>
            <div class="input-group">
                <textarea rows="5" cols="50" class="form-control" aria-label="additional-comments" readonly><?php echo $job["comments"]; ?></textarea>
            </div>

        <hr class="mb-4">
        <h5 class="mb-2">Staff Notes</h5>
            <div class="input-group">
                <textarea rows="5" cols="50" class="form-control" name="staff_notes" aria-label="additional-comments"><?php echo $job["staff_notes"]; ?></textarea>
            </div>
            <div class="invalid-feedback">
            Please enter additional comments.
            </div>
        </div>


        <hr class="mb-4">
        <h5 class="mb-2">Enable Email</h5>
        <div class="d-block my-3">
          <div class="custom-control custom-checkbox">
            <input id="en_email" name="email_enabaled" value= "enabled" type="checkbox" class="custom-control-input" <?php if ($job["status"] != "pending payment" && $job["status"] != "completed"){echo "checked";} ?>>
            <label class="custom-control-label" for="en_email">Send email for pending payment or completed when saved.</label>
          </div>
        </div>

        <hr class="mb-4">
        <div class="row">
            <div class="col-md-6 mb-3">
                <a href="url">
                    <button class="btn btn-primary btn-lg btn-block" class="form-control" type="submit" data-inline="true">Save</button>
                </a>
            </div>
            <div class="col-md-6 mb-3">
                <a class="btn btn-primary btn-lg btn-block" href="admin-dashboard.php" role="button">Back to Dashboard</a>
        </div>
    </div>
    </form>
  </div>
  

<!-- DUPLICATE JOB BUTTONS-->
<style>
/* Button style */
/*#myBtn{
    width: 50px;
    background-color: red;
    color: white;
    padding: 10px 20px;
    border: none;
    cursor: pointer;
    text-align: center;
}*/

/* The Popup (background) */
.popup {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Popup Content */
.popup-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}

/* The Close Button */
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
</style>



<hr class="mb-4">
  <center>
    <!-- Button to trigger 'Duplicate Job' confirmation popup; button background color set to purple-->
    <button id="duplicate-button" class="btn btn-primary btn-lg" style="background-color:#CF9FFF;">Duplicate Job</button> <!--duplicate button-->
      <!-- The Duplicate Popup -->
      <div id="DuplicateJobPopup" class="popup">
        <div class="popup-content">
          <span class="close" data-popup="DuplicateJobPopup">&times;</span>
          <p>Are you sure you want to duplicate your job?</p>
            <a href="customer-duplicate-laser-job.php?job_id=<?php echo $job["id"]; ?>">
                <button class="btn btn-primary btn-lg" style="background-color:#CF9FFF;">Duplicate Job</button>
            </a>
        </div>
      </div>
  </center>

<script>
window.onload = function() {
    // Function to open a popup
    function openPopup(popupId) {
        var popup = document.getElementById(popupId);
        if (popup) {
            popup.style.display = "block";
        }
    }

    // Function to close a popup
    function closePopup(popupId) {
        var popup = document.getElementById(popupId);
        if (popup) {
            popup.style.display = "none";
        }
    }

    // Attach event listeners to buttons
    var duplicateButton = document.getElementById("duplicate-button");

    if (duplicateButton) {
        duplicateButton.onclick = function() { openPopup("DuplicateJobPopup"); }
    }

    // Attach event listeners to close buttons
    var closeButtons = document.getElementsByClassName("close");
    for (var i = 0; i < closeButtons.length; i++) {
        closeButtons[i].onclick = function() {
            var popupId = this.getAttribute("data-popup");
            closePopup(popupId);
        }
    }

    // Close popup when clicking outside of it
    window.onclick = function(event) {
        if (event.target.classList.contains("popup")) {
            event.target.style.display = "none";
        }
    }
}
</script>
  <p></p>
  <br>
  <p></p>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
      <script>window.jQuery || document.write('<script src="/docs/4.5/assets/js/vendor/jquery.slim.min.js"><\/script>')</script><script src="/docs/4.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-1CmrxMRARb6aLqgBO7yyAxTOQE2AKb9GfXnEo760AUcUmFx3ibVJJAzGytlQcNXd" crossorigin="anonymous"></script>
        <script src="form-validation.js"></script>
        </body>
</html>
