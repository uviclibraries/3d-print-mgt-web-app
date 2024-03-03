<?php
session_start();
require ('auth-sec.php'); //Gets CAS & db
//auth-sec includes: $user, $user_email, $user_type, $user_name
//Is user Admin check
if ($user_type == 1) {
  header("Location: customer-dashboard.php");
  die();
}


$jobType = "large format print";
$userView = "admin";
$type_href= '<a href="';
$type_href  = $type_href . 'https://devwebapp.library.uvic.ca/demo/3dwebapp/admin-large-format-print-job-specification.php?job_id=';


$stm = $conn->prepare("SELECT * FROM web_job INNER JOIN large_format_print_job ON id=large_format_print_id WHERE id=?");
$stm->execute([$_GET["job_id"]]);
$job=$stm->fetch();

//Get users name & email
$userSQL = $conn->prepare("SELECT * FROM users WHERE netlink_id = :netlink_id");
$userSQL->bindParam(':netlink_id', $job['netlink_id']);
$userSQL->execute();
$job_owner = $userSQL->fetch();

//Fetches all of the customer's active jobs 'user_web_jobs', puts in 'active_user_jobs[]' and 'linked_jobs[]'
include('sql_snippets/fetch_active_jobs.php');

$parent_href = $type_href.$job['parent_job_id'] . '"">' . $prev_parent_id . '</a>';


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

  // change to source from web job and large_format_print_job
  $stmt = $conn->prepare("UPDATE web_job INNER JOIN large_format_print_job ON id=large_format_print_id SET price = :price, width_inches = :width_inches, copies =:copies, length_inches = :length_inches, staff_notes = :staff_notes, status = :status, parent_job_id =:parent_job_id,priced_date = :priced_date, paid_date = :paid_date, printing_date = :printing_date, completed_date = :completed_date, cancelled_date = :cancelled_date, delivered_date = :delivered_date, priced_signer =:priced_signer,  paid_signer= :paid_signer, printing_signer=:printing_signer, completed_signer=:completed_signer, delivered_signer=:delivered_signer, hold_date = :hold_date, hold_signer= :hold_signer,cancelled_signer= :cancelled_signer, model_name_2 =:model_name_2, is_parent = :is_parent WHERE id = :job_id");
  
  $current_date = date("Y-m-d");


  //Variables for status change dates and name of person who changed the status
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
  include('sql_snippets/update_broad_specs_snippet.php');

  //Updates length and width of print
  include('sql_snippets/update_large_format_specs_snippet.php');


  //Sets job status update date, and admin who updated the status, and sends any relevant emails via 'general_partials/send_customer_email_partial.php'
  include('admin_spec_php_partials/admin_update_job_status_email_partial.php');


  $stmt->execute();

  //Snippet checks if any related active user jobs have been selected, and updates those jobs' status to match the jobs status after save.
  include('sql_snippets/admin_update_multiple_status_snippet.php');

  //exit to dashboard after saving
  header("location: admin-dashboard.php");
  }

  //Sets the date to appear in `echo "Status changed: <br>" .$status_date;` based on $job['status'] and $job_owner['name'] at time of page load
  include('general_partials/declare_status_date.php');

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
    <!-- Custom styles for this template -->
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
    
      <!--Element and js for viewing and editing the specs specific to large format jobs
        (length and width)-->
      <?php include('admin_spec_php_partials/admin_large_format-specific_specs_partial.php');?>

      <!--Element for viewing the customer comments and adding/modifying admin comments-->
      <?php include('general_partials/comment_boxes_partial.php');?>


      <!--Elements for enabling email, save updated information, return to dashboard.-->
      <?php include('admin_spec_php_partials/admin_specs_footer_partial.php');?>


      </form>
    </div>

    <!--Button element to duplicate job-->

    <?php include('general_partials/duplicate_button_partial.php');?>
        
  <p></p>
  <br>
  <p></p>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script>window.jQuery || document.write('<script src="/docs/4.5/assets/js/vendor/jquery.slim.min.js"><\/script>')</script><script src="/docs/4.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-1CmrxMRARb6aLqgBO7yyAxTOQE2AKb9GfXnEo760AUcUmFx3ibVJJAzGytlQcNXd" crossorigin="anonymous"></script>
  <script src="form-validation.js"></script>
  <script type="text/javascript" src="js/popup_function.js"></script>
  </body>
</html>
