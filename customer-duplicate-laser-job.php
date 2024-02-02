<?php
session_start();
require ('auth-sec.php'); //Gets CAS & db
$stm = $conn->query("SELECT VERSION()");
#$version = $stm->fetch();
#echo $version;

$stm = $conn->prepare("SELECT * FROM web_job INNER JOIN laser_cut_job ON id=laser_cut_id WHERE id=?");
$stm->execute([$_GET["job_id"]]);
$job=$stm->fetch();
// print_r(array_keys($job));

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
  $laser_copies = intval($_POST["laser_copies"]);
  $good_statement = True;
  $stmt = $conn->prepare("INSERT INTO web_job (netlink_id, job_name, job_purpose, academic_code, submission_date, status) VALUES (:netlink_id, :job_name, :job_purpose, :academic_code, :submission_date, :job_status)");
  $owner_netlinkid = $job['netlink_id'];
  $stmt->bindParam(':netlink_id', $job['netlink_id']);
  $stmt->bindParam(':job_name', $_POST["job_name"]);
  $stmt->bindParam(':job_purpose', $_POST["job_purpose"]);
  $stmt->bindParam(':academic_code', $_POST["academic_code"]);
  $stmt->bindParam(':job_status', $status);
  $stmt->bindParam(':submission_date', $current_date);
  $good_statement &= $stmt->execute();

  /*TODO also validate laser cutting variables*/

  if(!$good_statement){
    die("Error during SQL execution");
  }

  // Extract most recent use id from the web job table based on user netlink id

  $stmt = $conn->prepare("SELECT MAX(id) FROM web_job WHERE netlink_id=:user_netlink");
  $stmt->bindParam(':user_netlink', $job['netlink_id']);
  $good_statement &= $stmt->execute();
  $curr_id = $stmt->fetch(PDO::FETCH_NUM)[0];

  if(!$good_statement){
    die("Error during SQL execution");
  }

  if(!$curr_id){
    die('No web job entry for username {$user}');
  }


  $stmt = $conn->prepare("INSERT INTO laser_cut_job (laser_cut_id, model_name, model_name_2, copies, material_type, specifications, comments) VALUES (:laser_cut_id, :model_name, :model_name_2, :copies, :material_type, :specifications, :comments)");
  $stmt->bindParam('laser_cut_id', $curr_id);
  $stmt->bindParam(':model_name', $final_file_name);
  $stmt->bindParam(':model_name_2', $secondary_file_name);
  $stmt->bindParam(':copies', $laser_copies);
  $stmt->bindParam(':material_type', $_POST["laser_material_type"]);
  $stmt->bindParam(':specifications', $_POST["user_specs"]);
  $stmt->bindParam(':comments', $_POST["comments"]);
  $stmt->execute();

  $userSQL = $conn->prepare("SELECT * FROM users WHERE netlink_id = :netlink_id");
  $userSQL->bindParam(':netlink_id', $job['netlink_id']);
  $userSQL->execute();
  $job_owner = $userSQL->fetch();

  $jobName =$_POST["job_name"];
  $direct_link = "https://onlineacademiccommunity.uvic.ca/dsc/how-to-laser-cut/";
  $msg = "
  <html>
  <head>
  <title>HTML email</title>
  </head>
  <body>
  <p>Hello, ".$job_owner['name'].". This is an automated message from the DSC.</p>
  <p>Thank you for submitting your laser cut request (".$jobName.") to the DSC at McPherson Library. We will evaluate the cost of the laser cut and you'll be notified by email when it is ready for payment. If you have any questions about the process or the status of your laser cut, please review our <a href=". $direct_link .">FAQ</a> or email us at DSCommons@uvic.ca.</p>
  </body>
  </html>";
  $headers = "MIME-Version: 1.0" . "\r\n";
  $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
  $headers .= "From: dscommons@uvic.ca" . "\r\n";
  mail($job_owner['email'],"DSC - New laser cut Job",$msg,$headers);

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

      <h3 class="mb-3">Upload Model or Graphic</h3>
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



    <div id="laser_specs" class="col-md-12 order-md-1">
      <!--change link to a future laser cut FAQ page-->
      <div>
      <h3 class="mb-3"> Laser Cut Specifications</h3>
        <label class="mb-2"> Indicate either cut or engrave properties for each color in laser cut graphic
          <span class="popup">
            &#9432
            <span class="popuptext" id="myPopup">
              <p>Specify if you want to <b>cut</b> or <b>engrave</b> your design.<br>
                If the file has both cutting and engraving, specify which colour is which (e.g red lines are cut, black lines are engraved).<br><br>
                <b>% Power:</b> higher power settings will result in a deeper or darker engraving.  Very high power will cut through the material instead of engraving.<br><br>
                <b>Passes:</b> similar to power, increasing the number of passes will make an engraving darker.  For fragile thin materials like paper, it may be better to have multiple passes at low power instead of a single high powered pass.<br><br>
                We use a Trotec Speedy 100 laser; any special settings can be requested in the comments box when submitting a request.
              </p>
              <span class="close-btn">x</span>
            </span>
          </span>
        </label>
            <div class="input-group">
                <textarea cols="50" rows="5" class="form-control" name="user_specs" aria-label="user-specs"><?php echo htmlspecialchars($job['specifications']); ?></textarea>
            </div>
            <div class="invalid-feedback">
            Please provide laser cutting specifications
            </div>
      </div>

        <div>
        <hr class="mb-4">
          <div class="col-md-3 mb-3">
            <label for="supports">Copies</label>
            <input type="number" class="form-control" name="laser_copies" min="1" max="100" step="1" id="supports" value="<?php echo htmlspecialchars($job['copies']); ?>" required />
            <div class="invalid-feedback">
              Please provide a valid response.
            </div>
          </div>
        </div>

        <hr class="mb-4">
        <!--change link to a future laser cut FAQ page-->
        <h3 class="mb-2">Laser Cut Material Type</h3>
        <div class="d-block my-3">
          <div class="custom-control custom-radio">
            <input id="plywood_3mm" name="laser_material_type" value="Plywood 3mm" type="radio" class="custom-control-input" <?php echo ($job['material_type'] == 'Plywood 3mm') ? 'checked' : ''; ?> required>
            <label class="custom-control-label" for="plywood_3mm">Plywood 3mm</label>
              <span class="popup">
                &#9432
                <span class="popuptext" id="myPopup">
                  <p><b>Plywood:</b> our cheapest material, in 3mm and 6mm thicknesses
                  </p>
                  <span class="close-btn">x</span>
                </span>
              </span>
          </div>
          <div class="custom-control custom-radio">
            <input id="plywood_6mm" name="laser_material_type" value="Plywood 6mm" type="radio" class="custom-control-input" <?php echo ($job['material_type'] == 'Plywood 6mm') ? 'checked' : ''; ?> required>
            <label class="custom-control-label" for="plywood_6mm">Plywood 6mm</label>
              <span class="popup">
                &#9432
                <span class="popuptext" id="myPopup">
                  <p><b>Plywood:</b> our cheapest material, in 3mm and 6mm thicknesses
                  </p>
                <span class="close-btn">x</span>
                </span>
              </span>
          </div>
          <div class="custom-control custom-radio">
            <input id="mdf_3mm" name="laser_material_type" value="MDF 3mm" type="radio" class="custom-control-input" <?php echo ($job['material_type'] == 'MDF 3mm') ? 'checked' : ''; ?> required>
            <label class="custom-control-label" for="mdf_3mm">MDF 3mm</label>
            <span class="popup">
              &#9432
              <span class="popuptext" id="myPopup">
                <p><b>MDF Wood (Medium-Density Fibreboard):</b> slightly denser and darker than plywood, available in 3mm and 6mm thicknesses.
                </p>
                <span class="close-btn">x</span>
              </span>
            </span>
          </div>
          <div class="custom-control custom-radio">
            <input id="mdf_6mm" name="laser_material_type" value="MDF 6mm" type="radio" class="custom-control-input" <?php echo ($job['material_type'] == 'MDF 6mm') ? 'checked' : ''; ?> required>
            <label class="custom-control-label" for="mdf_6mm">MDF 6mm</label>
            <span class="popup">
              &#9432
              <span class="popuptext" id="myPopup">
                <p><b>MDF Wood (Medium-Density Fibreboard):</b> slightly denser and darker than plywood, available in 3mm and 6mm thicknesses.
                </p>
                <span class="close-btn">x</span>
              </span>
            </span>
          </div>
          <div class="custom-control custom-radio">
            <input id="laser_cut_other" name="laser_material_type" value="Other" type="radio" class="custom-control-input" <?php echo ($job['material_type'] == 'Other') ? 'checked' : ''; ?> required>
            <label class="custom-control-label" for="laser_cut_other">Other
            </label>
            <span class="popup">
              &#9432
              <span class="popuptext" id="myPopup">
                <p><b>Higher-quality wood:</b> contact us for special requests.<br>
                    We can also engrave metals such as anodized aluminum, but you must provide the material yourself – email us at <a href="mailto:dscommons@uvic.ca">dscommons@uvic.ca</a> for more information.<br>
                    If no special settings are requested, we will use the recommended settings for the material.
                </p>
                <span class="close-btn">x</span>
              </span>
            </span>
            <small class="text-muted"> - Elaborate in Additional Comments section</small>
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
