<?php
session_start();
require ('auth-sec.php'); //Gets CAS & db
$stm = $conn->query("SELECT VERSION()");
#$version = $stm->fetch();
#echo $version;

//hey there

$status = "submitted";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  try {

      // Undefined | Multiple Files | $_FILES Corruption Attack
      // If this request falls under any of them, treat it invalid.
      if (
          !isset($_FILES["3d_model"]['error']) ||
          is_array($_FILES["3d_model"]['error'])
      ) {
 #         throw new RuntimeException('Invalid parameters.');
      }

      switch ($_FILES["3d_model"]['error']) {
          case UPLOAD_ERR_OK:
              break;
          case UPLOAD_ERR_NO_FILE:
              throw new RuntimeException('No file sent.');
          case UPLOAD_ERR_INI_SIZE:
          case UPLOAD_ERR_FORM_SIZE:
              throw new RuntimeException('Exceeded filesize limit.');
          default:
              throw new RuntimeException('Unknown errors.');
      }

      // You should also check filesize here.
      if ($_FILES["3d_model"]['size'] > 200000000) {

          throw new RuntimeException('Exceeded filesize limit.');
      }

      // DO NOT TRUST $_FILES["3d_model"]['mime'] VALUE !!
      // Check MIME Type by yourself.
      $file_name = $_FILES["3d_model"]['name'];
      $file_array = explode(".",$file_name);
      $ext = end($file_array);
      $explode_len = count($file_array);
      if (!in_array($ext, ["stl", "STL", "obj", "3mf", "gcode","svg", "pdf", "PDF"])|| $explode_len > 2) {
          throw new RuntimeException('Invalid file format.');
      }

      // You should name it uniquely.
      // DO NOT USE $_FILES["3d_model"]['name'] WITHOUT ANY VALIDATION !!
      // On this example, obtain safe unique name from its binary data.
      $date = new DateTime();
      $hash_name = sprintf("%s-%s.%s", sha1_file($_FILES["3d_model"]['tmp_name']),
      $date->getTimestamp(),
      $ext);
      $savefilename = sprintf('./uploads/%s', $hash_name,);
      if (!move_uploaded_file(
          $_FILES["3d_model"]['tmp_name'],
          $savefilename
      )) {
          throw new RuntimeException('Failed to move uploaded file.');
      }

      echo 'File is uploaded successfully.';

  } catch (RuntimeException $e) {

      echo $e->getMessage();

  }
