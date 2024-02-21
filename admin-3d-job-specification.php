<?php
session_start();
require ('auth-sec.php'); //Gets CAS & db
//auth-sec includes: $user, $user_email, $user_type, $user_name
//Is user Admin check
if ($user_type == 1) {
  header("Location: customer-dashboard.php");
  die();
}

$job_type = "3dPrint";
$user_view = "admin";

$stm = $conn->prepare("SELECT * FROM web_job INNER JOIN 3d_print_job ON id=3d_print_id WHERE id=?");
$stm->execute([$_GET["job_id"]]);
$job=$stm->fetch();

//Get users name & email
$userSQL = $conn->prepare("SELECT * FROM users WHERE netlink_id = :netlink_id");
$userSQL->bindParam(':netlink_id', $job['netlink_id']);
$userSQL->execute();
$job_owner = $userSQL->fetch();

//get list of active jobs associated with the job's owner
$stm = $conn->prepare("SELECT web_job.id AS id, web_job.job_name AS name, web_job.status AS status, web_job.submission_date AS submission_date, web_job.priced_date AS priced_date, web_job.paid_date AS paid_date,web_job.printing_date AS printing_date,web_job.completed_date AS completed_date,web_job.delivered_date AS delivered_date,web_job.hold_date AS hold_date,web_job.hold_signer AS hold_signer,web_job.cancelled_signer AS cancelled_signer,  web_job.priced_signer AS priced_signer, web_job.paid_signer AS paid_signer, web_job.printing_signer AS printing_signer, web_job.completed_signer AS completed_signer, web_job.delivered_signer AS delivered_signer, web_job.job_purpose AS job_purpose, web_job.academic_code AS academic_code, web_job.course_due_date AS course_due_date, 3d_print_job.duration AS duration, web_job.parent_job_id AS parent_job_id , web_job.is_parent AS is_parent FROM web_job INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status NOT IN ('delivered', 'archived', 'cancelled') AND web_job.netlink_id = :netlink_id");

  $stm->bindParam(':netlink_id', $job['netlink_id']);
  $stm->execute();
  $user_web_jobs = $stm->fetchAll();

  $parent=$job; //set self as parent if no other job has been assigned to this job as the parent.

  $active_user_jobs = [];
  $linked_jobs = [];

  foreach ($user_web_jobs as $related_job) {
    if($related_job['id'] != $job['id']){
      array_push($active_user_jobs, $related_job);
      if($related_job['parent_job_id'] == $job['id'] && ($related_job['parent_job_id'] !=0|| $job['parent_job_id'] != 0))
      {
        array_push($linked_jobs, $related_job);
      }

      if($related_job['id'] == $job['parent_job_id'] && $job['parent_job_id'] != 0){
        $parent = $related_job; //sets parent if the job's parent id matches the id of another job
        array_push($linked_jobs, $related_job);
      }
    }
    else{
      if($parent == $job){
        $parent = $related_job;}
    }
  }

  $bundled = $active_user_jobs ? true : false; //user has other active jobs
  // echo 'num linked: ' . count($linked_jobs) . '; num active' . count($active_user_jobs) . ';  is parent: ' . $job['is_parent'] . '; parent: ' . $parent['id'];
