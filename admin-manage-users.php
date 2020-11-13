<?php
require ('auth-sec.php'); //Gets CAS & db
//auth-sec includes: $user, $user_email, $user_type, $user_name
//Is user Admin check
if ($user_type == 1) {
  header("Location: customer-dashboard.php");
  die();
}
if ($_GET["user_id"] == "" OR $_GET["user_id"] == NULL) {
  $stm = $conn->query("SELECT * FROM users ORDER BY id");
}
else{
  $stm = $conn->prepare("SELECT * FROM users WHERE netlink_id LIKE ? OR name LIKE ? ORDER BY id");
  $searching = "%". $_GET["user_id"]."%";
  $stm->execute([$searching,$searching]);
}

$all_users = $stm->fetchAll();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $searchpage = $_POST["searchbar"];
  header("Location: admin-manage-users.php?user_id=".$searchpage);
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

  <div class="row">

  <div class="container">
  <div class="py-3 text-left">
    <h3>Users</h3>
    <br>
    <form method="POST">
        <label for="searchbar">Search</label>
        <input type="text" id= "searchbar" name="searchbar">
        <input type="submit" name="Search" value="Search">
    </form>
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

<div class="text-center">
  <a class="btn btn-md btn-primary btn-lg" href="admin-dashboard.php" role="button">Back to Dashboard</a>
</div>

</body>
</html>
