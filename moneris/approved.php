<?php
  //
  // approved.php  -  display Moneris transaction details/receipt
  //               -  Moneris returns user to this page on approved payment
  //
  session_start();
  include '../auth-sec.php';
  include 'moneris.inc.php';


?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <link rel="stylesheet" type="text/css" href="css/style.css" />
  <title>Moneris Demo Approved</title>
</head>
<body>
<p>
<b>Moneris Demo</b>
</p>
<p>
Your payment has been approved.  A receipt should have been sent to
<?php echo $user_email; ?> from Moneris.
</p>
<p>
The complete Moneris transaction response fields are:
</p>
<table>
<?php

//Change Status
$current_date = date("Y-m-d");
$current_status = "paid";
$stmt = $conn->prepare("UPDATE print_job SET status = :status, paid_date = :rdy WHERE id = :job_id");
$stmt->bindParam(':status', $current_status);
$stmt->bindParam(':rdy', $current_date);
$stmt->bindParam(':job_id', $_SESSION['job_id']);
$stmt->execute();

echo "post";
foreach ($_POST as $key=>$result) {
  echo $key. " >--holds--> " . $result;
  ?>
  <br>
  <?php
}

  $input   = array();
  $columns = implode(", ",$moneris_response_fields);
  foreach ($moneris_response_fields as $field) {
    if (array_key_exists($field, $_POST)) {
      $value = $_POST[$field];
    }
    else {
      $value = '';
    }
    echo "<tr><td>$field</td><td>$value</td></tr>\n";
    if ($value == '' OR $value == "" OR $value == NULL) {
      $input[] = "NULL";
    }else{
      $input[] = $value;
    }
  }
$inputStr = implode(", ", $input);

?>
<p><?php echo $columns ?> </p>
<p> <?php echo $inputStr ?> </p>

<?php
//add to moneris_fields
/*
$sql = "INSERT INTO moneris_fields  " . $columns ." VALUES " . $inputStr;
$stm = $conn->prepare($sql);
$stm->execute();
*/

?>
</table>
<p>
This data must be stored in the database so the admin user(s) can review
it if/when required.
</p>
<a href="../customer-dashboard.php">
<button type="button" type="submit">Return</button>
</a>

</body>
</html>
