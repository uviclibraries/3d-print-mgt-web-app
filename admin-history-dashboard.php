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

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.delivered_date AS delivered_date, web_job.job_purpose AS job_purpose, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id=users.netlink_id INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status = 'delivered' ORDER BY web_job.delivered_date ASC;");
$print_job1 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.cancelled_date AS cancelled_date, web_job.job_purpose AS job_purpose, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status = 'cancelled' ORDER BY web_job.cancelled_date ASC");
$print_job2 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.paid_date AS paid_date, web_job.job_purpose AS job_purpose, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status = 'archived' ORDER BY web_job.paid_date ASC");
$print_job3 = $stm->fetchAll();



//pull only laser cutting strings from web_job

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.delivered_date AS delivered_date, web_job.job_purpose AS job_purpose, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id=users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status = 'delivered' ORDER BY web_job.delivered_date ASC;");
$laser_job1 = $stm->fetchAll();


$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.cancelled_date AS cancelled_date, web_job.job_purpose AS job_purpose, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status = 'cancelled' ORDER BY web_job.cancelled_date ASC");
$laser_job2 = $stm->fetchAll();

$stm = $conn->query("SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.delivered_date AS delivered_date, web_job.job_purpose AS job_purpose, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id = users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE web_job.status = 'archived' ORDER BY web_job.delivered_date ASC");
$laser_job3 = $stm->fetchAll();




//3d_printing jobs

$d_delivered = [];
$d_cancelled = [];
$d_archived = [];


foreach ($print_job1 as $job) {
  $d_delivered[] = $job;
}
foreach ($print_job2 as $job) {
  $d_cancelled[] = $job;
}
foreach ($print_job3 as $job) {
  $d_archived[] = $job;
}

$l_delivered = [];
$l_cancelled = [];
$l_archived = [];


