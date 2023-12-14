<?php
session_start();
require ('auth-sec.php'); //Gets CAS & db
//auth-sec includes: $user, $user_email, $user_type, $user_name

$stm = $conn->query("SELECT id, job_name, status, submission_date, priced_date, paid_date, printing_date, completed_date, cancelled_date, hold_date, delivered_date FROM web_job INNER JOIN 3d_print_job ON id=3d_print_id WHERE netlink_id = '$user' ORDER BY id DESC");
$data = $stm->fetchAll();
//split results by Status
$d_submitted=[];
$d_pending_payment = [];
$d_onhold=[];
$d_paid=[];
$d_inprogress_completed =[];
$d_delivered=[];
$d_cancelled = [];
$d_archived=[];

foreach ($data as $job) {
  if($job['status']=="submitted"){
    $d_submitted[] = $job;
  }
  elseif ($job['status'] == "pending payment") {
    $d_pending_payment[] = $job;
  }
  
  elseif ($job['status'] == "on hold") {
    $d_onhold[] = $job;
  }

  elseif ($job['status'] == "paid") {
    $d_paid[] = $job;
  }
  elseif ($job['status'] == "completed" || $job['status'] == "printing") {
    $d_inprogress_completed[] = $job;
  }
  elseif ($job['status'] == "delivered"){
    $d_delivered[] = $job;
  } 
  elseif($job['status']=="cancelled"){
    $d_cancelled[] = $job;
  }
  elseif($job['status']=="archived"){
    $d_archived[] = $job;
  }
}

$stm = $conn->query("SELECT id, job_name, status, submission_date, priced_date, paid_date, printing_date, completed_date, cancelled_date, hold_date, delivered_date FROM web_job INNER JOIN laser_cut_job ON id=laser_cut_id WHERE netlink_id = '$user' ORDER BY id DESC");
$data = $stm->fetchAll();
//split results by Status
$l_submitted=[];
$l_pending_payment = [];
$l_onhold=[];
$l_paid = [];
$l_inprogress_completed =[];
$l_delivered=[];
$l_cancelled = [];
$l_archived=[];

