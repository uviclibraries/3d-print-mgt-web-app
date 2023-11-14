<?php
session_start();
require ('auth-sec.php'); //Gets CAS & db
//auth-sec includes: $user, $user_email, $user_type, $user_name
//Is user Admin check
if ($user_type == 1) {
  header("Location: customer-dashboard.php");
  die();
}

//pull only 3d print strings from web_job

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.submission_date AS submission_date, web_job.job_purpose AS job_purpose, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id=users.netlink_id INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status = 'submitted' ORDER BY web_job.submission_date ASC;");
$print_job1 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.hold_date AS hold_date, web_job.job_purpose AS job_purpose, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id=users.netlink_id INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status = 'on hold' ORDER BY web_job.hold_date ASC;");
$print_job6 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.priced_date AS priced_date, web_job.job_purpose AS job_purpose, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status = 'pending payment' ORDER BY web_job.priced_date ASC");
$print_job2 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.paid_date AS paid_date, web_job.job_purpose AS job_purpose, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status = 'paid' ORDER BY web_job.paid_date ASC");
$print_job3 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.printing_date AS printing_date, web_job.job_purpose AS job_purpose, users.name as name FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status = 'printing' ORDER BY web_job.printing_date ASC");
$print_job4 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.completed_date AS completed_date, web_job.job_purpose AS job_purpose, users.name as name FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status = 'completed' ORDER BY web_job.completed_date ASC");
$print_job5 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.delivered_date AS delivered_date, web_job.job_purpose AS job_purpose, users.name as name FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status = 'delivered' ORDER BY web_job.delivered_date ASC");
$print_job7 = $stm->fetchAll();



//pull only laser cutting strings from web_job

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.submission_date AS submission_date, web_job.job_purpose AS job_purpose, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id=users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status = 'submitted' ORDER BY web_job.submission_date ASC;");
$laser_job1 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.hold_date AS hold_date, web_job.job_purpose AS job_purpose, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id=users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status = 'on hold' ORDER BY web_job.hold_date ASC;");
$laser_job6 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.priced_date AS priced_date, web_job.job_purpose AS job_purpose, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status = 'pending payment' ORDER BY web_job.priced_date ASC");
$laser_job2 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.paid_date AS paid_date, web_job.job_purpose AS job_purpose, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status = 'paid' ORDER BY web_job.paid_date ASC");
$laser_job3 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.printing_date AS printing_date, web_job.job_purpose AS job_purpose, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status = 'printing' ORDER BY web_job.printing_date ASC");
$laser_job4 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.completed_date AS completed_date, web_job.job_purpose AS job_purpose, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status = 'completed' ORDER BY web_job.completed_date ASC");
$laser_job5 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.delivered_date AS delivered_date, web_job.job_purpose AS job_purpose, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status = 'delivered' ORDER BY web_job.delivered_date ASC");
$laser_job7 = $stm->fetchAll();



//3d_printing jobs

$d_not_priced = [];
$d_on_hold = [];
$d_pending_payment = [];
$d_paid = [];
$d_printing = [];
$d_completed = [];
$d_delivered =[];


foreach ($print_job1 as $job) {
  $d_not_priced[] = $job;
}
foreach ($print_job6 as $job) {
  $d_on_hold[] = $job;
}
foreach ($print_job2 as $job) {
  $d_pending_payment[] = $job;
}
foreach ($print_job3 as $job) {
  $d_paid[] = $job;
}
foreach ($print_job4 as $job) {
  $d_printing[] = $job;
}
foreach ($print_job5 as $job) {
  $d_completed[] = $job;
}
foreach ($print_job7 as $job) {
  $d_delivered[] = $job;
}


$l_not_priced = [];
$l_on_hold = [];
$l_pending_payment = [];
$l_paid = [];
$l_printing = [];
$l_completed = [];
$l_delivered = [];


