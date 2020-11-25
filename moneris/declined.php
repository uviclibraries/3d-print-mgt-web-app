<?php
  //
  // declined.php  -  display Moneris transaction details/receipt
  //               -  Moneris returns user to this page on declined payment
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
foreach ($_POST as $key=>$result) {
  echo $key. " --holds-- " . $result;
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

/*
$inputStr = implode(", ", $input);
$sqlStr = 'INSERT INTO moneris_fields (response_order_id, response_code, date_stamp, time_stamp, result, trans_name, cardholder, card, charge_total, f4l4, message, iso_code, bank_approval_code, bank_transaction_id, txn_num, avs_response_code, cavv_result_code, INVOICE, ISSCONF, ISSNAME) VALUES (' . $inputStr . ')';
$field_results = $conn->prepare($sqlStr);
$field_results->execute();
*/
?>
</table>
<p>
This data must be stored in the database so the admin user(s) can review
it if/when required. hello.
</p>
<p> <?php echo $inputStr ?> </p>

<a href="../customer-dashboard.php">
<button type="button" type="submit">Return</button>
</a>

</body>
</html>
