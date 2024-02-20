<?php
session_start();
require ('auth-sec.php'); //Gets CAS & db

/*
$stm = $conn->prepare("SELECT * FROM print_job WHERE id=?");
$stm->execute([$_GET["job_id"]]);
$job=$stm->fetch();
*/

$stm = $conn->prepare("SELECT * FROM web_job INNER JOIN large_format_print_job ON id=large_format_print_id WHERE id=?");
$stm->execute([$_GET["job_id"]]);
$job=$stm->fetch();

//Only owner and admin can see.
if ($user != $job["netlink_id"] && $user_type == 1) {
  header("Location: customer-dashboard.php");
  die();
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
    <title>Print <?php echo $job["job_name"]; ?> information</title>

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
  <div class="py-5 text-center">

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


          <hr class="mb-6">

    <h3 class="mb-3">Upload Print Document</h3>
    <?php
    if (is_file(("uploads/" . $job['model_name']))) {
        ?>
        <!--Grabs file and renames it to the job name when downloaded-->
        <a href="<?php echo "uploads/" . $job['model_name']; ?>" download="<?php
            $filetype = explode(".", $job['model_name']);
            echo $job['job_name'] . "." . $filetype[1]; ?>">
            Download Print file
        </a>
    <?php
    }
    else{ ?>
      <p>File Deleted</p>
    <?php } ?>
      <br>

    <!-- if its priced/payed-->
    <?php if($job["status"] != "submitted"){?>
      <hr class="mb-6">

        <div class="col-md-3 mb-3">
                <label for="username">Price</label>


                    <div class="input-group-prepend">
                        <span class="input-group-text">$</span>
                          <input type="text" name="price" class="form-control" value="<?php echo $job["price"]; ?>" readonly>
                    </div>


            </div>
        <!-- if its priced and not payed-->
        <?php
        if ($job["status"] == "pending payment") {
          $_SESSION['price'] = strval($job["price"]);
          $_SESSION['job_id'] = $job['id'];
          ?>
          <a href="moneris/customer-payment.php">
            <button type="button" class="btn btn-primary btn-lg" type="submit">
              Payment
            </button>
          </a>
        <?php } ?>
    <?php }  ?>
           <!-- end if(if) else -->

        </div>

      <hr class="mb-6">
<div id="large_format_specs" class="col-md-12 order-md-1">
      <h3 class="mb-3">Large Format Print Specifications</h3>
      <div class="row"> 
        <div class="col-md-3 mb-3">
            <label for="length">Length</label>
            <div class="input-group">
              <div class="input-group mb-3">
                <input type="number" step="0.01" class="form-control" placeholder="100" value="<?php echo $job["height_inches"]; ?>" aria-label="100" aria-describedby="basic-addon2" readonly>
                <div class="input-group-append">
                <span class="input-group-text" id="basic-addon2">in</span>
                </div>
                </div>  
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <label for="width">Width</label>
            <div class="input-group">
              <div class="input-group mb-3">
                <input type="number" step="0.01" class="form-control" placeholder="100" value="<?php echo $job["width_inches"]; ?>" aria-label="100" aria-describedby="basic-addon2" readonly>
                <div class="input-group-append">
                <span class="input-group-text" id="basic-addon2">in</span>
                </div>
                </div>
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
        <h5 class="mb-2">Additional Comments</h5>
            <div class="input-group">
                <textarea rows="5" cols="50" class="form-control" aria-label="additional-comments"readonly ><?php echo $job["comments"]; ?>
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

  <!-- CANCEL AND DUPLICATE JOB BUTTONS-->

<?php
// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
  // Check which form was submitted
  if (isset($_POST['cancel_job'])) {
    $query = "UPDATE web_job INNER JOIN 3d_print_job ON id=3d_print_id SET status = :status, cancelled_date = :cancelled_date, cancelled_signer = :cancelled_signer WHERE id = :id";
    
    $stmt = $conn->prepare($query);
    $status_cancelled = 'cancelled';
    $cur_date = date("Y-m-d");

    $stmt->bindParam(':status', $status_cancelled);
    $stmt->bindParam(':cancelled_signer',$user);
    $stmt->bindParam(':cancelled_date',$cur_date);
    $stmt->bindParam(':id',$job['id']);
    

    $stmt->execute();

    
    //redirect to customer dashboard upon confirm cancel and update job status in db
    echo "<script>";
    echo "window.location.replace('https://webapp.library.uvic.ca/3dprint/customer-dashboard.php');";
    echo "setTimeout(move, 3000);";
    echo "</script>";
   
  exit();
  }
}
?>

    

