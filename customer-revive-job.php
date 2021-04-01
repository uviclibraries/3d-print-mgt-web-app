<?php
session_start();
require ('auth-sec.php'); //Gets CAS & db
//auth-sec includes: $user, $user_email, $user_type, $user_name
$stm = $conn->prepare("SELECT * FROM print_job WHERE id=?");
$stm->execute([$_GET["job_id"]]);
$job=$stm->fetch();

//Only owner and admin can see.
if (($user != $job["netlink_id"] && $user_type == 1) || !is_file(("uploads/" . $job['model_name']))) {
  header("Location: customer-dashboard.php");
  die();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = $conn->prepare("UPDATE print_job SET price = :price, infill = :infill, scale = :scale, layer_height = :layer_height, supports = :supports, copies = :copies, material_type = :material_type, status = :status, comments = :comments, submission_date = :submission_date WHERE id = :job_id;
  ");
  $current_date = date("Y-m-d h:i");
  $status = "submitted";
  $stmt->bindParam(':job_id', $job['id']);
  $price = floatval(number_format((float)0, 2, '.',''));
  $stmt->bindParam(':price', $price);
  $infill = intval($_POST["infill"]);
  $stmt->bindParam(':infill', $infill, PDO::PARAM_INT);
  $scale = intval($_POST["scale"]);
  $stmt->bindParam(':scale', $scale, PDO::PARAM_INT);
  $stmt->bindParam(':layer_height', $_POST["layer_height"], PDO::PARAM_STR);
  $supports = intval($_POST["supports"]) ;
  $stmt->bindParam(':supports', $supports , PDO::PARAM_INT);
  $copies = intval($_POST["copies"]);
  $stmt->bindParam(':copies', $copies , PDO::PARAM_INT);
  $stmt->bindParam(':material_type', $_POST["material_type"]);
  $stmt->bindParam(':comments', $_POST["comments"]);
  $stmt->bindParam(':status', $status);
  $stmt->bindParam(':submission_date', $current_date);

  //email user
  //get job owner details
  $userSQL = $conn->prepare("SELECT * FROM users WHERE netlink_id = :netlink_id");
  $userSQL->bindParam(':netlink_id', $job['netlink_id']);
  $userSQL->execute();
  $job_owner = $userSQL->fetch();

  $direct_link = "https://onlineacademiccommunity.uvic.ca/dsc/how-to-3d-print/";
  $msg = "
  <html>
  <head>
  <title>HTML email</title>
  </head>
  <body>
  <p>Hello, ".$user_name.". This is an automated message from the DSC.</p>
  <p>You have revived a print request with the DSC at McPherson Library. We will evalute the cost of the print and you'll be notified by email when it is ready for payment. If you have any questions about the process or the status of your print, please review our <a href=". $direct_link .">FAQ</a> or email us at DSCommons@uvic.ca.</p>
  </body>
  </html>";
  $headers = "MIME-Version: 1.0" . "\r\n";
  $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
  $headers .= "From: dscommons@uvic.ca" . "\r\n";
  mail($job_owner['email'],"3D Print - Revived Job",$msg,$headers);


  $stmt->execute();

  //exit to dashboard after saving
  $destination = "location: customer-job-information.php?job_id=" . $job['id'];
  header($destination);
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
    <title>Job id: <?php echo $job["id"] ?></title>

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
    <h1><?php echo $job["job_name"]; ?></h1>
    </div>

    <div class="col-md-12 order-md-1">
        <h4 class="mb-3">Status</h4>
          <div class="row">
            <div class="col-md-3 mb-3">
              <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="100" value="<?php echo $job["status"]; ?>" aria-label="100" aria-describedby="basic-addon2" readonly>
              </div>
              </div>
              </div>
          </div>

          <div class="col-md-12 order-md-1">
            <h4 class="mb-3">Submission Date</h4>
              <div class="row">
                  <div class="col-md-3 mb-3">
                      <div class="input-group">
                        <div class="input-group">
                          <input type="text" class="form-control" value="<?php echo $job["submission_date"]; ?>" readonly>
                          </div>
                      </div>
                      <div class="invalid-feedback" style="width: 100%;">
                      Status is required.
                      </div>
                      </div>
                  </div>
              </div>

          <div class="col-md-12 order-md-1">


    <hr class="mb-6">

    <h3 class="mb-3">3D Model</h3>
        <?php
        if (is_file(("uploads/" . $job['model_name']))) {
            ?>
            <!--Grabs file and renames it to the job name when downloaded-->
            <a href="<?php echo "uploads/" . $job['model_name']; ?>" download="<?php
                $filetype = explode(".", $job['model_name']);
                echo $job['job_name'] . "." . $filetype[1]; ?>">
                Download 3D file
            </a>
        <?php
        }
        else{ ?>
          <p>File Deleted</p>
        <?php } ?>
      <br>
      <hr class="mb-6">

    <div class="col-md-12 order-md-1">
      <h4 class="mb-3">Specifications</h4>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="username">Infill</label>
                <div class="input-group">
                  <div class="input-group mb-3">
                    <input type="text" name="infill" class="form-control" value="<?php echo $job["infill"]; ?>" placeholder="100" aria-label="100" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                    <span class="input-group-text" id="basic-addon2">%</span>
                    </div>
                </div>
                <div class="invalid-feedback" style="width: 100%;">
                Infill is required.
                </div>
            </div>
            </div>
            <div class="col-md-3 mb-3">
                <label for="username">Scale</label>
                <div class="input-group">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="scale" value="<?php echo $job["scale"]; ?>" placeholder="100" aria-label="100" aria-describedby="basic-addon2">
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
          <div class="col-md-3 mb-3">
            <label for="layer-height">Layer Height</label>
            <select class="custom-select d-block w-100" name="layer_height" id="layer-height">
              <option <?php if ($job["layer_height"]== 0.4){echo "selected";} ?>>0.4</option>
              <option <?php if ($job["layer_height"]== 0.3){echo "selected";} ?>>0.3</option>
              <option <?php if ($job["layer_height"]== 0.2){echo "selected";} ?>>0.2</option>
              <option <?php if ($job["layer_height"]== 0.15){echo "selected";} ?>>0.15</option>
              <option <?php if ($job["layer_height"]== 0.1){echo "selected";} ?>>0.1</option>
              <option <?php if ($job["layer_height"]== 0.06){echo "selected";} ?>>0.06</option>
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <label for="supports">Supports</label>
            <select class="custom-select d-block w-100" name="supports" id="supports">
              <option value = 1  <?php if ($job["supports"]== 1){echo "selected";} ?>>Yes</option>
              <option value = 0 <?php if ($job["supports"]== 0){echo "selected";} ?>>No</option>
            </select>
          </div>
        </div>

        <div>
        <hr class="mb-4">
          <div class="col-md-3 mb-3">
            <label for="copies">Copies</label>
            <select class="custom-select d-block w-100" name="copies" id="copies">
              <option <?php if ($job["copies"]== 1){echo "selected";} ?>>1</option>
              <option <?php if ($job["copies"]== 2){echo "selected";} ?>>2</option>
              <option <?php if ($job["copies"]== 3){echo "selected";} ?>>3</option>
              <option <?php if ($job["copies"]== 4){echo "selected";} ?>>4</option>
              <option <?php if ($job["copies"]== 5){echo "selected";} ?>>5</option>
              <option <?php if ($job["copies"]== 6){echo "selected";} ?>>6</option>
              <option <?php if ($job["copies"]== 7){echo "selected";} ?>>7</option>
              <option <?php if ($job["copies"]== 8){echo "selected";} ?>>8</option>
              <option <?php if ($job["copies"]== 9){echo "selected";} ?>>9</option>
              <option <?php if ($job["copies"]== 10){echo "selected";} ?>>10</option>
            </select>
          </div>
        </div>

        <hr class="mb-4">
        <h5 class="mb-2">Material Type</h5>
        <div class="d-block my-3">
          <div class="custom-control custom-radio">
            <input id="pla" name="material_type" value="PLA" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "PLA"){echo "checked";} ?>>
            <label class="custom-control-label" for="pla">PLA</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="pla-pva" name="material_type" value="PLA + PVA" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "PLA + PVA"){echo "checked";} ?>>
            <label class="custom-control-label" for="pla-pva">PLA + PVA</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="tpu95" name="material_type" value="TPU95" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "TPU95"){echo "checked";} ?>>
            <label class="custom-control-label" for="tpu95">TPU95</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="other" name="material_type" value="Other" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "Other"){echo "checked";} ?>>
            <label class="custom-control-label" for="other">Other</label>
            <small class="text-muted"> - Elaborate in Additional Comments section</small>
          </div>
        </div>

        <hr class="mb-4">
        <h5 class="mb-2">Additional Comments</h5>
            <div class="input-group">
                <textarea class="form-control" name="comments" aria-label="additional-comments"><?php echo $job["comments"]; ?></textarea>
            </div>

        </div>


        <hr class="mb-4">
        <div class="row">
            <div class="col-md-6 mb-3">
                <a href="url">
                    <button class="btn btn-primary btn-lg btn-block" class="form-control" type="submit" data-inline="true">Revive</button>
                </a>
            </div>
            <div class="col-md-6 mb-3">
                <a class="btn btn-primary btn-lg btn-block" href="cutomer-dashboard.php" role="button">Cancel</a>
        </div>
    </div>
    </form>
  </div>

  <p></p>
  <br>
  <p></p>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
      <script>window.jQuery || document.write('<script src="/docs/4.5/assets/js/vendor/jquery.slim.min.js"><\/script>')</script><script src="/docs/4.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-1CmrxMRARb6aLqgBO7yyAxTOQE2AKb9GfXnEo760AUcUmFx3ibVJJAzGytlQcNXd" crossorigin="anonymous"></script>
        <script src="form-validation.js"></script>
        </body>
</html>
