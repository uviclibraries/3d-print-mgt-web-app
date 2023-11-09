<?php
session_start();
require ('auth-sec.php'); //Gets CAS & db
//auth-sec includes: $user, $user_email, $user_type, $user_name
//Is user Admin check
if ($user_type == 1) {
  header("Location: customer-dashboard.php");
  die();
}

$getcheck = array_fill(0,2, FALSE);
if (isset($_GET["user_id"]) && ($_GET["user_id"] != "" OR $_GET["user_id"] != NULL)) {
  $getcheck[0] = True;
  $sql_line[] = "(netlink_id LIKE :netlink_id OR name LIKE :name)";
}if (isset($_GET['admin_only'])) {
  $getcheck[1] = True;
  $sql_line[] = "user_type = 0";
}

//Check if parameters are empty
if ($getcheck[0]==FALSE && $getcheck[1]==FALSE) {
  $stm = $conn->query("SELECT * FROM users ORDER BY id");
}
//find out what parameters are being searched for
else{
  //build sql query line based on search parameters
  $searchline = "SELECT * FROM users WHERE " . implode(" AND ", $sql_line) . " ORDER BY id";
  $stm = $conn->prepare($searchline);
  //echo $searchline . "\n";

  //Bind search parameters
  if ($getcheck[0] == TRUE) {
    $searching = "%". $_GET["user_id"]."%";
    $stm->bindParam(':netlink_id', $searching, PDO::PARAM_STR);
    $stm->bindParam(':name', $searching, PDO::PARAM_STR);
  }

  $stm->execute();

}


$all_users = $stm->fetchAll();

$get_line = array();
//Seach button clicked
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  if (isset($_POST["searchbar"])) {
    $get_line[] = "user_id=" . $_POST["searchbar"];
  }if (isset($_POST["admin_only"])) {
    $get_line[] = "admin_only=" . $_POST["admin_only"];
  }
  header("Location: admin-manage-users.php?".implode("&", $get_line));
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
    <title>User Management</title>

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

  <div class="row">

  <div class="container">
  <div class="py-3 text-left">
    <h3>Users</h3>
    <br>

    <div class="row">
      <div class="col-md-4">
        <form method="POST">
          <div class="">
            <label for = "admin_only">Only Admin: </label>
            <input type="checkbox" id= "admin_only" name="admin_only">
          </div>
          <input type="text" id= "searchbar" name="searchbar">
          <input type="submit" name="Search" value="Search">
        </form>
      </div>
      <div class="col-md-4 offset-md-4">
        <a class="btn btn-md btn-primary btn-" href="admin-dashboard.php" role="button">Back to Dashboard</a>
      </div>
    </div>

  <br>
  <div class="table-responsive">
    <table class="table table-striped table-md">
      <tbody>
        <tr>
          <thread>
            <th>id</th>
            <th>Name</th>
            <th>Netlink</th>
            <th>User Type</th>
            <th>email</th>
          </thread>
        </tr>
        <!------------------------------------------->
        <?php foreach ($all_users as $row) {
        ?>
        <tr>
          <td><?php echo $row["id"]; ?></td>
          <td><?php echo $row["name"]; ?></td>
          <td><a href="admin-user-specification.php?user_id=<?php echo $row["id"]; ?>"><?php echo $row["netlink_id"]; ?></a></td>
          <td>
            <?php if ($row["user_type"] == 0) {
              echo "Admin";
            }else{
              echo "Regular";
            } ?>
          </td>
          <td><?php echo $row["email"]; ?></td>
        </tr>
        <?php
        }
        ?>
      <!------------------------------------------->
      </tbody>
    </table>
  </div>

<hr class="mb-12">

</div>
</div>
</div>

</body>
</html>