foreach ($data as $job) {
  if($job['status']=="submitted"){
    $l_submitted[] = $job;
  }
  elseif ($job['status'] == "pending payment") {
    $l_pending_payment[] = $job;
  }
  elseif ($job['status'] == "on hold") {
    $l_onhold[] = $job;
  }
  elseif ($job['status'] == "paid") {
    $l_paid[] = $job;
  }
  elseif ($job['status'] == "completed" || $job['status'] == "printing") {
    $l_inprogress_completed[] = $job;
  }
  elseif ($job['status'] == "delivered"){
    $l_delivered[] = $job;
  } 
  elseif($job['status']=="cancelled"){
    $l_cancelled[] = $job;
  }
  elseif($job['status']=="archived"){
    $l_archived[] = $job;
  }
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
    <title>Your Dashboard</title>
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

    <h1><b> DSC 3D Printing and Laser Cutting Dashboard</b></h1>
    <p><b><p><b>If you encounter a problem or have questions, contact: <a href=“mailto:dscommons@uvic.ca”>dscommons@uvic.ca</a></b></p>
  </div>

  <div class="row">
    <div class="col-md-4 order-md-2 mb-4">
      <h4 class="d-flex justify-content-between align-items-center mb-3">
    </div>

    <div class="col-md-12 order-md-1">
      <div class="py-5 text-center">

        <div class="row">
          <div class="btn-auto mr-auto">
          <a href="customer-new-job.php">
            <button class="btn btn-primary btn-lg" type="submit">Create New Project</button>
          </a>
          </div>

        <div class="btn-auto mr-auto">
          <a href="https://onlineacademiccommunity.uvic.ca/dsc/how-to-3d-print/">
            <button class="btn btn-primary btn-lg" type="submit" style="background-color:#5e8669;">3D Print FAQ</button>
          </a>
        </div>

        <div class="btn-auto mr-auto">
          <a href="https://onlineacademiccommunity.uvic.ca/dsc/how-to-laser-cut/">
            <button class="btn btn-primary btn-lg" type="submit" style="background-color:#5e8669;">Laser Cut FAQ</button>
          </a>
        </div>

        <div class="btn-auto mr-auto">
          <?php if ($user_type == 0){ ?>
            <a href="admin-dashboard.php">
              <button class="btn btn-primary btn-lg" type="submit">Admin Dashboard</button>
            </a>
          <?php } else{ ?>
            <a href=" mailto:dscommons@uvic.ca?subject=3DAppFeedback">
              <button class="btn btn-primary btn-lg" type="submit">Feedback</button>
            </a>
          <?php }  ?>
        </div>
        
      </div>

    </div>
<hr class="mb-12">

      <!-- 3d print table -->

      <h2>3D Print Jobs</h2>
      <div class="table-responsive">
        <table class="table table-striped table-md">
        <tbody>
            <thead>
              <tr>
                <th>Name</th>
                <th>Status</th>
                <th>Last Updated</th>
              </tr>
            </thead>

            <!--submitted jobs-->
            <?php if (!empty($d_submitted)) {
               foreach ($d_submitted as $row) {
            ?>
            <tr>
              <td><a href="customer-3d-job-information.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td><?php echo $row["status"]; ?></td>
              <td><?php echo $row["submission_date"]; ?></td>
            </tr>
            <?php
            }
            }

            //pending payment jobs
            if (!empty($d_pending_payment)) {
               foreach ($d_pending_payment as $row) {
            ?>
            <tr>
              <td><a href="customer-3d-job-information.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td><?php echo $row["status"]; ?></td>
              <td><?php echo $row["priced_date"]; ?></td>
            </tr>
            <?php
            }
            }

            //pending payment jobs
            if (!empty($d_onhold)) {
               foreach ($d_onhold as $row) {
            ?>
            <tr>
              <td><a href="customer-3d-job-information.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td><?php echo $row["status"]; ?></td>
              <td><?php echo $row["hold_date"]; ?></td>
            </tr>
            <?php
            }
            }

            //Paid jobs
            if (!empty($d_paid)) {
               foreach ($d_paid as $row) {
              ?>
              <tr>
                <td><a href="customer-3d-job-information.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
                <td><?php echo $row["status"]; ?></td>
                <td><?php echo $row["paid_date"]; ?></td>
              </tr>
              <?php
              }
            }
            
            //In Progress (printing and completed) jobs
            if (!empty($d_inprogress_completed)) {
               foreach ($d_inprogress_completed as $row) {
              ?>
              <tr>
                <td><a href="customer-3d-job-information.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
                <td>in progress</td>
                
                <?php if ($row["status"] == "printing") { ?>
                <td><?php echo $row["printing_date"]; ?></td>
              <?php } else { ?>
                <td><?php echo $row["completed_date"]; ?></td>
              <?php } ?>
              </tr>
              <?php
              }
            }

            //Delivered jobs
            if (!empty($d_delivered)) {
              foreach ($d_delivered as $row) {
              ?>
              <tr>
                <td><a href="customer-3d-job-information.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
                <td>ready for pickup</td>
                <td><?php echo $row["delivered_date"]; ?></td>
              </tr>
              <?php
              }
            }

            //Cancelled jobs
            if (!empty($d_cancelled)) {
               foreach ($d_cancelled as $row) {
              ?>
              <tr>
                <td><a href="customer-3d-job-information.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
                <td><?php echo $row["status"]; ?></td>
                <td><?php echo $row["cancelled_date"]; ?></td>
              </tr>
              <?php
              }
            }

            //archived jobs
            if (!empty($d_archived)) {
               foreach ($d_archived as $row) {
              ?>
              <tr>
                <td><a href="customer-3d-job-information.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
                <td><?php echo $row["status"]; ?></td>
                <td><?php echo $row["delivered_date"]; ?></td>
              </tr>
              <?php
              }
            }
          ?>
           </tbody>
        </table>
      </div>
        
        <hr class="mb-12">

      <!-- Laser Cut table -->

      <h2>Laser Cut Jobs</h2>
      <div class="table-responsive">
        <table class="table table-striped table-md">
        <tbody>
            <thead>
              <tr>
                <th>Name</th>
                <th>Status</th>
                <th>Last Updated</th>
              </tr>
            </thead>

            <!--submitted jobs-->
            <?php if (!empty($l_submitted)) {
               foreach ($l_submitted as $row) {
            ?>
            <tr>
              <td><a href="customer-3d-job-information.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td><?php echo $row["status"]; ?></td>
              <td><?php echo $row["submission_date"]; ?></td>
            </tr>
            <?php
            }
            }

            //pending payment jobs
            if (!empty($l_pending_payment)) {
               foreach ($l_pending_payment as $row) {
            ?>
            <tr>
              <td><a href="customer-3d-job-information.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td><?php echo $row["status"]; ?></td>
              <td><?php echo $row["priced_date"]; ?></td>
            </tr>
            <?php
            }
            }

            //pending payment jobs
            if (!empty($l_onhold)) {
               foreach ($l_onhold as $row) {
            ?>
            <tr>
              <td><a href="customer-3d-job-information.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td><?php echo $row["status"]; ?></td>
              <td><?php echo $row["hold_date"]; ?></td>
            </tr>
            <?php
            }
            }

            //Paid jobs
            if (!empty($l_paid)) {
               foreach ($l_paid as $row) {
              ?>
              <tr>
                <td><a href="customer-3d-job-information.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
                <td><?php echo $row["status"]; ?></td>
                <td><?php echo $row["paid_date"]; ?></td>
              </tr>
              <?php
              }
            }
            
            //In Progress (printing and completed) jobs
            if (!empty($l_inprogress_completed)) {
               foreach ($l_inprogress_completed as $row) {
              ?>
              <tr>
                <td><a href="customer-3d-job-information.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
                <td>in progress</td>
                
                <?php if ($row["status"] == "printing") { ?>
                <td><?php echo $row["printing_date"]; ?></td>
              <?php } else { ?>
                <td><?php echo $row["completed_date"]; ?></td>
              <?php } ?>
              </tr>
              <?php
              }
            }

            //Delivered jobs
            if (!empty($l_delivered)) {
              foreach ($l_delivered as $row) {
              ?>
              <tr>
                <td><a href="customer-3d-job-information.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
                <td>ready for pickup</td>
                <td><?php echo $row["delivered_date"]; ?></td>
              </tr>
              <?php
              }
            }

            //Cancelled jobs
            if (!empty($l_cancelled)) {
               foreach ($l_cancelled as $row) {
              ?>
              <tr>
                <td><a href="customer-3d-job-information.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
                <td><?php echo $row["status"]; ?></td>
                <td><?php echo $row["cancelled_date"]; ?></td>
              </tr>
              <?php
              }
            }

            //archived jobs
            if (!empty($l_archived)) {
               foreach ($l_archived as $row) {
              ?>
              <tr>
                <td><a href="customer-3d-job-information.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
                <td><?php echo $row["status"]; ?></td>
                <td><?php echo $row["delivered_date"]; ?></td>
              </tr>
              <?php
              }
            }
          ?>
           </tbody>
        </table>
      </div>
        <hr class="mb-12">

        <a class="btn btn-md btn-block" href="?logout=" role="button">Log Out</a>

    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
      <script>window.jQuery || document.write('<script src="/docs/4.5/assets/js/vendor/jquery.slim.min.js"><\/script>')</script><script src="/docs/4.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-1CmrxMRARb6aLqgBO7yyAxTOQE2AKb9GfXnEo760AUcUmFx3ibVJJAzGytlQcNXd" crossorigin="anonymous"></script>
        <script src="form-validation.js"></script></body>
</html>
