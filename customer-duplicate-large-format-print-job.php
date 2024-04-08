<?php
session_start();
require ('auth-sec.php'); //Gets CAS & db
$stm = $conn->query("SELECT VERSION()");
#$version = $stm->fetch();
#echo $version;

$stm = $conn->prepare("SELECT * FROM web_job INNER JOIN large_format_print_job ON id=large_format_print_id WHERE id=?");
$stm->execute([$_GET["job_id"]]);
$job=$stm->fetch();
// print_r(array_keys($job));


$jobType = "large format print";
$userView = "customer";
$type_href= '<a href="';
$type_href  = $type_href . 'customer-large-format-print-job-information.php?job_id=';


//Get users name & email
$userSQL = $conn->prepare("SELECT * FROM users WHERE netlink_id = :netlink_id");
$userSQL->bindParam(':netlink_id', $job['netlink_id']);
$userSQL->execute();
$job_owner = $userSQL->fetch();

$status = "submitted"; //declaring value that the job will take when the submission form is submitted

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $final_file_name = $job['model_name']; // Default to existing file name
  $secondary_file_name = 'NA';

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
              // You should also check filesize here.
              if ($_FILES["3d_model"]['size'] > 200000000) {

                  throw new RuntimeException('Exceeded filesize limit.');
              }

              // DO NOT TRUST $_FILES["3d_model"]['mime'] VALUE !!
              // Check MIME Type by yourself.
              $file_name = $_FILES["3d_model"]['name'];
              $file_array = explode(".",$file_name);
              $ext = array_pop($file_array);
              $explode_len = count($file_array);
              if (!in_array($ext, ["svg", "pdf", "SVG","PDF"])|| $explode_len > 2) {
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
              $final_file_name = $hash_name;
              echo $final_file_name .'<br>';
              echo 'File is uploaded successfully.';
              break;

          case UPLOAD_ERR_NO_FILE:
              //Copy over the modified art file if the user didn't upload new art.
              if (strcasecmp(trim($final_file_name), trim($job['model_name'])) == 0) {
                $secondary_file_name = $job['model_name_2'];
              }
              echo "Final: '{$final_file_name}' <br>Job: '{$job['model_name']}' <br>";
              echo $final_file_name .'<br>';
              echo 'File is uploaded successfully.';
              break;

          case UPLOAD_ERR_INI_SIZE:
          case UPLOAD_ERR_FORM_SIZE:
              throw new RuntimeException('Exceeded filesize limit.');
          default:
              throw new RuntimeException('Unknown errors.');
      }

      

  } catch (RuntimeException $e) {

      echo $e->getMessage();
  }
