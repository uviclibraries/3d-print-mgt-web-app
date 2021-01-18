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
  <!--header link-->
  <link rel="stylesheet" href="../css/uvic_banner.css">
  <link rel="icon" href="https://www.uvic.ca/assets/core-4-0/img/favicon-32.png">
  <title>Moneris Transaction Error</title>
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
<b>An error with Moneris Occured. Please save the error message below and contact the dscommons@uvic.ca</b>
</p>
<p>
<b>Error:</b> <?php echo "$message" ?>
</p>
</body>
</html>