foreach ($laser_job1 as $job) {
  $l_delivered[] = $job;
}
foreach ($laser_job2 as $job) {
  $l_cancelled[] = $job;
}
foreach ($laser_job3 as $job) {
  $l_archived[] = $job;
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
    <title>Admin Job History Dashboard</title>
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
        <div class="container">
          <div class="py-5 text-left">

            <div class="row">
              <div class="col-md-4">
                <form method="POST">
                  <div>
                    <label for = "searchdate_start">Start date:</label>
                    <input type="date" id= "searchdate_start" name="searchdate_start">
                  </div>
                  <div class="">
                    <label for = "searchdate_end">End date  :</label>
                    <input type="date" id= "searchdate_end" name="searchdate_end">
                  </div>
                  <div class="">
                    <label for = "search_id">netlink id:</label>
                    <input type="text" id= "search_id" name="search_id">
                  </div>
                  <!-- extra search criteria
                  <div class="">
                    <label for = "approved">Only Approved: </label>
                    <input type="checkbox" id= "approved" name="approved">
                  </div>
                -->
                  <input type="submit" name="Search" value="Search">
                </form>
              </div>
              <div class="col-md-4 offset-md-4">
                <a class="btn btn-md btn-primary btn-" href="admin-dashboard.php" role="button">Back to Dashboard</a>
              </div>
            </div>



<h2 id="3d-print-jobs">3D Print Jobs</h2>
  <p><a href="#laser-cut-jobs" >(Jump to Laser Cut jobs)</a></p>
  <button class="accordion active">Delivered</button>
    <div class="panel" style="display:block;">
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
            <tr>
              <!-- table header-->
              <th style="width:95px;">Name</th>
              <th style="width:95px;">Job</th>
              <th style="width:95px;">Delivered Date</th>
              <th style="width:95px;">Status</th>
              <th style="width:20px;">Purpose</th>
            </tr>
          </thead>
          <tbody>
          <?php
          //Grab each item from each array
          foreach($d_delivered as $row){
          ?>
          <tr>
            <td style="width:95px;"><?php echo $row["name"]; ?></td>
            <td style="width:95px;"><a href="admin-3d-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
            <td style="width:95px;"><?php echo $row["delivered_date"]; ?></td>
            <td style="width:95px;"><?php echo $row["status"]; ?></td>
            <td style="width:20px;"><?php echo $row["job_purpose"]; ?></td>
          </tr>
          <?php
          } ?>
          </tbody>
        </table>
      </div>
    </div>
    
    <button class="accordion active">Cancelled</button>
    <div class="panel" style="display:block;">
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
            <tr>
              <!-- table header-->
              <th style="width:95px;">Name</th>
              <th style="width:95px;">Job</th>
              <th style="width:95px;">Cancelled Date</th>
              <th style="width:95px;">Status</th>
              <th style="width:20px;">Purpose</th>
            </tr>
          </thead>
          <tbody>
          <?php
          //Grab each item from each array
          foreach($d_cancelled as $row){
          ?>
          <tr>
            <td style="width:95px;"><?php echo $row["name"]; ?></td>
            <td style="width:95px;"><a href="admin-3d-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
            <td style="width:95px;"><?php echo $row["cancelled_date"]; ?></td>
            <td style="width:95px;"><?php echo $row["status"]; ?></td>
            <td style="width:20px;"><?php echo $row["job_purpose"]; ?></td>
          </tr>
          <?php
          } ?>
          </tbody>
        </table>
      </div>
    </div>
  
  
  <button class="accordion">Archived</button>
    <div class="panel">
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
            <tr>
              <!-- table header-->
              <th style="width:95px;">Name</th>
              <th style="width:95px;">Job</th>
              <th style="width:95px;">Archived Date</th>
              <th style="width:95px;">Status</th>
              <th style="width:20px;">Purpose</th>
              <!--  -->
            </tr>
          </thead>
          <tbody>
            <?php foreach ($d_archived as $row) {
            ?>
            <tr>
              <td style="width:95px;"><?php echo $row["name"]; ?></td>
              <td style="width:95px;"><a href="admin-3d-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td style="width:95px;"><?php echo $row["delivered_date"]; ?></td>
              <td style="width:95px;"><?php echo $row["status"]; ?></td>
              <td style="width:20px;"><?php echo $row["job_purpose"]; ?></td>

            </tr>
            <?php
            }?>
          </tbody>
        </table>
      </div>
    </div>
    

<h2><br><br><h2 id="laser-cut-jobs">Laser Cut Jobs</h2>
  <p><a href="#3d-print-jobs" >(Jump to 3D Print jobs)</a></p>

  <button class="accordion active">Delivered</button>
    <div class="panel" style="display:block;">
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
            <tr>
              <!-- table header-->
              <th style="width:95px;">Name</th>
              <th style="width:95px;">Job</th>
              <th style="width:95px;">Delivered Date</th>
              <th style="width:95px;">Status</th>
              <th style="width:20px;">Purpose</th>
              
            </tr>
          </thead>
          <tbody>

          <?php
          //Grab each item from each array
          foreach($l_delivered as $row){
          ?>
          <tr>
            <td style="width:95px;"><?php echo $row["name"]; ?></td>
            <td style="width:95px;"><a href="admin-laser-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
            <td style="width:95px;"><?php echo $row["delivered_date"]; ?></td>
            <td style="width:95px;"><?php echo $row["status"]; ?></td>
            <td style="width:20px;"><?php echo $row["job_purpose"]; ?></td>
          </tr>
          <?php
          } ?>
          </tbody>
        </table>
      </div>
    </div>

  <button class="accordion active">Cancelled</button>
      <div class="panel" style="display:block;">
        <div class="table-responsive">
          <table class="table table-striped table-md">
            <thead>
              <tr>
                <!-- table header-->
                <th style="width:95px;">Name</th>
                <th style="width:95px;">Job</th>
                <th style="width:95px;">Cancelled Date</th>
                <th style="width:95px;">Status</th>
                <th style="width:20px;">Purpose</th>
                
              </tr>
            </thead>
            <tbody>

            <?php
            //Grab each item from each array
            foreach($l_cancelled as $row){
            ?>
            <tr>
              <td style="width:95px;"><?php echo $row["name"]; ?></td>
              <td style="width:95px;"><a href="admin-laser-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td style="width:95px;"><?php echo $row["cancelled_date"]; ?></td>
              <td style="width:95px;"><?php echo $row["status"]; ?></td>
              <td style="width:20px;"><?php echo $row["job_purpose"]; ?></td>
            </tr>
            <?php
            } ?>
            </tbody>
          </table>
        </div>
      </div>
  

  <button class="accordion">Archived</button>
    <div class="panel">
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
            <tr>
              <!-- table header-->
              <th style="width:95px;">Name</th>
              <th style="width:95px;">Job</th>
              <th style="width:95px;">Archived Date</th>
              <th style="width:95px;">Status</th>
              <th style="width:95px;">Purpose</th>
              
            </tr>
          </thead>
          <tbody>

          <?php foreach ($l_archived as $row) {
          ?>
          <tr>
            <td style="width:95px;"><?php echo $row["name"]; ?></td>
            <td style="width:95px;"><a href="admin-laser-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
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
