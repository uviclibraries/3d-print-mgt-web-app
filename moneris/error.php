<?php
  //
  // error.php  -  display an error message
  //
  session_start();
  include 'moneris.inc.php';

  $message = $_GET['mesg'];
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
<b>Error:</b> <?php echo "$message" ?>
</p>
</body>
</html>
