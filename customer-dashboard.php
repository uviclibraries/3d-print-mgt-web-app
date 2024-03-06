<?php
session_start();
require ('auth-sec.php'); //Gets CAS & db
//auth-sec includes: $user, $user_email, $user_type, $user_name

$stm = $conn->query("SELECT id, job_name, status, submission_date, priced_date, paid_date, printing_date, completed_date, cancelled_date, hold_date, delivered_date FROM web_job INNER JOIN 3d_print_job ON id=3d_print_id WHERE netlink_id = '$user' ORDER BY id DESC");
$data = $stm->fetchAll();
//split results by Status
$d_not_priced=[];
$d_pending_payment = [];
$d_on_hold=[];
$d_paid=[];
$d_in_progress=[];
// $d_completed =[];
$d_delivered=[];
$d_cancelled = [];
$d_archived=[];

foreach ($data as $job) {
  if($job['status']=="submitted"){
    $d_not_priced[] = $job;
  }
  elseif ($job['status'] == "pending payment") {
    $d_pending_payment[] = $job;
  }
  
  elseif ($job['status'] == "on hold") {
    $d_on_hold[] = $job;
  }

  elseif ($job['status'] == "paid") {
    $d_paid[] = $job;
  }
  elseif ($job['status'] == "printing" || $job['status'] == "completed") {
    $d_in_progress[] = $job;
  }
  // elseif ($job['status'] == "completed" ) {
  //   $d_completed[] = $job;
  // }
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
$l_not_priced=[];
$l_pending_payment = [];
$l_on_hold=[];
$l_paid = [];
$l_in_progress=[];
// $l_completed =[];
$l_delivered=[];
$l_cancelled = [];
$l_archived=[];

foreach ($data as $job) {
  if($job['status']=="submitted"){
    $l_not_priced[] = $job;
  }
  elseif ($job['status'] == "pending payment") {
    $l_pending_payment[] = $job;
  }
  elseif ($job['status'] == "on hold") {
    $l_on_hold[] = $job;
  }
  elseif ($job['status'] == "paid") {
    $l_paid[] = $job;
  }
  elseif ($job['status'] == "printing" || $job['status'] == "completed") {
    $l_in_progress[] = $job;
  }
  // elseif ($job['status'] == "completed" ) {
  //   $l_completed[] = $job;
  // }
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


$stm = $conn->query("SELECT id, job_name, status, submission_date, priced_date, paid_date, printing_date, completed_date, cancelled_date, hold_date, delivered_date FROM web_job INNER JOIN large_format_print_job ON id=large_format_print_id WHERE netlink_id = '$user' ORDER BY id DESC");
$data = $stm->fetchAll();
//split results by Status
$lf_not_priced=[];
$lf_pending_payment = [];
$lf_on_hold=[];
$lf_paid = [];
$lf_in_progress=[];
// $lf_completed =[];
$lf_delivered=[];
$lf_cancelled = [];
$lf_archived=[];

foreach ($data as $job) {
  if($job['status']=="submitted"){
    $lf_not_priced[] = $job;
  }
  elseif ($job['status'] == "pending payment") {
    $lf_pending_payment[] = $job;
  }
  elseif ($job['status'] == "on hold") {
    $lf_on_hold[] = $job;
  }
  elseif ($job['status'] == "paid") {
    $lf_paid[] = $job;
  }
 elseif ($job['status'] == "printing" || $job['status'] == "completed" ) {
    $lf_in_progress[] = $job;
  }
  // elseif ($job['status'] == "completed" ) {
  //   $lf_completed[] = $job;
  // }
  elseif ($job['status'] == "delivered"){
    $lf_delivered[] = $job;
  } 
  elseif($job['status']=="cancelled"){
    $lf_cancelled[] = $job;
  }
  elseif($job['status']=="archived"){
    $lf_archived[] = $job;
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

  <!-- <div class="row" style="border:solid;"> -->
   
    <div class="col-md-12 order-md-1">
      <div class="py-5 text-center">
        <div class="row">
          <div class="btn-auto mr-auto">
          <a href="customer-new-job.php">
            <button class="btn btn-primary btn-lg" type="submit">Create New Project</button>
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
    </div>
  <!-- </div> -->

  <!-- <div class="row" style="border:solid;"> -->
    <div class="col-md-12 order-md-1">

      <div class="py-5 text-center">
        <div class="row">
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
            <a href="https://onlineacademiccommunity.uvic.ca/dsc/tools-tech/large-format-printer-and-scanner/">
              <button class="btn btn-primary btn-lg" type="submit" style="background-color:#5e8669;">Large Format Print FAQ</button>
            </a>
          </div>
        </div>
    </div>
  </div>

<?php
$d_href='customer-3d-job-information.php?job_id=';
$l_href= 'customer-laser-job-information.php?job_id=';
$lf_href='customer-large-format-print-job-information.php?job_id=';

function generateTable($table_id, $rel_jobs, $job_ref, $status_date, $date_header) {
    echo '  <div class="table-responsive" style="width: 100%; table-layout: fixed;">';
    echo '    <table id="' . $table_id . '" class="table table-striped table-md">';
    echo '      <thead>';
    echo '        <tr>';
    echo '          <th style="width:60%;">Job Name</th>';
    echo '          <th style="width:20%;">Status</th>';
    echo '          <th style="width:20%;">' . $date_header . '</th>';
    echo '        </tr>';
    echo '      </thead>';
    echo '      <tbody>';
    
    foreach ($rel_jobs as $row) {
      $status = $row["status"] == 'printing' || $row["status"] == 'completed' ? 'in progress' : ($row["status"] == 'delivered' ? 'ready' : $row["status"]);
        echo '        <tr>';
        echo '          <td style="width:60%;"><a href="' .$job_ref. $row["id"] . '">' . $row['job_name'] . '</a></td>';
        echo '          <td style="width:20%;">' . $status . '</td>';
        echo '          <td style="width:20%;">' . $row[$status_date] . '</td>';
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
  
  <button class="accordion active">In Progress</button>
    <div class="panel" style="display:block;">
      <?php generateTable('d_inprogress_completed',$d_in_progress, $d_href, 'printing_date', 'Print Start Date'); 
      // generateRows($d_completed, $d_href, 'completed_date');
      // generateTableEnd();
      ?>
    </div>
  
  <button class="accordion">Ready for pickup</button>
    <div class="panel">
        <?php generateTable('d_delivered',$d_delivered, $d_href, 'delivered_date', 'Delivered Date'); ?>
    </div>

    

<h2 id="laser-cut-jobs">Laser Cut Jobs</h2>
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
      <?php generateTable('l_in_progress',$l_in_progress, $l_href, 'printing_date', 'Cut Start Date'); ?>
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
      <?php generateTable('lf_in_progress',$lf_in_progress, $lf_href, 'printing_date', 'Print Start Date'); ?>
    </div>
  
  <button class="accordion">Ready for pickup</button>
    <div class="panel">
      <?php generateTable('lf_delivered', $lf_delivered, $lf_href, 'delivered_date', 'Delivered Date'); ?>
    </div>



        <a class="btn btn-md btn-block" href="?logout=" role="button">Log Out</a>

    </div>
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
  var tables_to_sort=['d_not_priced','d_not_priced','d_not_priced','d_on_hold','d_pending_payment','d_paid','d_in_progress','d_delivered','l_not_priced','l_on_hold','l_pending_payment','l_paid','l_in_progress','l_delivered','lf_not_priced','lf_on_hold','lf_pending_payment','lf_paid','lf_in_progress','lf_delivered'];


  tables_to_sort.forEach(sortTable);
  
}

window.onload = sortAllTablesPurpose();
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
      <script>window.jQuery || document.write('<script src="/docs/4.5/assets/js/vendor/jquery.slim.min.js"><\/script>')</script><script src="/docs/4.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-1CmrxMRARb6aLqgBO7yyAxTOQE2AKb9GfXnEo760AUcUmFx3ibVJJAzGytlQcNXd" crossorigin="anonymous"></script>
        <script src="form-validation.js"></script></body>
</html>
