<?php
session_start();
require ('auth-sec.php'); //Gets CAS & db
//auth-sec includes: $user, $user_email, $user_type, $user_name
//Is user Admin check
if ($user_type == 1) {
  header("Location: customer-dashboard.php");
  die();
}

$stm = $conn->prepare("SELECT * FROM users WHERE id=?");
$stm->execute([$_GET["user_id"]]);
$job=$stm->fetch();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = $conn->prepare("UPDATE users SET user_type = :user_type, email = :email, cron_report = :cron_report WHERE id = :user_id;
  ");
  $stmt->bindParam(':user_id', $_GET["user_id"]);
  $b = 0;
  $stmt->bindParam(':cron_report', $b);
  if ($_POST["user_type"] == "Admin") {
    $a = 0;
    $stmt->bindParam(':user_type', $a);
    if (isset($_POST["cron_report"]) && $_POST["cron_report"] == "Reports") {
      //isset means the option was available, must be Admin from parent if, and reports was selected.
        $b = 1;
    }
  }
  else{
    $a = 1;
      $stmt->bindParam(':user_type', $a);
  }
  $stmt->bindParam(':email', $_POST["email"]);
  $stmt->execute();
  //refresh page to display new values.
  echo "<meta http-equiv='refresh' content='0'>";
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
    <title>User id: <?php echo $job["id"] ?></title>

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
    <form method="POST">
  <div class="py-5 text-center">
    <h1><?php echo $job["name"]; ?></h1>
    </div>

    <div class="col-md-12 order-md-1">


    <hr class="mb-6">

    <div class="col-md-12 order-md-1">
      <h4 class="mb-3">Specifications</h4>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="username">ID</label>
                <div class="input-group">
                  <div class="input-group mb-3">
                    <input type="text" name="infill" class="form-control" value="<?php echo $job["id"]; ?>" readonly>
                </div>
            </div>
            </div>
            <div class="col-md-3 mb-3">
                <label for="username">Netlink</label>
                <div class="input-group">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="scale" value="<?php echo $job["netlink_id"]; ?>"  readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
          <div class="col-md-3 mb-3">
            <label for="layer-height">User Type</label>
            <select class="custom-select d-block w-100" name="user_type" id="user_type">
              <option <?php if ($job["user_type"]== 0){echo "selected";} ?>>Admin</option>
              <option <?php if ($job["user_type"]== 1){echo "selected";} ?>>Regular</option>
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <label for="email">Email</label>
            <input type="email" class="form-control" id = "email" name="email" value="<?php echo $job["email"]; ?>" autocomplete="off">
          </div>
          <?php if ($job["user_type"] == 0) {
            //if user is admin, show cron option
            ?>
          <div class="col-md-3 mb-3">
            <label for="cron">Email Reports</label>
            <select class="custom-select d-block w-100" name="cron_report" id="cron_report">
              <option <?php if ($job["cron_report"]== 0){echo "selected";} ?>>None</option>
              <option <?php if ($job["cron_report"]== 1){echo "selected";} ?>>Reports</option>
            </select>
          </div>
          <?php } ?>

        </div>
    </div>

        <hr class="mb-4">
        <div class="row">
            <div class="col-md-6 mb-3">
                <a href="url">
                    <button class="btn btn-primary btn-lg btn-block" class="form-control" type="submit" data-inline="true">Save</button>
                </a>
            </div>
            <div class="col-md-6 mb-3">
                <a class="btn btn-primary btn-lg btn-block" href="admin-manage-users.php?user_id=<?php echo $job["name"]; ?>" role="button">Back to Manage Users</a>
        </div>
    </div>
    </form>
  </div>

  <p></p>
  <br>
  <p></p>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
      <script>window.jQuery || document.write('<script src="/docs/4.5/assets/js/vendor/jquery.slim.min.js"><\/script>')</script><script src="/docs/4.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-1CmrxMRARb6aLqgBO7yyAxTOQE2AKb9GfXnEo760AUcUmFx3ibVJJAzGytlQcNXd" crossorigin="anonymous"></script>
        <script src="form-validation.js"></script>
        </body>
</html>
