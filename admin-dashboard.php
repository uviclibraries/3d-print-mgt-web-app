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

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.submission_date AS submission_date, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id=users.netlink_id INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status = 'submitted' ORDER BY web_job.submission_date ASC;");
$print_job1 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.priced_date AS priced_date, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status = 'pending payment' ORDER BY web_job.priced_date ASC");
$print_job2 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.paid_date AS paid_date, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status = 'paid' ORDER BY web_job.paid_date ASC");
$print_job3 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.printing_date AS printing_date, users.name as name FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status = 'printing' ORDER BY web_job.printing_date ASC");
$print_job4 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.completed_date AS completed_date, users.name as name FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status = 'completed' ORDER BY web_job.completed_date ASC");
$print_job5 = $stm->fetchAll();

//pull only laser cutting strings from web_job

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.submission_date AS submission_date, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id=users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status = 'submitted' ORDER BY web_job.submission_date ASC;");
$laser_job1 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.priced_date AS priced_date, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status = 'pending payment' ORDER BY web_job.priced_date ASC");
$laser_job2 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.paid_date AS paid_date, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status = 'paid' ORDER BY web_job.paid_date ASC");
$laser_job3 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.printing_date AS printing_date, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status = 'printing' ORDER BY web_job.printing_date ASC");
$laser_job4 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.completed_date AS completed_date, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status = 'completed' ORDER BY web_job.completed_date ASC");
$laser_job5 = $stm->fetchAll();

//3d_printing jobs

$d_not_priced = [];
$d_pending_payment = [];
$d_paid = [];
$d_printing = [];
$d_complete = [];

foreach ($print_job1 as $job) {
  $d_not_priced[] = $job;
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
  $d_complete[] = $job;
}

$l_not_priced = [];
$l_pending_payment = [];
$l_paid = [];
$l_printing = [];
$l_complete = [];

