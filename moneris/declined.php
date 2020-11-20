<?php
  //
  // declined.php  -  display Moneris transaction details/receipt
  //               -  Moneris returns user to this page on declined payment
  //
  session_start();
  include 'moneris.inc.php';


  $job_id = $_SESSION['id'];
  $user_name = $_SESSION['user_name'];
  $user_email = $_SESSION['user_email'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <link rel="stylesheet" type="text/css" href="css/style.css" />
  <title>Moneris Demo Declined</title>
</head>
<body>
<p>
<b>Moneris Demo</b>
</p>
<p>
Your payment attempt has been declined.
</p>
<p>
The complete Moneris transaction response fields are:
</p>
<table>
<?php
  foreach ($moneris_response_fields as $field) {
    if (array_key_exists($field, $_POST)) {
      $value = $_POST[$field];
    }
    else {
      $value = '';
    }
    echo "<tr><td>$field</td><td>$value</td></tr>\n";
  }
?>
</table>
<p>
This data must be stored in the database so the admin user(s) can review
it if/when required. hello.
</p>

<a href="../customer-dashboard.php">
<button type="button" type="submit">Return</button>
</a>

</body>
</html>
