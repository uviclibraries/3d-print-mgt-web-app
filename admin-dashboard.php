<?php
require ('db.php');
$stm = $conn->query("SELECT id, job_name, netlink_id, status, submission_date, priced_date, pending_pmt_date, ready_to_prnt_date, printing_date, complete_date FROM print_job ORDER BY id DESC");
$jobs = $stm->fetchAll();
#
$not_priced = [];
$pending_payment = [];
$ready_to_print = [];
$printing = [];
$complete = [];

foreach ($jobs as $job) {
  if ($job["status"] == "not_priced"){
    $not_priced[] = $job;
  } else if ($job["status"] == "pending_payment"){
    $pending_payment[] = $job;
  } else if ($job["status"] == "ready_to_print"){
    $ready_to_print[] = $job;
  } else if ($job["status"] == "printing"){
    $printing[] = $job;
  } else if ($job["status"] == "complete"){
    $complete[] = $job;
  }
  # code...
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
    <title>Checkout example · Bootstrap</title>

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
    <div class="container">
        <div class="py-5 text-center">
            <img class="d-block mx-auto mb-4" src="/docs/4.5/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="admin-reports.php">
                        <button class="btn btn-primary btn-lg btn-block" class="form-control" type="submit" data-inline="true">Reports</button>
                    </a>
                </div>

                <div class="col-md-4 mb-3">
                    <a href="admin-print-history.php">
                        <button class="btn btn-primary btn-lg btn-block" class="form-control" type="submit" data-inline="true">Print History</button>
                    </a>
                </div>

                <div class="col-md-4 mb-3">
                    <a href="admin-manage-printers.php">
                        <button class="btn btn-primary btn-lg btn-block" class="form-control" type="submit" data-inline="true">Manage Printers</button>
                    </a>
                </div>

            <div class="container">
          <div class="py-5 text-left">
            <h2>Not Priced</h2>
            <div class="py-3"></div>
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
            <tr>
              <th>Username</th>
              <th>Name</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
          <?php
            foreach($not_priced as $row){
            ?>
            <tr>
              <td><?php echo $row["netlink_id"]; ?></td>
                <td><a href="admin-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td><?php echo $row["submission_date"]; ?></td>
            </tr>
            <?php
            }
            ?>
          </tbody>
        </table>

            <h2>Pending Payment</h2>
            <div class="py-3"></div>
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <tbody>

          <?php
            foreach($pending_payment as $row){
            ?>
            <tr>
              <td><?php echo $row["netlink_id"]; ?></td>
                <td><a href="admin-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td><?php echo $row["priced_date"]; ?></td>
            </tr>
            <?php
            }
            ?>
          </tbody>
        </table>

            <h2>Ready to Print</h2>
            <div class="py-3"></div>
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <tbody>
          <?php
            foreach($ready_to_print as $row){
            ?>
            <tr>
              <td><?php echo $row["netlink_id"]; ?></td>
                <td><a href="admin-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td><?php echo $row["ready_to_prnt_date"]; ?></td>
            </tr>
            <?php
            }
            ?>
          </tbody>
        </table>

            <h2>Printing</h2>
            <div class="py-3"></div>
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <tbody>
          <?php
            foreach($printing as $row){
            ?>
            <tr>
              <td><?php echo $row["netlink_id"]; ?></td>
                <td><a href="admin-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td><?php echo $row["printing_date"]; ?></td>
            </tr>
            <?php
            }
            ?>
          </tbody>
        </table>

            <h2>Complete</h2>
            <div class="py-3"></div>
      <div class="table-responsive">
        <table class="table table-striped table-md">
         <tbody>
          <?php
            foreach($complete as $row){
            ?>
            <tr>
              <td><?php echo $row["netlink_id"]; ?></td>
                <td><a href="admin-job-specification.php?job_id=<?php echo $row["id"]; ?>"><?php echo $row["job_name"]; ?></a></td>
              <td><?php echo $row["complete_date"]; ?></td>
            </tr>
            <?php
            }        
            ?>
          </tbody>
        </table>
      </div>

        <hr class="mb-12">
        <a class="btn btn-md btn-block" href="login.php" role="button">Log Out</a>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
      <script>window.jQuery || document.write('<script src="/docs/4.5/assets/js/vendor/jquery.slim.min.js"><\/script>')</script><script src="/docs/4.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-1CmrxMRARb6aLqgBO7yyAxTOQE2AKb9GfXnEo760AUcUmFx3ibVJJAzGytlQcNXd" crossorigin="anonymous"></script>
        <script src="form-validation.js"></script></body>
</html>