/*  Check inputs here */
  $current_date = date("Y-m-d");
  $good_statement = True;
  $jobName =$_POST["job_name"];
  
  //Inserts new job into web_job and and sets netlink id, job name, status=submitted, submission_date=todat, job purpose, and if for academic purpose, course code and due date.
  include('sql_snippets/insert_new_webjob_snippet.php');

  // Extract most recent use id from the web job table based on user netlink id
  include('sql_snippets/bind_user_new_snippet.php');

  //Inserts new job into laser_cut and and sets id, model (file) name, copies, material type, laser cutting specifications (text box), user comments.
  include('sql_snippets/new_laser_snippet.php');

  $userSQL = $conn->prepare("SELECT * FROM users WHERE netlink_id = :netlink_id");
  $userSQL->bindParam(':netlink_id', $job['netlink_id']);
  $userSQL->execute();
  $job_owner = $userSQL->fetch();


  include('general_partials/send_customer_email_partial.php');

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
    <title>Duplicate Laser Cut Job</title>

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
        <h1>Duplicate Laser Cut Job</h1>
      </div>

    <div class="col-md-12 order-md-1">

      <h3 class="mb-3">Job Name - <?php echo $job['job_name']. ' copy' ?></h3>
      <div class="row">
        <div class="col-md-12 mb-3">
          <input type="text" class="form-control" name="job_name" id="printJobName" placeholder="" autocomplete="off" value="<?php echo $job['job_name'] . ' copy' ?>" required>

          <div class="invalid-feedback">
            Valid print job name is required.
          </div>
        </div>
      </div>
      <hr class="mb-6">

      <h3 class="mb-3">Upload File</h3>
      <small class="text-muted">Accepted file types: .stl, .svg, .obj, .pdf (Max 200M)</small>
      <br>    

        <?php
        $fileExists = is_file("uploads/" . $job['model_name']);
        if ($fileExists) {
            ?>
            <!--Grabs file and renames it to the job name when downloaded-->
            <a href="<?php echo "uploads/" . $job['model_name']; ?>" download="<?php
                $filetype = explode(".", $job['model_name']);
                echo $job['job_name'] . "." . $filetype[1]; ?>">
                Download file
            </a>
        <?php
        }
        else{ ?>
          <p>File Deleted</p>
        <?php }
        echo '<br> Replace with a new file <br>';?>

        <input type="file" id="myFile" name="3d_model" <?php if (!$fileExists) { echo 'required'; } ?>>
      <br>
      <hr class="mb-6">


    <script type ="text/JavaScript">
      function setPageInfo() {
        var jobPurpose = "<?php echo $job['job_purpose']; ?>";
        if (jobPurpose == 'academic') {
          showAcademicCodeText({value: 'academic'});
        }
      }
      window.onload = setPageInfo;   
    </script>


    <script type="text/JavaScript">
      //Function will be triggered by the selection of job_purpose ("academic" and "personal") radio buttons. 
      //Shows the academic_code text box if the "academic" rafio button is selected, hides otherwise.
      function showAcademicCodeText(clickedRadio) {
        var academiccode_div = document.getElementById("academiccode_textbox");
        var academicdeadline_div = document.getElementById("academicdeadline_textbox");

        // Check the value of the clicked radio button
        if (clickedRadio.value === "academic") {
          academiccode_div.style.display = "inline-block";
          academiccode_div.style.width = "300px";
          academicdeadline_div.style.display = "inline-block";
          academicdeadline_div.style.width = "300px";
          
        } else if (clickedRadio.value === "personal") {
          academiccode_div.style.display = "none";
          academicdeadline_div.style.display = "none";
        }
      }
      //add field for academic job deadline
    </script>
  
    <h3 class="mb-2">Job Purpose</h3>
    <!-- contains radio buttons and optional textbox to indicate if it's a personal or academic (and academic code) job-->
      
      <div class="row">
        <div class="col-md-3 mb-3">
          <div class="custom-control custom-radio">
            <input id="academic-purpose" name="job_purpose" value="academic" type="radio" class="custom-control-input" onclick="showAcademicCodeText(this)" <?php echo ($job['job_purpose'] != 'personal') ? 'checked' : ''; ?> required>
            <label class="custom-control-label" for="academic-purpose">Academic</label>
              <span class="popup">
                &#9432
                <span class="popuptext" id="myPopup">
                  <p>Academic jobs will be prioritized during high-volume periods.</p>
                  <span class="close-btn">x</span>
                </span>
              </span> <!-- popup box for job_purpose =="academic"-->
            
          </div>
        </div><!--to fill TABLE `web_job` column `job_purpose`=="academic"; *selection will cause academiccode_textbox to appear -->
        
        <div class="col-md-3 mb-3 w-100" id="academiccode_textbox" style="display:none;padding-top: 0.5px;">
          <p>Course Code:  </p>
          <input type="text" class="form-control" name="academic_code" placeholder="<?php echo ($job['job_purpose'] === 'academic') ? $job['academic_code'] : 'Course code'; ?>" autocomplete="off">
        </div>
        <div class="col-md-3 mb-3 w-100" id="academicdeadline_textbox" style="display:none;">
          <p>Assignment due date:  </p>
          <input type="date" class="form-control" name="academic_deadline" autocomplete="off" min="<?php echo date('Y-m-d'); ?>"
       value="<?php echo ($job['job_purpose'] === 'academic' && !empty($job['assignment_due_date'])) ? $job['assignment_due_date'] : date('Y-m-d'); ?>" 
       placeholder="<?php echo ($job['job_purpose'] === 'academic') ? 'Assignment Due Date' : ''; ?>">

        </div>
      </div>

      <!--Personal Jobs-->
        <div class="custom-control custom-radio">
          <input type="radio" id="personal-purpose" name="job_purpose" value="personal" class="custom-control-input" onclick="showAcademicCodeText(this)" <?php echo ($job['job_purpose'] === 'personal') ? 'checked' : ''; ?> required>
          <label class="custom-control-label" for="personal-purpose">Personal</label>
        </div><!--to fill TABLE `web_job` column `job_purpose`=="personal"-->
    
    <hr class="mb-6">
      
    <style>
      .popup {
        position: relative;
        display: inline;  /* Changed from inline-block for better alignment */
        cursor: pointer;
        padding-left: 5px;  /* Add some spacing between the header text and the trigger */
      }


      .popup .popuptext {
          visibility: hidden;
          width: 500px;
          background-color: #555;
          color: #fff;
          text-align: center;
          border-radius: 6px;
          padding: 8px 0;
          position: absolute;
          z-index: 1;
          
          /* Adjust the positioning */
          top: 0;          /* Aligns the top of the popup with the trigger text */
          left: 110%;     /* Places the popup to the right of the trigger text */
          margin-left: 20px; /* Optional: Adds some spacing between the trigger and the popup */
      }


      /* Popup leftward arrow */
      .popup .popuptext::after {
        content: "";
        position: absolute;
        top: 50%;  /* Center vertically */
        left: 0;  /* Place at the left edge */
        margin-top: -5px;  /* Adjust vertical position for true centering */
        border-width: 5px 0 5px 5px;  /* Create left-pointing arrow */
        border-style: solid;
        border-color: transparent transparent transparent #555;
      }

      /* Toggle this class - hide and show the popup */
      .popup .show {
        visibility: visible;
        -webkit-animation: fadeIn 1s;
        animation: fadeIn 1s;
      }

      .close-btn {
        position: absolute;
        top: 0;
        right: 0;
        padding: 5px 10px;
        cursor: pointer;
      }
    </style>