foreach ($laser_job1 as $job) {
  $l_not_priced[] = $job;
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
  $l_complete[] = $job;
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
                        <button class="btn btn-primary btn-lg btn-block" class="form-control" type="submit" data-inline="true">Print History</button>
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

            <h3>3D Print Jobs</h3>
            <div class="py-3"></div>
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
            <tr>
              <!-- table header-->
              <th>Name</th>
              <th>Job</th>
              <th>Submission Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>

          <?php
            //Grab each item from each array
            foreach($d_not_priced as $row){
            ?>
            <tr>
              <td><?php echo $row["name"]; ?></td>
              <td><a href="admin-3d-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td> <?php echo $row["submission_date"]; ?></td>
              <td><?php echo $row["status"]; ?></td>
            </tr>
            <?php
            } ?>
            <tr>
              <!-- empty row-->
              <td> </td>
              <td> </td>
              <td> </td>
              <td> </td>
            </tr>
            <tr>
              <thread>
                <th>Name</th>
                <th>Job</th>
                <th>Priced Date</th>
                <th>Status</th>
              </thread>
            </tr>
            <?php foreach ($d_pending_payment as $row) {
            ?>
            <tr>
              <td><?php echo $row["name"]; ?></td>
                <td><a href="admin-3d-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td><?php echo $row["priced_date"]; ?></td>
              <td><?php echo $row["status"]; ?></td>
            </tr>
            <?php
            }
            ?>
            <tr>
              <td> </td>
              <td> </td>
              <td> </td>
              <td> </td>
            </tr>
            <tr>
            <tr>
              <thread>
                <th>Name</th>
                <th>Job</th>
                <th>Payment Date</th>
                <th>Status</th>
              </thread>
            </tr>
            <?php foreach ($d_paid as $row) {
            ?>
            <tr>
              <td><?php echo $row["name"]; ?></td>
                <td><a href="admin-3d-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td><?php echo $row["paid_date"]; ?></td>
              <td><?php echo $row["status"]; ?></td>
            </tr>
            <?php
            }
            ?>
            <tr>
              <td> </td>
              <td> </td>
              <td> </td>
              <td> </td>
            </tr>
            <tr>
            <tr>
              <thread>
                <th>Name</th>
                <th>Job</th>
                <th>Print Start Date</th>
                <th>Status</th>
              </thread>
            </tr>
            <?php foreach ($d_printing as $row) {
            ?>
            <tr>
              <td><?php echo $row["name"]; ?></td>
                <td><a href="admin-3d-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td><?php echo $row["printing_date"]; ?></td>
              <td><?php echo $row["status"]; ?></td>
            </tr>
            <?php
            }
            ?>
            <tr>
              <td> </td>
              <td> </td>
              <td> </td>
              <td> </td>
            </tr>
            <tr>
            <tr>
              <thread>
                <th>Name</th>
                <th>Job</th>
                <th>Completion Date</th>
                <th>Status</th>
              </thread>
            </tr>
            <?php foreach ($d_complete as $row) {
            ?>
            <tr>
              <td><?php echo $row["name"]; ?></td>
                <td><a href="admin-3d-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td><?php echo $row["completed_date"]; ?></td>
              <td><?php echo $row["status"]; ?></td>
            </tr>
            <?php
            }
            ?>

          </tbody>
        </table>
      </div>

      <h3>Laser Cut Jobs</h3>
            <div class="py-3"></div>
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
            <tr>
              <!-- table header-->
              <th>Name</th>
              <th>Job</th>
              <th>Submission Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>

          <?php
            //Grab each item from each array
            foreach($l_not_priced as $row){
            ?>
            <tr>
              <td><?php echo $row["name"]; ?></td>
              <td><a href="admin-laser-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td> <?php echo $row["submission_date"]; ?></td>
              <td><?php echo $row["status"]; ?></td>
            </tr>
            <?php
            } ?>
            <tr>
              <!-- empty row-->
              <td> </td>
              <td> </td>
              <td> </td>
              <td> </td>
            </tr>
            <tr>
              <thread>
                <th>Name</th>
                <th>Job</th>
                <th>Priced Date</th>
                <th>Status</th>
              </thread>
            </tr>
            <?php foreach ($l_pending_payment as $row) {
            ?>
            <tr>
              <td><?php echo $row["name"]; ?></td>
                <td><a href="admin-laser-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td><?php echo $row["priced_date"]; ?></td>
              <td><?php echo $row["status"]; ?></td>
            </tr>
            <?php
            }
            ?>
            <tr>
              <td> </td>
              <td> </td>
              <td> </td>
              <td> </td>
            </tr>
            <tr>
            <tr>
              <thread>
                <th>Name</th>
                <th>Job</th>
                <th>Payment Date</th>
                <th>Status</th>
              </thread>
            </tr>
            <?php foreach ($l_paid as $row) {
            ?>
            <tr>
              <td><?php echo $row["name"]; ?></td>
                <td><a href="admin-laser-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td><?php echo $row["paid_date"]; ?></td>
              <td><?php echo $row["status"]; ?></td>
            </tr>
            <?php
            }
            ?>
            <tr>
              <td> </td>
              <td> </td>
              <td> </td>
              <td> </td>
            </tr>
            <tr>
            <tr>
              <thread>
                <th>Name</th>
                <th>Job</th>
                <th>Cut Start Date</th>
                <th>Status</th>
              </thread>
            </tr>
            <?php foreach ($l_printing as $row) {
            ?>
            <tr>
              <td><?php echo $row["name"]; ?></td>
                <td><a href="admin-laser-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td><?php echo $row["printing_date"]; ?></td>
              <td><?php echo $row["status"]; ?></td>
            </tr>
            <?php
            }
            ?>
            <tr>
              <td> </td>
              <td> </td>
              <td> </td>
              <td> </td>
            </tr>
            <tr>
            <tr>
              <thread>
                <th>Name</th>
                <th>Job</th>
                <th>Completion Date</th>
                <th>Status</th>
              </thread>
            </tr>
            <?php foreach ($l_complete as $row) {
            ?>
            <tr>
              <td><?php echo $row["name"]; ?></td>
                <td><a href="admin-laser-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td><?php echo $row["completed_date"]; ?></td>
              <td><?php echo $row["status"]; ?></td>
            </tr>
            <?php
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
