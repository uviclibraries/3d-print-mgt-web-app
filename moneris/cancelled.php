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
  <!--header link-->
  <link rel="stylesheet" href="../css/uvic_banner.css">
  <link rel="icon" href="https://www.uvic.ca/assets/core-4-0/img/favicon-32.png">
  <title>Moneris Transaction Cancelled</title>
</head>
<body>

  <!--Header-->
  <div id="custom_header"><div class="wrapper" id="banner">
   <div style="position:absolute; left: 5px; top: 26px;">
    <a href="http://www.uvic.ca/" id="logo"><span>University of Victoria</span></a>
   </div>
   <div style="position:absolute; left: 176px; top: 26px;">
    <a href="http://www.uvic.ca/library/" id="unit"><span>Libraries</span></a>
   </div>
   <div class="edge" style="position:absolute; margin: 0px;right: 0px; top: 0px; height: 96px; width:200px;">&nbsp;</div>
  </div>
  <!--Header end-->

<p>
<b>Moneris Transaction Cancelled</b>
</p>
<p>
Your payment attempt was cancelled.
</p>
<br>
<a href="../customer-dashboard.php">
<button type="button" type="submit">Return</button>
</a>
</body>
</html>
