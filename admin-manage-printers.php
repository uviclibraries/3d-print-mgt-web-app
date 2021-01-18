<?php
session_start();
require ('auth-sec.php'); //Gets CAS & db
//auth-sec includes: $user, $user_email, $user_type, $user_name
//Is user Admin check
if ($user_type == 1) {
  header("Location: customer-dashboard.php");
  die();
}

$stm = $conn->query("SELECT id, printer_name, make_model, comments, operational, color ,color2, 2extruder FROM printer ORDER BY id");
$all_printers = $stm->fetchAll();

//Update printer sql
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = $conn->prepare("UPDATE printer SET operational = :operational, color = :color, color2 = :color2 WHERE id = :printer_id");
  //Goes through whole table
  foreach ($all_printers as $rowupdate) {

    $operationalUP = false;
    $colorUP ="color:".$rowupdate['id'];
    $color2UP ="color2:".$rowupdate['id'];
    if ($_POST[('operating:'.$rowupdate['id'])] == "On") {
      $operationalUP = true;
    }

    $stmt->bindParam(':printer_id', $rowupdate['id']);
    $stmt->bindParam(':operational',$operationalUP);
    $stmt->bindParam(':color', $_POST[$colorUP]);
    $stmt->bindParam(':color2', $_POST[$color2UP]);
    $stmt->execute();
  }
  //refresh pageto display new values.
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
    <title>Printer Management</title>

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
  <body class="text-center">

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

    <form method="POST" enctype="multipart/form-data">
  <h1 class="h3 mb-3 font-weight-normal">Manage Printers</h1>

  <a class="btn btn-primary btn-lg" href="admin-dashboard.php" role="button">Back to Dashboard</a>
  <div class="container">
  <div class="py-3"></div>
  <div class="table-responsive">
    <table class="table table-striped table-md">
      <tbody>
        <tr>
          <thread>
            <th>id</th>
            <th>Printer Name</th>
            <th>Model</th>
            <th>Status</th>
            <th>Extruder 1</th>
            <th>Extruder 2</th>
          </thread>
        </tr>
        <!------------------------------------------->
        <?php foreach ($all_printers as $row) {
        ?>
        <tr>
          <td><?php echo $row["id"]; ?></td>
          <td><?php echo $row["printer_name"]; ?></td>
          <td><?php echo $row["make_model"]; ?></td>
          <td>
            <select class="form-control" name="operating:<?php echo $row["id"]; ?>" >
              <option  <?php if ($row["operational"]== true){echo "selected";} ?> > On
              </option>
              <option  <?php if ($row["operational"]== false){echo "selected";} ?> > Off
              </option>
            </select>
          </td>
          <td>
            <input type="text" class="form-control" name="color:<?php echo $row["id"]; ?>" value="<?php echo $row["color"]; ?>">
          </td>
          <td>
              <input type="text" name="color2:<?php echo $row["id"]; ?>" class="form-control"
                <?php if ($row["2extruder"] == true){ ?>
                  value = <?php echo $row["color2"];
                }else { ?>
                  value = <?php echo "Single-Extruder"; ?> readonly
                <?php }?> >
            <?php

            ?>
          </td>
        </tr>
        <?php
        }
        ?>
      <!------------------------------------------->
      </tbody>
    </table>
    <hr class="mb-4">
    <center>
      <button class="btn btn-primary btn-lg" type="submit">Update Printers</button>
    </center>
  </div>
</div>
</form>
</body>
</html>
