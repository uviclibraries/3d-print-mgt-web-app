<?php
session_start();
require ('auth-sec.php'); //Gets CAS & db
//auth-sec includes: $user, $user_email, $user_type, $user_name
//Is user Admin check
if ($user_type == 1) {
  header("Location: customer-dashboard.php");
  die();
}


$jobType = "largeFormat";
$user_view = "admin";

$stm = $conn->prepare("SELECT * FROM web_job INNER JOIN large_format_print_job ON id=large_format_print_id WHERE id=?");
$stm->execute([$_GET["job_id"]]);
$job=$stm->fetch();

//Get users name & email
$userSQL = $conn->prepare("SELECT * FROM users WHERE netlink_id = :netlink_id");
$userSQL->bindParam(':netlink_id', $job['netlink_id']);
$userSQL->execute();
$job_owner = $userSQL->fetch();

//get list of active jobs associated with the job's owner
$stm = $conn->prepare("SELECT web_job.id AS id, web_job.job_name AS name, web_job.status AS status, web_job.submission_date AS submission_date, web_job.priced_date AS priced_date, web_job.paid_date AS paid_date,web_job.printing_date AS printing_date,web_job.completed_date AS completed_date,web_job.delivered_date AS delivered_date,web_job.hold_date AS hold_date,web_job.hold_signer AS hold_signer,web_job.cancelled_signer AS cancelled_signer,  web_job.priced_signer AS priced_signer, web_job.paid_signer AS paid_signer, web_job.printing_signer AS printing_signer, web_job.completed_signer AS completed_signer, web_job.delivered_signer AS delivered_signer, web_job.job_purpose AS job_purpose, web_job.academic_code AS academic_code, web_job.course_due_date AS course_due_date, web_job.parent_job_id AS parent_job_id , web_job.is_parent AS is_parent FROM web_job INNER JOIN large_format_print_job ON web_job.id=large_format_print_job.large_format_print_id WHERE web_job.status NOT IN ('delivered', 'archived', 'cancelled') AND web_job.netlink_id = :netlink_id");

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
  $stmt = $conn->prepare("UPDATE web_job INNER JOIN large_format_print_job ON id=large_format_print_id SET price = :price, width_inches = :width_inches, copies =:copies, length_inches = :length_inches, staff_notes = :staff_notes, status = :status, priced_date = :priced_date, paid_date = :paid_date, printing_date = :printing_date, completed_date = :completed_date, cancelled_date = :cancelled_date, delivered_date = :delivered_date, priced_signer =:priced_signer,  paid_signer= :paid_signer, printing_signer=:printing_signer, completed_signer=:completed_signer, delivered_signer=:delivered_signer, hold_date = :hold_date, hold_signer= :hold_signer,cancelled_signer= :cancelled_signer, model_name_2 =:model_name_2 parent_job_id =:parent_job_id WHERE id = :job_id;");
  
  $current_date = date("Y-m-d");

  $stmt->bindParam(':job_id', $job['id']);
  $price = floatval(number_format((float)$_POST["price"], 2, '.',''));
  $stmt->bindParam(':price', $price);
  $length_inches = intval($_POST["length_inches"]);
  $stmt->bindParam(':length_inches', $length_inches, PDO::PARAM_INT);
  $width_inches = intval($_POST["width_inches"]);
  $stmt->bindParam(':width_inches', $width_inches, PDO::PARAM_INT);
  
  $copies = intval($_POST["copies"]);
  $stmt->bindParam(':copies', $copies , PDO::PARAM_INT);
  // $duration = intval($_POST["duration"]);
  // $stmt->bindParam(':duration',$duration, PDO::PARAM_INT);
  $stmt->bindParam(':staff_notes', $_POST["staff_notes"]);
  $stmt->bindParam(':status', $_POST["status"]);
  $stmt->bindParam(':model_name_2', $modify_value);
  $new_parent= intval($_POST["select_parent"]);
  $stmt->bindParam(':parent_job_id', $new_parent, PDO::PARAM_INT);
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
    $priceds=$user;
    //email user
    if (isset($_POST['email_enabaled']) && $_POST['email_enabaled'] == "enabled") {
      //get job owner details
      $userSQL = $conn->prepare("SELECT * FROM users WHERE netlink_id = :netlink_id");
      $userSQL->bindParam(':netlink_id', $job['netlink_id']);
      $userSQL->execute();
      $job_owner = $userSQL->fetch();

      $direct_link = "https://webapp.library.uvic.ca/3dprint/customer-large-format-print-job-information?job_id=". $job['id'];
      $direct_link2 = "https://onlineacademiccommunity.uvic.ca/dsc/tools-tech/large-format-printer-and-scanner/";
      $msg = "
      <html>
      <head>
      <title>HTML email</title>
      </head>
      <body>
      <p> Hello, ". $job_owner['name'] .". This is an automated email from the DSC. </p>
      <p> Your large format print job (".$job['job_name']. ") has been evaluated at a cost of $" . (number_format((float)$_POST["price"], 2, '.','')) . " </p>
      <p> Please make your payment <a href=". $direct_link .">here</a> for it to be placed in our printing queue.</p>
      <p>If you have any questions please review our <a href=". $direct_link2 .">FAQ</a> or email us at dscommons@uvic.ca.</p>
      </body>
      </html>";
      $headers = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers .= "From: dscommons@uvic.ca" . "\r\n";
      mail($job_owner['email'],"Your Large Format Print is ready for payment",$msg,$headers);
    }
  } elseif($_POST['status'] == "paid"){
    //this is done automatically when payment is received.
    $d2 = $current_date;
    $paids=$user;


  } elseif($_POST['status'] == "printing"){
    $d3 = $current_date;
    $printings=$user;
}
    elseif($_POST['status'] == "completed"){
    $d6 = $current_date;
    $completes=$user;


  } elseif ($_POST['status'] == "delivered") {
    $d4 = $current_date;
    $ds=$user;

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
      <p> Your large format print job (".$job['job_name']. ") has been printed. You can pick it up from the front desk at the McPherson Library.</p>
      <p>Please check up to date library hours by checking the library website <a href=". $direct_link .">here</a></p>
      </body>
      </html>";
      $headers = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers .= "From: dscommons@uvic.ca" . "\r\n";
      mail($job_owner['email'], "Your Large Format Print is ready for collection",$msg,$headers);
    }
  } elseif($_POST['status'] == "archived"){
    $d4 = $current_date;
    $completes = $user;

  }elseif($_POST['status'] == "on hold"){
    $d5 = $current_date;
    $hs = $user;
  }
  elseif($_POST['status'] == "cancelled"){
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

      $stm = $conn->prepare("UPDATE web_job INNER JOIN large_format_print_job ON id=large_format_print_id SET status = :status, priced_date = :priced_date, paid_date = :paid_date, printing_date = :printing_date, completed_date = :completed_date, delivered_date = :delivered_date, priced_signer=:priced_signer,  paid_signer= :paid_signer, printing_signer=:printing_signer, completed_signer=:completed_signer, delivered_signer=:delivered_signer, hold_date = :hold_date, hold_signer= :hold_signer, cancelled_date=:cancelled_date, cancelled_signer = :cancelled_signer WHERE id IN ($checkedIDs_sql)");

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
  }

  //exit to dashboard after saving
  header("location: admin-dashboard.php");
  }

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
      $status_date = $job["completed_date"];
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
      <?php include('admin_spec_php_partials/    admin_large_format-specific_specs_partial.php');?>

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
        </body>
</html>
