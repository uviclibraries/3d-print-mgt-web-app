<?php
require ('db.php');
$stm = $conn->prepare("SELECT * FROM print_job WHERE id=?");
$stm->execute([$_GET["job_id"]]);
$job=$stm->fetch();
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
    <h1>Submitted Print Job</h1>
    </div>

    <div class="col-md-12 order-md-1">
        <h3 class="mb-3">Print Job Name</h3>
        <form class="needs-validation" novalidate>
          <div class="row">
            <div class="col-md-12 mb-3">
              <input type="text" class="form-control" id="printJobName" placeholder="Velociraptor" value="<?php echo $job["job_name"]; ?>" required readonly>
              <div class="invalid-feedback">
                Valid print job name is required.
              </div>
            </div>
          </div>
          <!------------------->
          <div class="row">
            <div class="col-md-3 mb-3">
                <label for="username">Status</label>
                <div class="input-group">
                  <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="100" value="<?php echo $job["status"]; ?>" aria-label="100" aria-describedby="basic-addon2" readonly>
                </div>
                <div class="invalid-feedback" style="width: 100%;">
                Status is required.
                </div>
            </div>
            </div>
            </div>
            <!------------------->


          <hr class="mb-6">

    <h3 class="mb-3">Upload 3D Model</h3>
    <small class="text-muted">(Max 200MB)</small>
    <form action="/action_page.php">
        <input type="file" id="myFile" name="filename" disabled> <!-- *need*display 3d file -->
      </form>
      <br>
        
    <!-- if its priced/payed-->
    <?php if($job["status"] != "not_priced"){?>
       

        <div class="col-md-3 mb-3">
                <label for="username">Price</label>
                
                
                    <div class="input-group-prepend">
                        <span class="input-group-text">$</span>
                          <input type="text" name="price" class="form-control" value="<?php echo $job["price"]; ?>" readonly>
                    </div>
                
                
            </div>
        <!-- if its priced and not payed-->                    
        <?php if ($job["status"] == "pending_payment") { ?> 
                
                    <a href="customer-payment.php">
                        <button type="button" class="btn btn-primary btn-lg" type="submit">Payment</button>
                    </a>
            
        <?php } ?>       
    <?php }  ?>
            
        
           <!-- end if(if) else --> 
           
        </div>

      <hr class="mb-6">

    <div class="col-md-12 order-md-1">
      <h4 class="mb-3">Specifications</h4>
      <form class="needs-validation" novalidate>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="username">Infill</label>
                <div class="input-group">
                  <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="100" value="<?php echo $job["infill"]; ?>" aria-label="100" aria-describedby="basic-addon2" readonly>
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
                    <input type="text" class="form-control" placeholder="100" value="<?php echo $job["scale"]; ?>" aria-label="100" aria-describedby="basic-addon2" readonly>
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
                <label for="username">Layer Height</label>
                <div class="input-group">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="100" value="<?php echo $job["layer_height"]; ?>" aria-label="100" aria-describedby="basic-addon2" readonly>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <label for="username">Supports</label>
                <div class="input-group">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="100" value="<?php if ($job["supports"]==1) {echo "Yes";} else {echo "No";} ?>" aria-label="100" aria-describedby="basic-addon2" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
                <label for="username">Copies</label>
                <div class="input-group">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="100" value="<?php echo $job["copies"]; ?>" aria-label="100" aria-describedby="basic-addon2" readonly>
                    </div>
                </div>
            </div>

            <hr class="mb-4">
            <div class="col-md-3 mb-3">
                <label for="username">Material Type</label>
                <div class="input-group">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="100" value="<?php echo $job["material_type"]; ?>" aria-label="100" aria-describedby="basic-addon2" readonly>
                    </div>
                </div>
            </div>

        <hr class="mb-4">
        <h5 class="mb-2">Additional Comments</h5>
            <div class="input-group">
                <textarea class="form-control" aria-label="additional-comments"readonly ><?php echo $job["comments"]; ?>      
                </textarea>
            </div>
            <div class="invalid-feedback">
            Please enter additional comments.
            </div>
        </div>
        
        <hr class="mb-4">
        <center>
            <a href="customer-dashboard.php">
                <button type="button" class="btn btn-primary btn-lg" type="submit">Back to Dashboard</button>
            </a>
        </center>
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