/*
$stm = $conn->prepare("SELECT * FROM print_job WHERE id=?");
$stm->execute([$_GET["job_id"]]);
$job=$stm->fetch();
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

  // change to source from web job and 3d_print_job
  $stmt = $conn->prepare("UPDATE web_job INNER JOIN 3d_print_job ON id=3d_print_id SET price = :price, infill = :infill, scale = :scale, layer_height = :layer_height, copies=:copies,supports = :supports, material_type = :material_type, staff_notes = :staff_notes, status = :status, priced_date = :priced_date,  paid_date = :paid_date, printing_date = :printing_date, completed_date = :completed_date, cancelled_date = :cancelled_date, delivered_date = :delivered_date, priced_signer =:priced_signer,  paid_signer= :paid_signer, printing_signer=:printing_signer, completed_signer=:completed_signer, delivered_signer=:delivered_signer, hold_date = :hold_date, hold_signer= :hold_signer,cancelled_signer= :cancelled_signer, model_name_2 =:model_name_2, duration = :duration, parent_job_id =:parent_job_id WHERE id = :job_id;");
  //doesn't include change is_parent
  $current_date = date("Y-m-d");


  $d_priced = $job['priced_date'];
  $d_paid = $job['paid_date'];
  $d_printing = $job['printing_date'];
  $d_delivered = $job['delivered_date'];
  $d_hold = $job['hold_date'];
  $d_completed = $job['completed_date'];
  $d_cancelled = $job['cancelled_date'];

  $n_hold = $job['hold_signer'];
  $n_cancelled = $job['cancelled_signer'];
  $n_priced = $job['priced_signer'];
  $n_paid = $job['paid_signer'];
  $n_printing = $job['printing_signer'];
  $n_completed = $job['completed_signer'];
  $n_delivered = $job['delivered_signer'];


  //Updates job id, price, staff notes, copies, updated model name, status, parentid, and if laser cut or 3d print, duration and material type.
  insert('update_job_specs_snippet.php');



  //need variable to check if admin wants to send email. case: updating notes but dont send email
  if ($_POST['status'] == "pending payment") {
    $d_priced = $current_date;
    $n_priced=$user;
    //email user
    if (isset($_POST['email_enabaled']) && $_POST['email_enabaled'] == "enabled") {
      //get job owner details
      $userSQL = $conn->prepare("SELECT * FROM users WHERE netlink_id = :netlink_id");
      $userSQL->bindParam(':netlink_id', $job['netlink_id']);
      $userSQL->execute();
      $job_owner = $userSQL->fetch();

      $direct_link = "https://webapp.library.uvic.ca/3dprint/customer-3d-job-information.php?job_id=". $job['id'];
      $direct_link2 = "https://onlineacademiccommunity.uvic.ca/dsc/how-to-3d-print/";
      $msg = "
      <html>
      <head>
      <title>HTML email</title>
      </head>
      <body>
      <p> Hello, ". $job_owner['name'] .". This is an automated email from the DSC. </p>
      <p> Your 3D print job (".$job['job_name']. ") has been evaluated at a cost of $" . (number_format((float)$_POST["price"], 2, '.','')) . " </p>
      <p> Please make your payment <a href=". $direct_link .">here</a> for it to be placed in our printing queue.</p>
      <p>If you have any questions please review our <a href=". $direct_link2 .">FAQ</a> or email us at dscommons@uvic.ca.</p>
      </body>
      </html>";
      $headers = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers .= "From: dscommons@uvic.ca" . "\r\n";
      mail($job_owner['email'],"Your 3D Print is ready for payment",$msg,$headers);
    }
  } elseif($_POST['status'] == "paid"){
    //this is done automatically when payment is received.
    $d_paid = $current_date;
    $n_paid=$user;


  } elseif($_POST['status'] == "printing"){
    $d_printing = $current_date;
    $n_printing=$user;
}
    elseif($_POST['status'] == "completed"){
    $d_completed = $current_date;
    $n_completed=$user;


  } elseif ($_POST['status'] == "delivered") {
    $d_delivered = $current_date;
    $n_delivered=$user;

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
      <p> Your 3D print job (".$job['job_name']. ") has been printed. You can pick it up from the front desk at the McPherson Library.</p>
      <p>Please check up to date library hours by checking the library website <a href=". $direct_link .">here</a></p>
      </body>
      </html>";
      $headers = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers .= "From: dscommons@uvic.ca" . "\r\n";
      mail($job_owner['email'], "Your 3D Print is ready for collection",$msg,$headers);
    }
  } elseif($_POST['status'] == "archived"){
    $d_delivered = $current_date;
    $n_delivered = $user;

  } elseif($_POST['status'] == "on hold"){
    $d_hold = $current_date;
    $n_hold = $user;
  }
  elseif($_POST['status'] == "cancelled"){
    $d_cancelled = $current_date;
    $n_cancelled = $user;
  }

  $stmt->execute();


  //Snippet checks if any related active user jobs have been selected, and updates those jobs' status to match the jobs status after save.
  insert('sql_snippets/admin_update_multiple_status_snippet.php');


  //exit to dashboard after saving
  header("location: admin-dashboard.php");
  }

//sets status date and signer variables based on $job['status'] at time of page load.
  insert('general_partials/declare_status_date.php');

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
    <link rel="stylesheet" href="css/popup_styles.css">
    <link rel="stylesheet" href="css/linked_jobs_display_styles.css">
    <link href="form-validation.css" rel="stylesheet">

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
  </style>

  </head>

  <body class="bg-light">

    <!--Header-->
    <div id="custom_header">
      <div class="wrapper" style="min-height: 6em;" id="banner">
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
      <script>
        // Listen for keypress events on the form
        document.getElementById('myForm').addEventListener('keypress', function(event) {
          // Check if the pressed key is the Enter key
          if (event.key === 'Enter') {
              // Prevent the default action to stop form submission
              event.preventDefault();
          }
        });
      </script>

      <!--Name of job, customer, date submited, academic/personal project, what admins updated status last-->
      <?php include('admin_spec_php_partials/admin_broad_spec_partial.php');?>

      <!--Dropdown to set a new status-->
      <?php include('admin_spec_php_partials/admin_status_dropdown_partial.php');?>

      <!-- Select a new parent, see linked jobs, link jobs, change status of multiple jobs-->
      <?php include('admin_spec_php_partials/admin_linked_jobs_tab_partial.php');?>
      
      <!--Element for viewing/setting job price-->
      <?php include('admin_spec_php_partials/admin_price_partial.php');?>

      <!--Element for uploading/downloading job model files-->
      <?php include('general_partials/models_partial.php');?>

      <!--Element for viewing the number of copies ordered-->
      <?php include('general_partials/copies_partial.php');?>

      <!--Element for viewing the specs specific to 3d jobs
        infill, scale, layer heights, support, material type-->
      <?php include('admin_spec_php_partials/admin_3d-specific_specs_partial.php');?>
      
      <!--Element for viewing the customer comments and adding/modifying admin comments-->
      <?php include('general_partials/comment_boxes_partial.php');?>

      <!--Elements for enabling email, save updated information, return to dashboard, or duplicate the job.-->
      <?php include('admin_spec_php_partials/admin_specs_footer_partial.php');?>


  <p></p>
  <br>
  <p></p>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script>window.jQuery || document.write('<script src="/docs/4.5/assets/js/vendor/jquery.slim.min.js"><\/script>')</script><script src="/docs/4.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-1CmrxMRARb6aLqgBO7yyAxTOQE2AKb9GfXnEo760AUcUmFx3ibVJJAzGytlQcNXd" crossorigin="anonymous"></script>
  <script src="form-validation.js"></script>
  <script type="text/javascript" src="js/linked_jobs_function.js"></script>
  <script type="text/javascript" src="js/popup_function.js"></script>
</body>
</html>