<div id="large_format_specs" class="col-md-12 order-md-1">
      <h3 class="mb-3">Large Format Print Specifications</h3>
      <p>Dimensions
        <span class="popup">
          &#9432
          <span class="popuptext" id="myPopup">
            <p></p>
          <span class="close-btn">x</span>
        </span>
      </span>
      </p>
      <div class="row"> 
        <div class="col-md-3 mb-3">
          <label for="length_input">Length<span class="error"></span></label>
          <input type="number" step="0.01" id="length_input" name="length_input" oninput="validateDimensions()" placeholder="Length" style="width: 200px;" value="<?php echo $job["height_inches"]; ?>" required >
          <div class="invalid-feedback">
            Please enter the desired length.
          </div>
        </div>

        <div class="col-md-3 mb-3">
          <label for="width_input">Width</label>
          <input type="number" step="0.01" id="width_input" name="width_input" oninput="validateDimensions()" placeholder="Width" style="width: 200px;" value="<?php echo $job["width_inches"]; ?>"required>
          <div class="invalid-feedback">
            Please enter the desired width.
          </div>
        </div>

        <div class="col-md-3 mb-3">
          <label for="unit_selector">Unit</label><br>
          <select id="unit_selector" name="unit_measurement" onchange="validateDimensions()" style="width: 100px;"> <!-- Adjust the width as needed -->
            <option value="in">in</option>
            <option value="cm">cm</option>
          </select>
          <div class="invalid-feedback">
            Please select a unit of measurement.
          </div>
        </div>
      </div>
      <div><span id="dimension_warning"></span>
      </div>

      <script>
        function validateDimensions(){
          console.log('validating dimensions');
          var unit = document.getElementById('unit_selector').value;
          var width = document.getElementById('width_input').value;
          var length = document.getElementById('length_input').value;
          var dimension_warning = document.getElementById("dimension_warning");

          var maxDimension = unit === 'cm' ? 91.44 : 36; // 36 inches in cm
          // console.log("unit: " + unit + "; length: " + length + ";width" + width);

          if (width > maxDimension && length > maxDimension) {
            // console.log('too large');
            dimension_warning.textContent = `Both width and length cannot exceed ${maxDimension} ${unit}. Please decrease one of the measurements.`;
            document.getElementById('length_input').value = "";
            document.getElementById('width_input').value = "";
            // if(event.type === "submit") {
            //     event.preventDefault(); // Prevent form submission
            // }
          }
          else{
            // console.log("not too large");
            dimension_warning.textContent = "";
          }
        }
        </script><!--Prevent oversize prints-->

      <div>
        <hr class="mb-4">
        <div class="col-md-3 mb-3">
          <label for="large_format_copies">Copies</label>
          <input type="number" class="form-control" name="large_format_copies" min="1" max="100" step="1" id="copies" value="<?php echo $job["copies"]; ?>" required />

          <div class="invalid-feedback">
            Please provide a valid response.
          </div>
        </div>
      </div>
    </div>


    <div id="submit_section" class="col-md-12 order-md-1"> 
      <hr class="mb-4">
          <h3 class="mb-2">Additional Comments</h3>
              <div class="input-group">
                  <textarea rows="5" cols="50" class="form-control" name="comments" aria-label="additional-comments"><?php echo htmlspecialchars($job['comments']); ?></textarea>
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

  <script>
    // Function to toggle popup
    function togglePopup(event) {
      event.stopPropagation();  // Stop event from propagating to parent elements
      console.log("popup clicked");
      var popup = event.currentTarget.querySelector('.popuptext');
      popup.classList.toggle('show');
    }

    // Function to close popup
    function closePopup(event) {
      event.stopPropagation();  // Stop event from propagating to parent elements
      event.stopPropagation();
      event.currentTarget.parentElement.classList.remove('show');
    }

    // Attach toggle function to all popups
    var popups = document.querySelectorAll('.popup');
    for (var i = 0; i < popups.length; i++) {
      popups[i].addEventListener('click', togglePopup);
    }

    // Attach close function to all close buttons
    var closeButtons = document.querySelectorAll('.close-btn');
    for (var i = 0; i < closeButtons.length; i++) {
      closeButtons[i].addEventListener('click', closePopup);
    }
  </script>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
      <script>window.jQuery || document.write('<script src="/docs/4.5/assets/js/vendor/jquery.slim.min.js"><\/script>')</script><script src="/docs/4.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-1CmrxMRARb6aLqgBO7yyAxTOQE2AKb9GfXnEo760AUcUmFx3ibVJJAzGytlQcNXd" crossorigin="anonymous"></script>
        <script src="form-validation.js"></script>

  </body>
</html>
