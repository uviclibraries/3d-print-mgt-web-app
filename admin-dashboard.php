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

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.submission_date AS submission_date, web_job.job_purpose AS job_purpose, users.name AS name, web_job.is_parent AS is_parent, web_job.parent_job_id AS parent_job_id FROM web_job INNER JOIN users ON web_job.netlink_id=users.netlink_id INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status = 'submitted' ORDER BY web_job.submission_date ASC;");
$print_job1 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.hold_date AS hold_date, web_job.job_purpose AS job_purpose, users.name AS name, web_job.is_parent AS is_parent, web_job.parent_job_id AS parent_job_id FROM web_job INNER JOIN users ON web_job.netlink_id=users.netlink_id INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status = 'on hold' ORDER BY web_job.hold_date ASC;");
$print_job6 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.priced_date AS priced_date, web_job.job_purpose AS job_purpose, users.name AS name, web_job.is_parent AS is_parent, web_job.parent_job_id AS parent_job_id FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status = 'pending payment' ORDER BY web_job.priced_date ASC");
$print_job2 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.paid_date AS paid_date, web_job.job_purpose AS job_purpose, users.name AS name, web_job.is_parent AS is_parent, web_job.parent_job_id AS parent_job_id FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status = 'paid' ORDER BY web_job.paid_date ASC");
$print_job3 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.printing_date AS printing_date, web_job.job_purpose AS job_purpose, users.name as name, web_job.is_parent AS is_parent, web_job.parent_job_id AS parent_job_id FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status = 'printing' ORDER BY web_job.printing_date ASC");
$print_job4 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.completed_date AS completed_date, web_job.job_purpose AS job_purpose, users.name as name, web_job.is_parent AS is_parent, web_job.parent_job_id AS parent_job_id FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status = 'completed' ORDER BY web_job.completed_date ASC");
$print_job5 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.delivered_date AS delivered_date, web_job.job_purpose AS job_purpose, users.name as name, web_job.is_parent AS is_parent, web_job.parent_job_id AS parent_job_id FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status = 'delivered' ORDER BY web_job.delivered_date ASC");
$print_job7 = $stm->fetchAll();



//pull only laser cutting strings from web_job

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.submission_date AS submission_date, web_job.job_purpose AS job_purpose, users.name AS name, web_job.is_parent AS is_parent, web_job.parent_job_id AS parent_job_id FROM web_job INNER JOIN users ON web_job.netlink_id=users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status = 'submitted' ORDER BY web_job.submission_date ASC;");
$laser_job1 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.hold_date AS hold_date, web_job.job_purpose AS job_purpose, users.name AS name, web_job.is_parent AS is_parent, web_job.parent_job_id AS parent_job_id FROM web_job INNER JOIN users ON web_job.netlink_id=users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status = 'on hold' ORDER BY web_job.hold_date ASC;");
$laser_job6 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.priced_date AS priced_date, web_job.job_purpose AS job_purpose, users.name AS name, web_job.is_parent AS is_parent, web_job.parent_job_id AS parent_job_id FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status = 'pending payment' ORDER BY web_job.priced_date ASC");
$laser_job2 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.paid_date AS paid_date, web_job.job_purpose AS job_purpose, users.name AS name, web_job.is_parent AS is_parent, web_job.parent_job_id AS parent_job_id FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status = 'paid' ORDER BY web_job.paid_date ASC");
$laser_job3 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.printing_date AS printing_date, web_job.job_purpose AS job_purpose, users.name AS name, web_job.is_parent AS is_parent, web_job.parent_job_id AS parent_job_id FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status = 'printing' ORDER BY web_job.printing_date ASC");
$laser_job4 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.completed_date AS completed_date, web_job.job_purpose AS job_purpose, users.name AS name, web_job.is_parent AS is_parent, web_job.parent_job_id AS parent_job_id FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status = 'completed' ORDER BY web_job.completed_date ASC");
$laser_job5 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.delivered_date AS delivered_date, web_job.job_purpose AS job_purpose, users.name AS name, web_job.is_parent AS is_parent, web_job.parent_job_id AS parent_job_id FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status = 'delivered' ORDER BY web_job.delivered_date ASC");
$laser_job7 = $stm->fetchAll();