<style>
/* Button style */
#myBtn{
    width: 50px;
    background-color: red;
    color: white;
    padding: 10px 20px;
    border: none;
    cursor: pointer;
    text-align: center;
}

/* The Popup (background) */
.popup {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Popup Content */
.popup-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}

/* The Close Button */
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
</style>



<hr class="mb-4">
  <center>
    <!-- Button to trigger 'Cancel Job' confirmation popup; button background color set to red-->
    <button id="cancel-button" class="btn btn-primary btn-lg" style="background-color: #f44336; display:<?php if($job['status'] =='submitted'||$job['status'] == 'on hold' || $job['status'] == 'pending payment'){echo "inline-block";} else{echo "none";}?>;">Cancel Job</button> <!--cancel button-->
      <!-- The First Popup -->
      <div id="CancelJobPopup" class="popup">
        <div class="popup-content">
          <span class="close" data-popup="CancelJobPopup">&times;</span>
          <p>Are you sure you want to cancel your job?</p>
          <form method="post">
              <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job['id']); ?>">
              <input type="hidden" name="cancel_job" value="1">
              <button type="submit" class="btn btn-primary btn-lg" style="background-color: #f44336;">Cancel Job</button>
          </form>
        </div>
      </div>

    <!-- Button to trigger 'Duplicate Job' confirmation popup; button background color set to purple-->
    <button id="duplicate-button" class="btn btn-primary btn-lg" style="background-color:#CF9FFF;">Duplicate Job</button> <!--duplicate button-->
      <!-- The Second Popup -->
      <div id="DuplicateJobPopup" class="popup">
        <div class="popup-content">
          <span class="close" data-popup="DuplicateJobPopup">&times;</span>
          <p>Are you sure you want to duplicate your job?</p>
            <a href="customer-duplicate-3d-job.php?job_id=<?php echo $job["id"]; ?>">
                <button type="submit" class="btn btn-primary btn-lg" style="background-color:#CF9FFF;">Duplicate Job</button>
            </a>
        </div>
      </div>
  </center>

    <script>
    window.onload = function() {
        // Function to open a popup
        function openPopup(popupId) {
            var popup = document.getElementById(popupId);
            if (popup) {
                popup.style.display = "block";
            }
        }

        // Function to close a popup
        function closePopup(popupId) {
            var popup = document.getElementById(popupId);
            if (popup) {
                popup.style.display = "none";
            }
        }

        // Attach event listeners to buttons
        var cancelButton = document.getElementById("cancel-button");
        var duplicateButton = document.getElementById("duplicate-button");

        if (cancelButton) {
            cancelButton.onclick = function() { openPopup("CancelJobPopup"); }
        }
        if (duplicateButton) {
            duplicateButton.onclick = function() { openPopup("DuplicateJobPopup"); }
        }

        // Attach event listeners to close buttons
        var closeButtons = document.getElementsByClassName("close");
        for (var i = 0; i < closeButtons.length; i++) {
            closeButtons[i].onclick = function() {
                var popupId = this.getAttribute("data-popup");
                closePopup(popupId);
            }
        }

        // Close popup when clicking outside of it
        window.onclick = function(event) {
            if (event.target.classList.contains("popup")) {
                event.target.style.display = "none";
            }
        }
    }
    </script>

  <p></p>
  <br>
  <p></p>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
      <script>window.jQuery || document.write('<script src="/docs/4.5/assets/js/vendor/jquery.slim.min.js"><\/script>')</script><script src="/docs/4.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-1CmrxMRARb6aLqgBO7yyAxTOQE2AKb9GfXnEo760AUcUmFx3ibVJJAzGytlQcNXd" crossorigin="anonymous"></script>
        <script src="form-validation.js"></script></body>
</html>