/*  Check inputs here */
  $current_date = date("Y-m-d");
  $infill_bind = intval($_POST["infill"]);
  $scale_bind = intval($_POST["scale"]);
  $layer_bind = floatval(number_format((float)$_POST["layer_height"], 2, '.',''));
  $support_bind = intval($_POST["supports"]);
  $copies_bind = intval($_POST["copies"]);

  $laser_copies = intval($_POST["laser_copies"]);

  $good_statement = True;
  $stmt = $conn->prepare("INSERT INTO web_job (netlink_id, job_name, submission_date, status) VALUES (:netlink_id, :job_name, :submission_date, :job_status)");
  $stmt->bindParam(':netlink_id', $user);
  $stmt->bindParam(':job_name', $_POST["job_name"]);
  $stmt->bindParam(':job_status', $status);
  $stmt->bindParam(':submission_date', $current_date);
  $good_statement &= $stmt->execute();

  /*TODO also validate laser cutting variables*/

  if(!$good_statement){
    die("Error during SQL execution");
  }

  // Extract most recent use id from the web job table based on user netlink id

  $stmt = $conn->prepare("SELECT MAX(id) FROM web_job WHERE netlink_id=:user_netlink");
  $stmt->bindParam(':user_netlink', $user);
  $good_statement &= $stmt->execute();
  $curr_id = $stmt->fetch(PDO::FETCH_NUM)[0];

  if(!$good_statement){
    die("Error during SQL execution");
  }

  if(!$curr_id){
    die('No web job entry for username {$user}');
  }

  if($_POST["job_type"] == "3D Print"){
    // Use the extracted id to insert job information to the 3d print table

    $stmt = $conn->prepare("INSERT INTO 3d_print_job (3d_print_id, model_name, infill, scale, layer_height, supports, copies, material_type, comments) VALUES (:3d_print_id, :model_name, :infill, :scale, :layer_height, :supports, :copies, :material_type, :comments)");
    $stmt->bindParam(':3d_print_id', $curr_id);
    $stmt->bindParam(':model_name', $hash_name);
    $stmt->bindParam(':infill', $infill_bind, PDO::PARAM_INT);
    $stmt->bindParam(':scale', $scale_bind , PDO::PARAM_INT);
    $stmt->bindParam(':layer_height', $layer_bind);
    $stmt->bindParam(':supports', $support_bind, PDO::PARAM_INT);
    $stmt->bindParam(':copies', $copies_bind, PDO::PARAM_INT);
    $stmt->bindParam(':material_type', $_POST["print_material_type"]);
    $stmt->bindParam(':comments', $_POST["comments"]);
    $stmt->execute();
  }

  elseif($_POST["job_type"] == "laser_cut"){
    // Use extracted id to insert job information to the

    $stmt = $conn->prepare("INSERT INTO laser_cut_job (laser_cut_id, model_name, copies, material_type, specifications, comments) VALUES (:laser_cut_id, :model_name, :copies, :material_type, :specifications, :comments)");
    $stmt->bindParam('laser_cut_id', $curr_id);
    $stmt->bindParam(':model_name', $hash_name);
    $stmt->bindParam(':copies', $laser_copies);
    $stmt->bindParam(':material_type', $_POST["laser_material_type"]);
    $stmt->bindParam(':specifications', $_POST["user_specs"]);
    $stmt->bindParam(':comments', $_POST["comments"]);
    $stmt->execute();


  }
  else{
    // Invalid job type
    $job_type = $_POST["job_type"];
    die("$job_type invalid job type");
  }

  $direct_link = "https://onlineacademiccommunity.uvic.ca/dsc/how-to-3d-print/";
  $msg = "
  <html>
  <head>
  <title>HTML email</title>
  </head>
  <body>
  <p>Hello, ".$user_name.". This is an automated message from the DSC.</p>
  <p>Thank you for submiting your print request to the DSC at McPherson Library. We will evalute the cost of the print and you'll be notified by email when it is ready for payment. If you have any questions about the process or the status of your print, please review our <a href=". $direct_link .">FAQ</a> or email us at DSCommons@uvic.ca.</p>
  </body>
  </html>";
  $headers = "MIME-Version: 1.0" . "\r\n";
  $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
  $headers .= "From: dscommons@uvic.ca" . "\r\n";
  mail($user_email,"DSC - New Job",$msg,$headers);