//pull only large format strings from web_job

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.submission_date AS submission_date, web_job.job_purpose AS job_purpose, users.name AS name, web_job.is_parent AS is_parent, web_job.parent_job_id AS parent_job_id FROM web_job INNER JOIN users ON web_job.netlink_id=users.netlink_id INNER JOIN large_format_print_job ON web_job.id=large_format_print_job.large_format_print_id WHERE web_job.status = 'submitted' ORDER BY web_job.submission_date ASC;");
$largeformat_job1 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.hold_date AS hold_date, web_job.job_purpose AS job_purpose, users.name AS name, web_job.is_parent AS is_parent, web_job.parent_job_id AS parent_job_id FROM web_job INNER JOIN users ON web_job.netlink_id=users.netlink_id INNER JOIN large_format_print_job ON web_job.id=large_format_print_job.large_format_print_id WHERE web_job.status = 'on hold' ORDER BY web_job.hold_date ASC;");
$largeformat_job6 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.priced_date AS priced_date, web_job.job_purpose AS job_purpose, users.name AS name, web_job.is_parent AS is_parent, web_job.parent_job_id AS parent_job_id FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN large_format_print_job ON web_job.id=large_format_print_job.large_format_print_id WHERE web_job.status = 'pending payment' ORDER BY web_job.priced_date ASC");
$largeformat_job2 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.paid_date AS paid_date, web_job.job_purpose AS job_purpose, users.name AS name, web_job.is_parent AS is_parent, web_job.parent_job_id AS parent_job_id FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN large_format_print_job ON web_job.id=large_format_print_job.large_format_print_id WHERE web_job.status = 'paid' ORDER BY web_job.paid_date ASC");
$largeformat_job3 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.printing_date AS printing_date, web_job.job_purpose AS job_purpose, users.name AS name, web_job.is_parent AS is_parent, web_job.parent_job_id AS parent_job_id FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN large_format_print_job ON web_job.id=large_format_print_job.large_format_print_id WHERE web_job.status = 'printing' ORDER BY web_job.printing_date ASC");
$largeformat_job4 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.completed_date AS completed_date, web_job.job_purpose AS job_purpose, users.name AS name, web_job.is_parent AS is_parent, web_job.parent_job_id AS parent_job_id FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN large_format_print_job ON web_job.id=large_format_print_job.large_format_print_id WHERE web_job.status = 'completed' ORDER BY web_job.completed_date ASC");
$largeformat_job5 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.delivered_date AS delivered_date, web_job.job_purpose AS job_purpose, users.name AS name, web_job.is_parent AS is_parent, web_job.parent_job_id AS parent_job_id FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN large_format_print_job ON web_job.id=large_format_print_job.large_format_print_id WHERE web_job.status = 'delivered' ORDER BY web_job.delivered_date ASC");
$largeformat_job7 = $stm->fetchAll();



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


//laser jobs
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

//Large Format Print jobs
$lf_not_priced = [];
$lf_on_hold = [];
$lf_pending_payment = [];
$lf_paid = [];
$lf_printing = [];
$lf_completed = [];
$lf_delivered = [];


