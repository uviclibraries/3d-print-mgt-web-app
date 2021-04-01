<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/uvic_banner.css">
    <title>temp error page</title>
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





    <h1>You've run into an error</h1>
      <p>sorry, but the ldap was unsuccessful</p>
      <p>
        <?php if (isset($_GET['mesg'])) {
          echo "Error message: \n " . $_GET['mesg'];
        } ?>
      </p>
      <p>please contact the webdeveloper with the error message at kenziewong@gmail.com</p>
      <p>Thank you.</p>
  </body>
</html>