header("location: customer-dashboard.php");
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
    <title>New 3D print request</title>

    <!--javascript-->
    <!-- <script src="customer-new-job.js" async></script> -->

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
    <link href="form-validation.css" rel="stylesheet">
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

    <div class="container">
        <form method="POST" enctype="multipart/form-data">
  <div class="py-5 text-center">

    <h1>New 3D Print or Laser Cut Job</h1>
    </div>

    <div class="col-md-12 order-md-1">
        <h3 class="mb-3">Job Name</h3>
          <div class="row">
            <div class="col-md-12 mb-3">
              <input type="text" class="form-control" name="job_name" id="printJobName" placeholder="" autocomplete="off" value="" required>
              <div class="invalid-feedback">
                Valid print job name is required.
              </div>
            </div>
          </div>
          <hr class="mb-6">


    <h3 class="mb-3">Upload Model or Graphic</h3>
    <small class="text-muted">Accepted file types: .stl, .svg, .obj, .pdf (Max 200M)</small>
    <br>    
        <input type="file" id="myFile" name="3d_model" required>
      <br>
      <hr class="mb-6">

    <script type ="text/JavaScript">
      function setPageInfo() {
        var print_div = document.getElementById("3d_specs");
        var laser_div = document.getElementById("laser_specs");
        var submit = document.getElementById("submit_section");
        print_div.style.display = "none";
        laser_div.style.display = "none";
        submit.style.display = "none";
        //alert("Print info set");
      }
      window.onload = setPageInfo;
    </script>

    <script type="text/JavaScript">
      function showPrintInfo() {
        var print_div = document.getElementById("3d_specs");
        var laser_div = document.getElementById("laser_specs");
        var submit = document.getElementById("submit_section");
        //alert("Print info displayed");
        if (print_div.style.display == "none") {
          print_div.style.display = "block";
          laser_div.style.display = "none";
          submit.style.display = "block";
        }
        
      }
    </script>

    <script type="text/JavaScript">
      function showLaserInfo() {
        var print_div = document.getElementById("3d_specs");
        var laser_div = document.getElementById("laser_specs");
        var submit = document.getElementById("submit_section");
        //alert("Laser info displayed");
        if (laser_div.style.display == "none") {
          print_div.style.display = "none";        
          laser_div.style.display = "block";
          submit.style.display = "block";
        }
      }
    </script>

    <h3 class="mb-2">Job Type</h3>
      <div class="d-block my-3">
        <div class="custom-control custom-radio">
          <input id="3d_print" name="job_type" value="3D Print" type="radio" class="custom-control-input" onclick="showPrintInfo()" required>
          <label class="custom-control-label" for="3d_print">3D Print</label>
        </div>
        <div class="custom-control custom-radio">
          <input id="laser_cut" name="job_type" value="laser_cut" type="radio" class="custom-control-input" onclick="showLaserInfo()" required>
          <label class="custom-control-label" for="laser_cut">Laser Cut</label>
        </div>
    <br>
    <hr class="mb-6">
      

      <!--allows popup for Specifications-->
      <script>
      $(function () {
          $('[data-toggle="tooltip"]').tooltip()
      })
      </script>

    <div id="3d_specs" class="col-md-12 order-md-1">
      <h3 class="mb-3">3D Print Specifications <a href="https://onlineacademiccommunity.uvic.ca/dsc/how-to-3d-print/#settings" target="_blank" data-toggle="tooltip" data-placement="right" title="FAQ Specifications section">?</a></h3>
        <div class="row">
            <div class="col-md-3 mb-3" data-toggle="tooltip" data-placement="right" data-trigger="click" title="The percentage of the interior that is made up of material. More infill increases strength, print time, and cost.">
                <label for="infill">Infill</label>
                <div class="input-group">
                  <div class="input-group mb-3">
                    <input type="number" max="100" min="0" class="form-control" name="infill" value="10" aria-label="100" aria-describedby="basic-addon2" required>
                    <div class="input-group-append">
                    <span class="input-group-text" id="basic-addon2">%</span>
                    </div>
                </div>
                <div class="invalid-feedback" style="width: 100%;">
                Infill is required.
                </div>
            </div>
            </div>
            <div class="col-md-3 mb-3" data-toggle="tooltip" data-placement="right" title="The size based on the design of the file.">
                <label for="scale">Scale</label>
                <div class="input-group">
                <div class="input-group mb-3">
                    <input type="number" min="1" class="form-control" name="scale" value="100" aria-label="100" aria-describedby="basic-addon2" required>
                    <div class="input-group-append">
                        <span class="input-group-text" id="basic-addon2">%</span>
                    </div>
                    </div>
                <div class="invalid-feedback" style="width: 100%;">
                    Scale is required.
                </div>
                </div>
            </div>
        </div>

        <div class="row">
          <div class="col-md-3 mb-3" data-toggle="tooltip" data-placement="right" title="Thickness of each layer. Smaller heights increases detail and print time.">
            <label for="layer-height">Layer Height</label>
            <select class="custom-select d-block w-100" name="layer_height" id="layer-height" required>
              <option selected="selected">0.2</option>
              <option>0.15</option>
              <option>0.1</option>
              <option>0.06</option>
            </select>
            <div class="invalid-feedback">
              Please select a valid layer height.
            </div>
          </div>
          <div class="col-md-3 mb-3" data-toggle="tooltip" data-placement="right" title="3D printers cannot print large overhangs and supports allow builing sections. Supports use material and increase cost.">
            <label for="supports">Supports</label>
            <select class="custom-select d-block w-100" name="supports" id="supports" required>
              <option value="1">Yes</option>
              <option value="0">No</option>
            </select>
            <div class="invalid-feedback">
              Please provide a valid response.
            </div>
          </div>
        </div>

        <div>
        <hr class="mb-4">
          <div class="col-md-3 mb-3">
            <label for="supports">Copies</label>
            <select class="custom-select d-block w-100" name="copies" id="supports" required>
              <option>1</option>
              <option>2</option>
              <option>3</option>
              <option>4</option>
              <option>5</option>
              <option>6</option>
              <option>7</option>
              <option>8</option>
              <option>9</option>
              <option>10</option>
            </select>
            <div class="invalid-feedback">
              Please provide a valid response.
            </div>
          </div>
        </div>

        <hr class="mb-4">
        <h3 class="mb-2">3D Print Material Type <a href="https://onlineacademiccommunity.uvic.ca/dsc/how-to-3d-print/#filaments" target="_blank" data-toggle="tooltip" data-placement="right" title="FAQ Material section">?</a></h3>
        <div class="d-block my-3">
          <div class="custom-control custom-radio">
            <input id="pla" name="print_material_type" value="PLA" type="radio" class="custom-control-input" checked required>
            <label class="custom-control-label" for="pla">PLA</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="pla-pva" name="print_material_type" value="PLA + PVA" type="radio" class="custom-control-input" required>
            <label class="custom-control-label" for="pla-pva">PLA + PVA</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="tpu95" name="print_material_type" value="TPU95" type="radio" class="custom-control-input" required>
            <label class="custom-control-label" for="tpu95">TPU95</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="other" name="print_material_type" value="Other" type="radio" class="custom-control-input" required>
            <label class="custom-control-label" for="other">Other</label>
            <small class="text-muted"> - Elaborate in Additional Comments section</small>
          </div>
        </div>
    </div>

    <!-- script for first block -->

    <!-- new block start-->
    <div id="laser_specs" class="col-md-12 order-md-1">
      <!--change link to a future laser cut FAQ page-->
      <div>
      <h3 class="mb-3"> Laser Cut Specifications <a href="https://onlineacademiccommunity.uvic.ca/dsc/how-to-3d-print/#settings" target="_blank" data-toggle="tooltip" data-placement="right" title="FAQ Specifications section">?</a></h3>
        <label class="mb-2"> Indicate either cut or engrave properties for each color in laser cut graphic (temporary) </label>
            <div class="input-group">
                <textarea class="form-control" name="user_specs" aria-label="user-specs"></textarea>
            </div>
            <div class="invalid-feedback">
            Please provide laser cutting specifications
            </div>
      </div>

        <div>
        <hr class="mb-4">
          <div class="col-md-3 mb-3">
            <label for="supports">Copies</label>
            <select class="custom-select d-block w-100" name="laser_copies" id="supports" required>
              <option>1</option>
              <option>2</option>
              <option>3</option>
              <option>4</option>
              <option>5</option>
              <option>6</option>
              <option>7</option>
              <option>8</option>
              <option>9</option>
              <option>10</option>
            </select>
            <div class="invalid-feedback">
              Please provide a valid response.
            </div>
          </div>
        </div>

        <hr class="mb-4">
        <!--change link to a future laser cut FAQ page-->
        <h3 class="mb-2">Laser Cut Material Type <a href="https://onlineacademiccommunity.uvic.ca/dsc/how-to-3d-print/#filaments" target="_blank" data-toggle="tooltip" data-placement="right" title="FAQ Material section">?</a></h3>
        <div class="d-block my-3">
          <div class="custom-control custom-radio">
            <input id="mdf_6mm" name="laser_material_type" value="MDF 6mm" type="radio" class="custom-control-input" checked required>
            <label class="custom-control-label" for="mdf_6mm">MDF 6mm</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="mdf_3mm" name="laser_material_type" value="MDF 3mm" type="radio" class="custom-control-input" required>
            <label class="custom-control-label" for="mdf_3mm">MDF 3mm</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="plywood_6mm" name="laser_material_type" value="Plywood 6mm" type="radio" class="custom-control-input" required>
            <label class="custom-control-label" for="plywood_6mm">Plywood 6mm</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="plywood_3mm" name="laser_material_type" value="Plywood 3mm" type="radio" class="custom-control-input" required>
            <label class="custom-control-label" for="plywood_3mm">Plywood 3mm</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="laser_cut_other" name="laser_material_type" value="Other" type="radio" class="custom-control-input" required>
            <label class="custom-control-label" for="laser_cut_other">Other</label>
            <small class="text-muted"> - Elaborate in Additional Comments section</small>
          </div>
        </div>
    </div>

    <div id="submit_section" class="col-md-12 order-md-1"> 
      <hr class="mb-4">
          <h3 class="mb-2">Additional Comments</h3>
              <div class="input-group">
                  <textarea class="form-control" name="comments" aria-label="additional-comments"></textarea>
              </div>
              <div class="invalid-feedback">
                Please enter additional comments.
              </div>
          

          <hr class="mb-4">
          <center>
              <form action="customer-dashboard.php">
                  <button class="btn btn-primary btn-lg" type="submit">Submit</button>
              </form>
          </center>
      </div>
    <!-- new block end-->
    </form>
  </div>

  <p></p>
  <br>
  <p></p>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
      <script>window.jQuery || document.write('<script src="/docs/4.5/assets/js/vendor/jquery.slim.min.js"><\/script>')</script><script src="/docs/4.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-1CmrxMRARb6aLqgBO7yyAxTOQE2AKb9GfXnEo760AUcUmFx3ibVJJAzGytlQcNXd" crossorigin="anonymous"></script>
        <script src="form-validation.js"></script></body>
</html>