foreach ($largeformat_job1 as $job) {
  $lf_not_priced[] = $job;
}
foreach ($largeformat_job6 as $job) {
  $lf_on_hold[] = $job;
}
foreach ($largeformat_job2 as $job) {
  $lf_pending_payment[] = $job;
}
foreach ($largeformat_job3 as $job) {
  $lf_paid[] = $job;
}
foreach ($largeformat_job4 as $job) {
  $lf_printing[] = $job;
}
foreach ($largeformat_job5 as $job) {
  $lf_completed[] = $job;
}
foreach ($largeformat_job7 as $job) {
  $lf_delivered[] = $job;
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
    <link rel="stylesheet" href="css/accordion_styles.css">
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
          <?php
            // Create a DateTime object for the current date
            $prior10Days = (new DateTime())->modify('-10 days')->format('Y-m-d');
            // echo $prior10Days;

          ?>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="admin-reports.php?searchdate_start=<?php echo $prior10Days ?>&searchdate_end=<?php echo date("Y-m-d") ?>&approved=on">
                        <button class="btn btn-primary btn-lg btn-block" class="form-control" type="submit" data-inline="true">Reports</button>
                    </a>
                </div>

                <div class="col-md-4 mb-3">
                    <a href="admin-print-history.php?searchdate_start=<?php echo $prior10Days ?>&searchdate_end=<?php echo date("Y-m-d") ?>">
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

<?php 
function displayJobName($row) {
  $no_link_statues = array("cancelled", "archived", "delivered");
  $display_name = "";

  if (!in_array($row['status'], $no_link_statues)) {
    if ($row['parent_job_id'] > 0) {
      $display_name = '&copy ';
      // echo $display_name;
    } elseif ($row['is_parent']) {
      $display_name = '&#9413 ';
      // echo $display_name;
    }
  } 
  $display_name = $display_name . $row["job_name"];
    // echo $display_name;
  return $display_name;
}

$d_href='admin-3d-job-specification.php?job_id=';
$l_href= 'admin-laser-job-specification.php?job_id=';
$lf_href='admin-large-format-print-job-specification.php?job_id=';

function generateTable($table_id, $rel_jobs, $job_ref, $status_date, $date_header) {
    echo '  <div class="table-responsive" style="width: 100%; table-layout: fixed;">';
    echo '    <table id="' . $table_id . '" class="table table-striped table-md">';
    echo '      <thead>';
    echo '        <tr>';
    echo '          <th style="width:15%;">Name</th>';
    echo '          <th style="width:50%;">Job</th>';
    echo '          <th style="width:20%;">' . $date_header . '</th>';
    echo '          <th style="width:15%;">Purpose</th>';
    echo '        </tr>';
    echo '      </thead>';
    echo '      <tbody>';

    foreach ($rel_jobs as $row) {
        echo '        <tr>';
        echo '          <td style="width:15%;">' . $row["name"] . '</td>';
        echo '          <td style="width:50%;"><a href="' .$job_ref. $row["id"] . '">' . displayJobName($row) . '</a></td>';
        echo '          <td style="width:20%;">' . $row[$status_date] . '</td>';
        echo '          <td style="width:15%;">' . $row["job_purpose"] . '</td>';
        echo '        </tr>';
    }

    echo '      </tbody>';
    echo '    </table>';
    echo '  </div>';
}
?>



<h2 id="3d-print-jobs">3D Print Jobs</h2>
  <p><a href="#laser-cut-jobs" >(Jump to Laser Cut jobs)</a></p>
  <p><a href="#large-format-print-jobs" >(Jump to Large Format Print jobs)</a></p>
  <button class="accordion active">Submitted</button>
    <div class="panel" style="display:block;">
      <?php generateTable('d_not_priced',$d_not_priced, $d_href, 'submission_date', 'Submission Date'); ?>
    </div>
    
    <button class="accordion">On Hold</button>
    <div class="panel">
      <?php generateTable('d_on_hold', $d_on_hold, $d_href, 'hold_date', 'Hold Date'); ?>
    </div>
  
  
  <button class="accordion">Pending Payment</button>
    <div class="panel">
      <?php generateTable('d_pending_payment',$d_pending_payment, $d_href, 'priced_date', 'Priced Date'); ?>
    </div>
  
  <button class="accordion active">Paid</button>
    <div class="panel" style="display:block;">
      <?php generateTable('d_paid', $d_paid, $d_href, 'paid_date', 'Paid Date'); ?>
    </div>
  
  <button class="accordion active">Printing</button>
    <div class="panel" style="display:block;">
      <?php generateTable('d_printing',$d_printing, $d_href, 'printing_date', 'Print Start Date'); ?>
    </div>

    
  <button class="accordion active">Completed Print</button>
    <div class="panel" style="display:block;">
      <?php generateTable('d_completed', $d_completed, $d_href, 'completed_date', 'Completed Date'); ?>
    </div>
  
  <button class="accordion">Ready for pickup</button>
    <div class="panel">
        <?php generateTable('d_delivered',$d_delivered, $d_href, 'delivered_date', 'Delivered Date'); ?>
    </div>

    

<h2><br><br><h2 id="laser-cut-jobs">Laser Cut Jobs</h2>
  <p><a href="#3d-print-jobs" >(Jump to 3D Print jobs)</a></p>
  <p><a href="#large-format-print-jobs" >(Jump to Large Format Print jobs)</a></p>
  <button class="accordion active">Submitted</button>
    <div class="panel" style="display:block;">
      <?php generateTable('l_not_priced', $l_not_priced, $l_href, 'submission_date', 'Submission Date'); ?>
    </div>

  <button class="accordion">On Hold</button>
      <div class="panel">
        <?php generateTable('l_on_hold',$l_on_hold, $l_href, 'hold_date', 'Hold Date'); ?>
      </div>
  

  <button class="accordion">Pending Payment</button>
    <div class="panel">
      <?php generateTable('l_pending_payment',$l_pending_payment, $l_href, 'priced_date', 'Priced Date'); ?>
    </div>

  <button class="accordion active">Paid</button> 
    <div class="panel" style="display:block;">
      <?php generateTable('l_paid', $l_paid, $l_href, 'paid_date', 'Paid Date'); ?>
    </div>

  <button class="accordion active">Cutting</button> 
    <div class="panel" style="display:block;">
      <?php generateTable('l_printing',$l_printing, $l_href, 'printing_date', 'Cut Start Date'); ?>
    </div>

  
  <button class="accordion active">Completed</button> 
    <div class="panel" style="display:block;">
      <?php generateTable('l_completed',$l_completed, $l_href, 'completed_date', 'Completed Date'); ?>
    </div>
  
  <button class="accordion">Ready for pickup</button>
    <div class="panel">
      <?php generateTable('l_delivered',$l_delivered, $l_href, 'delivered_date', 'Delivered Date'); ?>
    </div>

<!--Large Format Print jobs-->
  <h2 id="large-format-print-jobs">Large Format Print Jobs</h2>
  <p><a href="#3d-print-jobs" >(Jump to 3D Print jobs)</a></p>
  <p><a href="#laser-cut-jobs" >(Jump to Laser Cut jobs)</a></p>
  <button class="accordion active">Submitted</button>
    <div class="panel" style="display:block;">
      <?php generateTable('lf_not_priced',$lf_not_priced, $lf_href, 'submission_date', 'Submission Date'); ?>
    </div>
    
    <button class="accordion">On Hold</button>
    <div class="panel">
      <?php generateTable('lf_on_hold',$lf_on_hold, $lf_href, 'hold_date', 'Hold Date'); ?>
    </div>
  
  
  <button class="accordion">Pending Payment</button>
    <div class="panel">
      <?php generateTable('lf_pending_payment',$lf_pending_payment, $lf_href, 'priced_date', 'Priced Date'); ?>
    </div>
  
  <button class="accordion active">Paid</button>
    <div class="panel" style="display:block;">
      <?php generateTable('lf_paid',$lf_paid, $lf_href, 'paid_date', 'Paid Date'); ?>
    </div>
  
  <button class="accordion active">Printing</button>
    <div class="panel" style="display:block;">
      <?php generateTable('lf_printing',$lf_printing, $lf_href, 'printing_date', 'Print Start Date'); ?>
    </div>

    
  <button class="accordion active">Completed Print</button>
    <div class="panel" style="display:block;">
      <?php generateTable('lf_completed',$lf_completed, $lf_href, 'completed_date', 'Completed Date'); ?>
    </div>
  
  <button class="accordion">Ready for pickup</button>
    <div class="panel">
      <?php generateTable('lf_delivered', $lf_delivered, $lf_href, 'delivered_date', 'Delivered Date'); ?>
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

<script>
function sortTable(tableId) {
  var table, rows, switching, i, shouldSwitch;
  table = document.getElementById(tableId);
  switching = true;

  // Convert rows to an array
  rows = Array.from(table.rows).slice(1); // skip the header row

  // sort by job purpose then by date
  rows.sort(function(rowA, rowB) {
    var purposeOrder = { 'academic': 1, 'personal': 2, 'null': 3 };

    var col4A = rowA.cells[3].textContent.toLowerCase(); // Column purpose
    var col4B = rowB.cells[3].textContent.toLowerCase();
    var purposeA = purposeOrder[col4A] || purposeOrder['null']; 
    var purposeB = purposeOrder[col4B] || purposeOrder['null']; 

    if (purposeA < purposeB) return -1;
    if (purposeA > purposeB) return 1;

    var col3A = rowA.cells[2].textContent; // Column date
    var col3B = rowB.cells[2].textContent;
    var dateA = new Date(col3A);
    var dateB = new Date(col3B);

    return dateB - dateA;
    
  });

  // Re-adding the sorted rows to the table
  for (i = 0; i < rows.length; i++) {
    table.appendChild(rows[i]);
  }
}

function sortAllTablesPurpose() {
  var tables_to_sort=['d_not_priced','d_not_priced','d_not_priced','d_on_hold','d_pending_payment','d_paid','d_printing','d_completed','d_delivered','l_not_priced','l_on_hold','l_pending_payment','l_paid','l_printing','l_completed','l_delivered','lf_not_priced','lf_on_hold','lf_pending_payment','lf_paid','lf_printing','lf_completed','lf_delivered'];


  tables_to_sort.forEach(sortTable);
  
}

window.onload = sortAllTablesPurpose();
</script>
  
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
      <script>window.jQuery || document.write('<script src="/docs/4.5/assets/js/vendor/jquery.slim.min.js"><\/script>')</script><script src="/docs/4.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-1CmrxMRARb6aLqgBO7yyAxTOQE2AKb9GfXnEo760AUcUmFx3ibVJJAzGytlQcNXd" crossorigin="anonymous"></script>
        <script src="form-validation.js"></script></body>
</html>
