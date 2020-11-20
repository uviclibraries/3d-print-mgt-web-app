<?php
  //
  // cancelled.php  -  Moneris returns user to this when they cancel
  //
  session_start();
  include 'moneris.inc.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <link rel="stylesheet" type="text/css" href="css/style.css" />
  <title>Moneris Demo</title>
</head>
<body>
<p>
<b>Moneris Demo</b>
</p>
<p>
Your payment attempt was cancelled.
</p>
<p>
The Moneris transaction response fields are:
</p>
<table>
 <tr><td>order_id</td><td><?php echo $_GET['order_id']; ?></td></tr>
</table>
</body>
</html>