foreach ($laser_job1 as $job) {
  $l_not_priced[] = $job;
}
foreach ($laser_job6 as $job) {
  $l_on_hold[] = $job;
}
foreach ($laser_job2 as $job) {
  $l_pending_payment[] = $job;
}
foreach ($laser_job3 as $job) {
  $l_paid[] = $job;
}
foreach ($laser_job4 as $job) {
  $l_printing[] = $job;
}
foreach ($laser_job5 as $job) {
  $l_completed[] = $job;
}
foreach ($laser_job7 as $job) {
  $l_delivered[] = $job;
}

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v4.0.1">
    <title>Admin Dashboard</title>
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
          font-size: 3.5.rem;
        }
      }

      .accordion {
        background-color: #eee;
        color: #444;
        cursor: pointer;
        padding: 18px;
        width: 100%;
        border: none;
        text-align: left;
        outline: none;
        font-size: 15px;
        transition: 0.4s;
      }

      .active, .accordion:hover {
        background-color: #ccc; 
      }

      .panel {
        padding: 0 18px;
        display: none;
        background-color: white;
        overflow: hidden;
      }

      .accordion:after {
        content: '\21A7'; /* Unicode character for "down" sign (↧) */
        font-size: 15px;
        color: #777;
        float: right;
        margin-left: 5px;
      }

      .active:after {
        content: "\21A5"; /* Unicode character for "up" sign (↥) */
        font-size: 15px;
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
        <div class="py-5 text-center">

            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="admin-reports.php?searchdate_start=<?php echo date("Y-m-01") ?>&searchdate_end=<?php echo date("Y-m-d") ?>&approved=on">
                        <button class="btn btn-primary btn-lg btn-block" class="form-control" type="submit" data-inline="true">Reports</button>
                    </a>
                </div>

                <div class="col-md-4 mb-3">
                    <a href="admin-print-history.php?searchdate_start=<?php echo date("Y-m-d") ?>">
                        <button class="btn btn-primary btn-lg btn-block" class="form-control" type="submit" data-inline="true">Job History</button>
                    </a>
                </div>

                <div class="col-md-4 mb-3">
                    <a href="admin-manage-printers.php">
                        <button class="btn btn-primary btn-lg btn-block" class="form-control" type="submit" data-inline="true">Manage Printers</button>
                    </a>
                </div>

                <div class="col-md-4 mb-3">
                    <a href="customer-dashboard.php">
                        <button class="btn btn-primary btn-lg btn-block" class="form-control" type="submit" data-inline="true">Personal Dashboard</button>
                    </a>
                </div>

                <div class="col-md-4 mb-3">

                </div>

                <div class="col-md-4 mb-3">
                    <a href="admin-manage-users.php?admin_only=on">
                        <button class="btn btn-primary btn-lg btn-block" class="form-control" type="submit" data-inline="true">Manage Users</button>
                    </a>
                </div>

            <div class="container">
          <div class="py-5 text-left">

<h2 id="3d-print-jobs">3D Print Jobs</h2>
  <p><a href="#laser-cut-jobs" >(Jump to Laser Cut jobs)</a></p>
  <button class="accordion active">Submitted</button>
    <div class="panel" style="display:block;">
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
            <tr>
              <!-- table header-->
              <th style="width:95px;">Name</th>
              <th style="width:95px;">Job</th>
              <th style="width:95px;">Submission Date</th>
              <th style="width:95px;">Status</th>
              <th style="width:20px;">Purpose</th>
            </tr>
          </thead>
          <tbody>
          <?php
          //Grab each item from each array
          foreach($d_not_priced as $row){
          ?>
          <tr>
            <td style="width:95px;"><?php echo $row["name"]; ?></td>
            <td style="width:95px;"><a href="admin-3d-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
            <td style="width:95px;"><?php echo $row["submission_date"]; ?></td>
            <td style="width:95px;"><?php echo $row["status"]; ?></td>
            <td style="width:20px;"><?php echo $row["job_purpose"]; ?></td>
          </tr>
          <?php
          } ?>
          </tbody>
        </table>
      </div>
    </div>
    
    <button class="accordion active">On Hold</button>
    <div class="panel" style="display:block;">
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
            <tr>
              <!-- table header-->
              <th style="width:95px;">Name</th>
              <th style="width:95px;">Job</th>
              <th style="width:95px;">Hold Date</th>
              <th style="width:95px;">Status</th>
              <th style="width:20px;">Purpose</th>
            </tr>
          </thead>
          <tbody>
          <?php
          //Grab each item from each array
          foreach($d_on_hold as $row){
          ?>
          <tr>
            <td style="width:95px;"><?php echo $row["name"]; ?></td>
            <td style="width:95px;"><a href="admin-3d-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
            <td style="width:95px;"><?php echo $row["hold_date"]; ?></td>
            <td style="width:95px;"><?php echo $row["status"]; ?></td>
            <td style="width:20px;"><?php echo $row["job_purpose"]; ?></td>
          </tr>
          <?php
          } ?>
          </tbody>
        </table>
      </div>
    </div>
  
  
  <button class="accordion">Pending Payment</button>
    <div class="panel">
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
            <tr>
              <!-- table header-->
              <th style="width:95px;">Name</th>
              <th style="width:95px;">Job</th>
              <th style="width:95px;">Priced Date</th>
              <th style="width:95px;">Status</th>
              <th style="width:20px;">Purpose</th>
              <!--  -->
            </tr>
          </thead>
          <tbody>
            <?php foreach ($d_pending_payment as $row) {
            ?>
            <tr>
              <td style="width:95px;"><?php echo $row["name"]; ?></td>
              <td style="width:95px;"><a href="admin-3d-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td style="width:95px;"><?php echo $row["priced_date"]; ?></td>
              <td style="width:95px;"><?php echo $row["status"]; ?></td>
              <td style="width:20px;"><?php echo $row["job_purpose"]; ?></td>

            </tr>
            <?php
            }?>
          </tbody>
        </table>
      </div>
    </div>
  
  <button class="accordion active">Paid</button>
    <div class="panel" style="display:block;">
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
            <tr>
              <!-- table header-->
              <th style="width:95px;">Name</th>
              <th style="width:95px;">Job</th>
              <th style="width:95px;">Payment Date</th>
              <th style="width:95px;">Status</th>
              <th style="width:20px;">Purpose</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($d_paid as $row) {
            ?>
            <tr>
              <td style="width:95px;"><?php echo $row["name"]; ?></td>
                <td><a href="admin-3d-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td style="width:95px;"><?php echo $row["paid_date"]; ?></td>
              <td style="width:95px;"><?php echo $row["status"]; ?></td>
              <td style="width:20px;"><?php echo $row["job_purpose"]; ?></td>
              
            </tr>
            <?php
            }?>
          </tbody>
        </table>
      </div>
    </div>
  
  <button class="accordion active">Printing</button>
    <div class="panel" style="display:block;">
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
            <tr>
              <!-- table header-->
              <th style="width:95px;">Name</th>
              <th style="width:95px;">Job</th>
              <th style="width:95px;">Print Start Date</th>
              <th style="width:95px;">Status</th>
              <th style="width:20px;">Purpose</th>
              
            </tr>
          </thead>
          <tbody>
            <?php foreach ($d_printing as $row) {
              ?>
              <tr>
                <td style="width:95px;"><?php echo $row["name"]; ?></td>
                <td style="width:95px;"><a href="admin-3d-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
                <td style="width:95px;"><?php echo $row["printing_date"]; ?></td>
                <td style="width:95px;"><?php echo $row["status"]; ?></td>
                <td style="width:20px;"><?php echo $row["job_purpose"]; ?></td>
              </tr>
              <?php
            }?>
          </tbody>
        </table>
      </div>
    </div>

    
  <button class="accordion active">Completed Print</button>
    <div class="panel" style="display:block;">
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
            <tr>
              <!-- table header-->
              <th style="width:95px;">Name</th>
              <th style="width:95px;">Job</th>
              <th style="width:95px;">Print Start Date</th>
              <th style="width:95px;">Status</th>
              <th style="width:20px;">Purpose</th>
              
            </tr>
          </thead>
          <tbody>
            <?php foreach ($d_completed as $row) {
              ?>
              <tr>
                <td style="width:95px;"><?php echo $row["name"]; ?></td>
                <td style="width:95px;"><a href="admin-3d-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
                <td style="width:95px;"><?php echo $row["completed_date"]; ?></td>
                <td style="width:95px;"><?php echo $row["status"]; ?></td>
                <td style="width:20px;"><?php echo $row["job_purpose"]; ?></td>
              </tr>
              <?php
            }?>
          </tbody>
        </table>
      </div>
    </div>
  
  <button class="accordion">Ready for pickup</button>
    <div class="panel">
      <div class="table-responsive">
        <table class="table table-striped table-md">
        <thead>
          <tr>
            <!-- table header-->
            <th style="width:95px;">Name</th>
            <th style="width:95px;">Job</th>
            <th style="width:95px;">Completion Date</th>
            <th style="width:95px;">Status</th>
            <th style="width:20px;">Purpose</th>
            
          </tr>
        </thead>
        <tbody>
          <?php foreach ($d_delivered as $row) {
          ?>
          <tr>
            <td style="width:95px;"><?php echo $row["name"]; ?></td>
            <td style="width:95px;"><a href="admin-3d-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
            <td style="width:95px;"><?php echo $row["delivered_date"]; ?></td>
            <td style="width:95px;"><?php echo $row["status"]; ?></td>
            <td style="width:20px;"><?php echo $row["job_purpose"]; ?></td>
          </tr>
          <?php
          }
          ?>
          </tbody>
        </table>
      </div>
    </div>

    

<h2><br><br><h2 id="laser-cut-jobs">Laser Cut Jobs</h2>
  <p><a href="#3d-print-jobs" >(Jump to 3D Print jobs)</a></p>

  <button class="accordion active">Submitted</button>
    <div class="panel" style="display:block;">
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
            <tr>
              <!-- table header-->
              <th style="width:95px;">Name</th>
              <th style="width:95px;">Job</th>
              <th style="width:95px;">Submission Date</th>
              <th style="width:95px;">Status</th>
              <th style="width:20px;">Purpose</th>
              
            </tr>
          </thead>
          <tbody>

          <?php
          //Grab each item from each array
          foreach($l_not_priced as $row){
          ?>
          <tr>
            <td style="width:95px;"><?php echo $row["name"]; ?></td>
            <td style="width:95px;"><a href="admin-laser-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
            <td style="width:95px;"><?php echo $row["submission_date"]; ?></td>
            <td style="width:95px;"><?php echo $row["status"]; ?></td>
            <td style="width:20px;"><?php echo $row["job_purpose"]; ?></td>
          </tr>
          <?php
          } ?>
          </tbody>
        </table>
      </div>
    </div>

  <button class="accordion active">On Hold</button>
      <div class="panel" style="display:block;">
        <div class="table-responsive">
          <table class="table table-striped table-md">
            <thead>
              <tr>
                <!-- table header-->
                <th style="width:95px;">Name</th>
                <th style="width:95px;">Job</th>
                <th style="width:95px;">Hold Date</th>
                <th style="width:95px;">Status</th>
                <th style="width:20px;">Purpose</th>
                
              </tr>
            </thead>
            <tbody>

            <?php
            //Grab each item from each array
            foreach($l_on_hold as $row){
            ?>
            <tr>
              <td style="width:95px;"><?php echo $row["name"]; ?></td>
              <td style="width:95px;"><a href="admin-laser-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td style="width:95px;"><?php echo $row["hold_date"]; ?></td>
              <td style="width:95px;"><?php echo $row["status"]; ?></td>
              <td style="width:20px;"><?php echo $row["job_purpose"]; ?></td>
            </tr>
            <?php
            } ?>
            </tbody>
          </table>
        </div>
      </div>
  

  <button class="accordion">Pending Payment</button>
    <div class="panel">
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
            <tr>
              <!-- table header-->
              <th style="width:95px;">Name</th>
              <th style="width:95px;">Job</th>
              <th style="width:95px;">Priced Date</th>
              <th style="width:95px;">Status</th>
              <th style="width:95px;">Purpose</th>
              
            </tr>
          </thead>
          <tbody>

          <?php foreach ($l_pending_payment as $row) {
          ?>
          <tr>
            <td style="width:95px;"><?php echo $row["name"]; ?></td>
            <td style="width:95px;"><a href="admin-laser-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
            <td style="width:95px;"><?php echo $row["priced_date"]; ?></td>
            <td style="width:95px;"><?php echo $row["status"]; ?></td>
            <td style="width:20px;"><?php echo $row["job_purpose"]; ?></td>

          </tr>
          <?php
          }
          ?>
          </tbody>
        </table>
      </div>
    </div>

  <button class="accordion active">Paid</button> 
    <div class="panel" style="display:block;">
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
            <tr>
              <!-- table header-->
              <th style="width:95px;">Name</th>
              <th style="width:95px;">Job</th>
              <th style="width:95px;">Payment Date</th>
              <th style="width:95px;">Status</th>
              <th style="width:20px;">Purpose</th>
              
            </tr>
          </thead>
          <tbody>

          <?php foreach ($l_paid as $row) {
          ?>
          <tr>
            <td style="width:95px;"><?php echo $row["name"]; ?></td>
            <td style="width:95px;"><a href="admin-laser-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
            <td style="width:95px;"><?php echo $row["paid_date"]; ?></td>
            <td style="width:95px;"><?php echo $row["status"]; ?></td>
            <td style="width:20px;"><?php echo $row["job_purpose"]; ?></td>

          </tr>
          <?php
          }
          ?>
          </tbody>
        </table>
      </div>
    </div>

  <button class="accordion active">Cutting</button> 
    <div class="panel" style="display:block;">
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
            <tr>
              <!-- table header-->
              <th style="width:95px;">Name</th>
              <th style="width:95px;">Job</th>
              <th style="width:95px;">Cut Start Date</th>
              <th style="width:95px;">Status</th>
              <th style="width:20px;">Purpose</th>
              
            </tr>
          </thead>
          <tbody>

          <?php foreach ($l_printing as $row) {
          ?>
          <tr>
            <td style="width:95px;"><?php echo $row["name"]; ?></td>
            <td style="width:95px;"><a href="admin-laser-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
            <td style="width:95px;"><?php echo $row["printing_date"]; ?></td>
            <td style="width:95px;"><?php echo $row["status"]; ?></td>
            <td style="width:20px;"><?php echo $row["job_purpose"]; ?></td>

          </tr>
          <?php
          }
          ?>
          </tbody>
        </table>
      </div>
    </div>

  
  <button class="accordion active">Completed</button> 
    <div class="panel" style="display:block;">
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
            <tr>
              <!-- table header-->
              <th style="width:95px;">Name</th>
              <th style="width:95px;">Job</th>
              <th style="width:95px;">Completed Cut Date</th>
              <th style="width:95px;">Status</th>
              <th style="width:20px;">Purpose</th>
              
            </tr>
          </thead>
          <tbody>

          <?php foreach ($l_completed as $row) {
          ?>
          <tr>
            <td style="width:95px;"><?php echo $row["name"]; ?></td>
            <td style="width:95px;"><a href="admin-laser-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
            <td style="width:95px;"><?php echo $row["completed_date"]; ?></td>
            <td style="width:95px;"><?php echo $row["status"]; ?></td>
            <td style="width:20px;"><?php echo $row["job_purpose"]; ?></td>

          </tr>
          <?php
          }
          ?>
          </tbody>
        </table>
      </div>
    </div>
  
  <button class="accordion">Ready for pickup</button>
    <div class="panel">
      <div class="table-responsive">
        <table class="table table-striped table-md">
        <thead>
          <tr>
            <!-- table header-->
            <th style="width:95px;">Name</th>
            <th style="width:95px;">Job</th>
            <th style="width:95px;">Delivery Date</th>
            <th style="width:95px;">Status</th>
            <th style="width:20px;">Purpose</th>
            
          </tr>
        </thead>
        <tbody>
        <?php foreach ($l_delivered as $row) {
          ?>
          <tr>
            <td style="width:95px;"><?php echo $row["name"]; ?></td>
            <td style="width:95px;"><a href="admin-laser-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
            <td style="width:95px;"><?php echo $row["completed_date"]; ?></td>
            <td style="width:95px;"><?php echo $row["status"]; ?></td>
            <td style="width:20px;"><?php echo $row["job_purpose"]; ?></td>
          </tr>
          <?php
          }
          ?>
          </tbody>
        </table>
      </div>
    </div>

        <hr class="mb-12">
        <a class="btn btn-md btn-block" href="?logout=" role="button">Log Out</a>
    </div>
  </div>

<script>
  var acc = document.getElementsByClassName("accordion");
  var i;

  for (i = 0; i < acc.length; i++) {
    acc[i].addEventListener("click", function() {
      this.classList.toggle("active");
      var panel = this.nextElementSibling;
      if (panel.style.display === "block") {
        panel.style.display = "none";
      } else {
        panel.style.display = "block";
      }
    });
  }
</script>
  
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
      <script>window.jQuery || document.write('<script src="/docs/4.5/assets/js/vendor/jquery.slim.min.js"><\/script>')</script><script src="/docs/4.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-1CmrxMRARb6aLqgBO7yyAxTOQE2AKb9GfXnEo760AUcUmFx3ibVJJAzGytlQcNXd" crossorigin="anonymous"></script>
        <script src="form-validation.js"></script></body>
</html>
