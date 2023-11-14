<?php
session_start();
require ('auth-sec.php'); //Gets CAS & db
//auth-sec includes: $user, $user_email, $user_type, $user_name
//Is user Admin check
if ($user_type == 1) {
  header("Location: customer-dashboard.php");
  die();
}


/*
User querey fields
*/
$sql_line =array(); //sql builder
$getcheck = array_fill(0,3, FALSE);
if (isset($_GET['searchdate_start']) && ($_GET['searchdate_start'] != "" && $_GET['searchdate_start'] != NULL)) {
  $getcheck[0] = True;
  $sql_line[] = "completed_date >= :searchdate_start";
}if (isset($_GET['searchdate_end']) && ($_GET['searchdate_end'] != "" && $_GET['searchdate_end'] != NULL)) {
  $getcheck[1] = True;
  $sql_line[] = "completed_date <= :searchdate_end";
}if (isset($_GET['search_id']) && ($_GET['search_id'] != "" && $_GET['search_id'] != NULL)) {
  $getcheck[2] = True;
  $sql_line[] = "netlink_id LIKE :search_id";
}

/*3D PRINT JOBS
*/
  //build sql query line based on search parameters
  $searchline = "SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.submission_date AS submission_date, web_job.delivered_date AS delivered_date, web_job.cancelled_date AS cancelled_date, web_job.job_purpose AS job_purpose, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id=users.netlink_id INNER JOIN laser_cut_job ON web_job.id=3d_print_job.3d_print_id WHERE status IN ('completed', 'delivered', 'archived', 'cancelled')";

  if(!empty($sql_line)){
    $searchline .= " AND " . implode(" AND ", $sql_line);
  }

  $searchline .= "ORDER BY completed_date DESC";

  $stm = $conn ->prepare($searchline);

//Bind search parameters
if ($getcheck[0] == TRUE) {
  $stm->bindParam(':searchdate_start', $_GET['searchdate_start'], PDO::PARAM_STR);
}if ($getcheck[1] == TRUE) {
  $stm->bindParam(':searchdate_end', $_GET['searchdate_end'], PDO::PARAM_STR);
}if ($getcheck[2] == TRUE) {
  $temp = $_GET['search_id']."%";
  $stm->bindParam(':search_id', $temp, PDO::PARAM_STR);
}

$stm->execute(); //**ERROR HERE!!

//SQL results
$d_history = $stm->fetchAll();

/*laser cut JOBS
*/
  $searchline = "SELECT web_job.id AS id, web_job.job_name AS job_name, web_job.status AS status, web_job.submission_date AS submission_date, web_job.delivered_date AS delivered_date, web_job.cancelled_date AS cancelled_date, web_job.job_purpose AS job_purpose, users.name AS name FROM web_job INNER JOIN users ON web_job.netlink_id=users.netlink_id INNER JOIN laser_cut_job ON web_job.id=laser_cut_job.laser_cut_id WHERE status IN ('completed', 'delivered', 'archived', 'cancelled')";

  if(!empty($sql_line)){
    $searchline .= " AND " . implode(" AND ", $sql_line);
  }

  $searchline .= "ORDER BY completed_date DESC";

  $stm = $conn ->prepare($searchline);

//Bind search parameters
if ($getcheck[0] == TRUE) {
  $stm->bindParam(':searchdate_start', $_GET['searchdate_start'], PDO::PARAM_STR);
}if ($getcheck[1] == TRUE) {
  $stm->bindParam(':searchdate_end', $_GET['searchdate_end'], PDO::PARAM_STR);
}if ($getcheck[2] == TRUE) {
  $temp = $_GET['search_id']."%";
  $stm->bindParam(':search_id', $temp, PDO::PARAM_STR);
}

$stm->execute();

//SQL results
$l_history = $stm->fetchAll();

/*more to do with search query*/
$get_line = array();
//Seach button clicked
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  if (isset($_POST["searchdate_start"])) {
    $get_line[] = "searchdate_start=" . $_POST["searchdate_start"];
  }
  if (isset($_POST["searchdate_end"])) {
    $get_line[] = "searchdate_end=" . $_POST["searchdate_end"];
  }
  if (isset($_POST["search_id"])) {
    $get_line[] = "search_id=" . $_POST["search_id"];
  }
  header("Location: admin-print-history.php?". implode("&", $get_line));
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
    <title>Admin print history</title>

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

  </head>
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
  <body class="bg-light">

    <div class="text-center">
      <h1>Print History</h1>
    </div>

  <div class="row">

  <div class="container">
  <div class="py-5 text-left">

  <h3>Archived &amp; Cancelled Jobs</h3>
  <br>

  <!--Search bar-->
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


  <button class="accordion active">3D Print Jobs</button>
    <div class="panel" style="display:block;">
  <div class="py-3"></div>
  <div class="table-responsive">
  <table class="table table-striped table-md">
  <thead>
  <tr>
    <!-- table header-->
    <th>Job id</th>
    <th>Username</th>
    <th>Name</th>
    <th>Completion Date</th>
    <th>Status</th>
  </tr>
  </thead>
  <tbody>

  <?php foreach ($d_history as $row) {
  ?>
  <tr>
    <td><?php echo $row["id"]; ?></td>
    <td><?php echo $row["netlink_id"]; ?></td>
      <!--CHANGE TO UNEDITABLE SCREEN -->
      <!-- Conditional link based on job_type -->
      <td style="width:95px;"><a href="admin-3d-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>   
      <td><?php echo $row["completed_date"]; ?></td>
    <td><?php echo $row["status"]; ?></td>
  </tr>
  <?php
  }
  ?>

  </tbody>
  </table>
  </div>
</div>

  <button class="accordion active">Laser Cut Jobs</button>
  <div class="panel" style="display:block;">
  <div class="py-3"></div>
  <div class="table-responsive">
  <table class="table table-striped table-md">
  <thead>
  <tr>
    <!-- table header-->
    <th>Job id</th>
    <th>Username</th>
    <th>Name</th>
    <th>Completion Date</th>
    <th>Status</th>
  </tr>
  </thead>
  <tbody>

  <?php foreach ($l_history as $row) {
  ?>
  <tr>
    <td><?php echo $row["id"]; ?></td>
    <td><?php echo $row["netlink_id"]; ?></td>
      <!--CHANGE TO UNEDITABLE SCREEN -->
      <!-- Conditional link based on job_type -->
      <td style="width:95px;"><a href="admin-laser-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>   
      <td><?php echo $row["completed_date"]; ?></td>
    <td><?php echo $row["status"]; ?></td>
  </tr>
  <?php
  }
  ?>

  </tbody>
  </table>
  </div>
</div>

  <hr class="mb-12">

  <!--<a class="btn btn-md btn-block" href="login.php" role="button">Log Out</a>
  -->
  </div>
  </div>
  </div>
<div class="text-center">
  <a class="btn btn-md btn-primary btn-lg" href="admin-dashboard.php" role="button">Back to Dashboard</a>
</div>


</body>
</html>
