<?php
require ('db.php');
$stm = $conn->prepare("SELECT * FROM print_job WHERE id=?");
$stm->execute([$_GET["job_id"]]);
$job=$stm->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = $conn->prepare("UPDATE print_job SET price = :price, infill = :infill, scale = :scale, layer_height = :layer_height, supports = :supports, copies = :copies, material_type = :material_type, staff_notes = :staff_notes WHERE id = :job_id;
  ");
  $stmt->bindParam(':job_id', $_POST["job_id"]);
  $stmt->bindParam(':price', intval($_POST["price"]), PDO::PARAM_INT);
  $stmt->bindParam(':infill', intval($_POST["infill"]), PDO::PARAM_INT);
  $stmt->bindParam(':scale', intval($_POST["scale"]), PDO::PARAM_INT);
  $stmt->bindParam(':layer_height', $_POST["layer_height"], PDO::PARAM_STR);
  $stmt->bindParam(':supports', intval($_POST["supports"]), PDO::PARAM_INT);
  $stmt->bindParam(':copies', intval($_POST["copies"]), PDO::PARAM_INT);
  $stmt->bindParam(':material_type', $_POST["material_type"]);
  $stmt->bindParam(':staff_notes', $_POST["staff_notes"]);
  $stmt->bindParam(':status', $status);
  $stmt->execute();

  header("location: admin-dashboard.php");
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
    <title>Checkout example · Bootstrap</title>

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
    <div class="container">
  <div class="py-5 text-center">
    <img class="d-block mx-auto mb-4" src="/docs/4.5/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72">
    <h1><?php echo $job["job_name"]; ?></h1>
    </div>
    
    <div class="col-md-12 order-md-1">
        <h4 class="mb-3">Status</h4>
        <form class="needs-validation" novalidate>
          <div class="row">
            <div class="col-md-3 mb-3">
                <select class="custom-select d-block w-100" id="layer-height">
                  <option <?php if ($job["status"]== "not_priced"){echo "selected";} ?>>Not Priced</option>
                  <option <?php if ($job["status"]== "pending_payment"){echo "selected";} ?>>Pending Payment</option>
                  <option <?php if ($job["status"]== "ready_to_print"){echo "selected";} ?>>Ready to Print</option>
                  <option <?php if ($job["status"]== "printing"){echo "selected";} ?>>Printing</option>
                  <option <?php if ($job["status"]== "complete"){echo "selected";} ?>>Complete</option>
                </select>
              </div>
              </div>
          </div>

          <div class="col-md-12 order-md-1">
            <h4 class="mb-3">Submission Date</h4>
            <form class="needs-validation" novalidate>
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
            <h4 class="mb-3">Price</h4>
            <form class="needs-validation" novalidate>
              <div class="row">
                  <div class="col-md-3 mb-3">
                      <div class="input-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                          <input type="text" class="form-control" value="<?php echo $job["price"]; ?>">
                          </div>
                      </div>
                      <div class="invalid-feedback" style="width: 100%;">
                      Status is required.
                      </div>
                      </div>
                  </div>
              </div>

    <hr class="mb-6">

    <h3 class="mb-3">3D Model</h3>
    <form action="/action_page.php">
        <input type="file" id="myFile" name="filename" disabled>
      </form>
      <br>
      <hr class="mb-6">

      <h3 class="mb-3">Modified 3D Model</h3>
    <small class="text-muted">Only if needed</small>
    <br />
    <small class="text-muted">(Max 200MB)</small>
    <form action="/action_page.php">
        <input type="file" id="myFile" name="filename">
      </form>
      <br>
      <hr class="mb-6">

    <div class="col-md-12 order-md-1">
      <h4 class="mb-3">Specifications</h4>
      <form class="needs-validation" novalidate>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="username">Infill</label>
                <div class="input-group">
                  <div class="input-group mb-3">
                    <input type="text" class="form-control" value="<?php echo $job["infill"]; ?>" placeholder="100" aria-label="100" aria-describedby="basic-addon2">
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
                    <input type="text" class="form-control" value="<?php echo $job["scale"]; ?>" placeholder="100" aria-label="100" aria-describedby="basic-addon2">
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
            <select class="custom-select d-block w-100" id="layer-height">
              <option <?php if ($job["layer_height"]== 0.2){echo "selected";} ?>>0.2</option>
              <option <?php if ($job["layer_height"]== 0.1){echo "selected";} ?>>0.1</option>
              <option <?php if ($job["layer_height"]== 0.15){echo "selected";} ?>>0.15</option>
              <option <?php if ($job["layer_height"]== 0.3){echo "selected";} ?>>0.3</option>
              <option <?php if ($job["layer_height"]== 0.6){echo "selected";} ?>>0.6</option>
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <label for="supports">Supports</label>
            <select class="custom-select d-block w-100" id="supports">
              <option <?php if ($job["supports"]== 1){echo "selected";} ?>>Yes</option>
              <option <?php if ($job["supports"]== 0){echo "selected";} ?>>No</option>
            </select>
          </div>
        </div>

        <div>
        <hr class="mb-4">
          <div class="col-md-3 mb-3">
            <label for="copies">Copies</label>
            <select class="custom-select d-block w-100" id="supports">
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
            <input id="pla" name="materialType" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "PLA"){echo "checked";} ?>>
            <label class="custom-control-label" for="pla">PLA</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="pla-pva" name="materialType" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "PLA + PVA"){echo "checked";} ?>>
            <label class="custom-control-label" for="pla-pva">PLA + PVA</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="tpu95" name="materialType" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "TPU95"){echo "checked";} ?>>
            <label class="custom-control-label" for="tpu95">TPU95</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="other" name="materialType" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "Other"){echo "checked";} ?>>
            <label class="custom-control-label" for="other">Other</label>
            <small class="text-muted"> - Elaborate in Additional Comments section</small>
          </div>
        </div>

        <hr class="mb-4">
        <h5 class="mb-2">Additional Comments</h5>
            <div class="input-group">
                <textarea class="form-control" aria-label="additional-comments" readonly><?php echo $job["comments"]; ?></textarea>
            </div>

        <hr class="mb-4">
        <h5 class="mb-2">Staff Notes</h5>
            <div class="input-group">
                <textarea class="form-control" aria-label="additional-comments"><?php echo $job["staff_notes"]; ?></textarea>
            </div>
            <div class="invalid-feedback">
            Please enter additional comments.
            </div>
        </div>

        <hr class="mb-4">
        <div class="row"> 
            <div class="col-md-6 mb-3">
                <a href="url">
                    <button class="btn btn-primary btn-lg btn-block" class="form-control" type="submit" data-inline="true">Save</button>
                </a>
            </div>
            <div class="col-md-6 mb-3">
                <a class="btn btn-primary btn-lg btn-block" href="admin-dashboard.html" role="button">Back to Dashboard</a>
        </div>
      </form>
    </div>
  </div>

  <p></p>
  <br>
  <p></p>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
      <script>window.jQuery || document.write('<script src="/docs/4.5/assets/js/vendor/jquery.slim.min.js"><\/script>')</script><script src="/docs/4.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-1CmrxMRARb6aLqgBO7yyAxTOQE2AKb9GfXnEo760AUcUmFx3ibVJJAzGytlQcNXd" crossorigin="anonymous"></script>
        <script src="form-validation.js"></script></body>
</html>