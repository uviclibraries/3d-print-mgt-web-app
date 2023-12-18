<?php
session_start();
require ('auth-sec.php'); //Gets CAS & db
$stm = $conn->query("SELECT VERSION()");
#$version = $stm->fetch();
#echo $version;


$stm = $conn->prepare("SELECT * FROM web_job INNER JOIN 3d_print_job ON id=3d_print_id WHERE id=?");
$stm->execute([$_GET["job_id"]]);
$job=$stm->fetch();
// print_r(array_keys($job));


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
  $infill_bind = intval($_POST["infill"]);
  $scale_bind = intval($_POST["scale"]);
  $layer_bind = floatval(number_format((float)$_POST["layer_height"], 2, '.',''));
  $support_bind = intval($_POST["supports"]);
  $copies_bind = intval($_POST["copies"]);
  $laser_copies = intval($_POST["laser_copies"]);
  $good_statement = True;
  $stmt = $conn->prepare("INSERT INTO web_job (netlink_id, job_name, job_purpose, academic_code, submission_date, status) VALUES (:netlink_id, :job_name, :job_purpose, :academic_code, :submission_date, :job_status)");
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
  $stmt->bindParam(':user_netlink',  $job['netlink_id']);
  $good_statement &= $stmt->execute();
  $curr_id = $stmt->fetch(PDO::FETCH_NUM)[0];

  if(!$good_statement){
    die("Error during SQL execution");
  }

  if(!$curr_id){
    die('No web job entry for username {$user}');
  }

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

  

  $direct_link = "https://onlineacademiccommunity.uvic.ca/dsc/how-to-3d-print/";
  $msg = "
  <html>
  <head>
  <title>HTML email</title>
  </head>
  <body>
  <p>Hello, ".$user_name.". This is an automated message from the DSC.</p>
  <p>Thank you for submitting your 3D print request to the DSC at McPherson Library. We will evaluate the cost of the 3D print and you'll be notified by email when it is ready for payment. If you have any questions about the process or the status of your 3D print, please review our <a href=". $direct_link .">FAQ</a> or email us at DSCommons@uvic.ca.</p>
  </body>
  </html>";
  $headers = "MIME-Version: 1.0" . "\r\n";
  $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
  $headers .= "From: dscommons@uvic.ca" . "\r\n";
  mail($user_email,"DSC - New 3D print Job",$msg,$headers);

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
    <title>Duplicate 3D print Job</title>

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
        <h1>Duplicate 3D Print Job</h1>
      </div>

    <div class="col-md-12 order-md-1">

      <h3 class="mb-3">Job Name</h3>
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
          if (is_file(("uploads/" . $job['model_name']))) {
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

        <input type="file" id="myFile" name="3d_model" required>
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
    </script><!--showAcademicCodeText()-->

    <h3 class="mb-2">Job Purpose</h3>
    <!-- contains radio buttons and optional textbox to indicate if it's a personal or academic (and academic code) job-->
            <!--Academic Jobs-->
      <div class="row">
        <div class="col-md-3 mb-3">
          <div class="custom-control custom-radio">
            <input id="academic-purpose" name="job_purpose" value="academic" type="radio" class="custom-control-input" onclick="showAcademicCodeText(this)" required>
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
          <input type="text" class="form-control" name="academic_code" placeholder="Course code" autocomplete="off">
        </div>
        <div class="col-md-3 mb-3 w-100" id="academicdeadline_textbox" style="display:none;">
          <p>Assignment due date:  </p>
          <input type="date" class="form-control" name="academic_deadline" placeholder="Assignment Due Date" autocomplete="off">
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
          text-align: left;
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



    <div id="3d_specs" class="col-md-12 order-md-1">
      <h3 class="mb-3">3D Print Specifications</h3>
        <div class="row">
          <div class="col-md-3 mb-3">
            <label for="layer-height">Layer Height
              <span class="popup">
                &#9432
                <span class="popuptext" id="myPopup">
                  <p>3D printing is done by slicing the digital object horizontally into layers, then printing each layer one at a time. Layer height is the thickness of each slice.<br>
                  The smaller the layer height, the finer the detail of the finished print. Small layer heights take significantly longer to print because more layers are needed.<br>
                  Standard print jobs are printed at 0.2mm layer height and for most jobs, this is the best option. <br>
                  The Makerbot printers will print to as fine as 0.1mm and the Ultimaker 3 will print down to a 0.06mm layer height.</p>
                  <span class="close-btn">x</span>
                </span>
              </span>
            </label>
            <select class="custom-select d-block w-100" name="layer_height" id="layer-height" required>
              <option <?php echo ($job['supports'] == "0.2") ? 'selected' : ''; ?>>0.2</option>
              <option <?php echo ($job['supports'] == "0.15") ? 'selected' : ''; ?>>0.15</option>
              <option<?php echo ($job['supports'] == "0.1") ? 'selected' : ''; ?>>0.1</option>
              <option<?php echo ($job['supports'] == "0.06") ? 'selected' : ''; ?>>0.06</option>
            </select>
            <div class="invalid-feedback">
              Please select a valid layer height.
            </div>
          </div>
            <div class="col-md-3 mb-3">
                <label for="scale">Scale
                  <span class="popup">
                      &#9432
                      <span class="popuptext" id="myPopup">
                        <p>The scale is the size of the print by comparison to the size it was designed to be. Many models are printed at 100% scale, but it is also common to change the size.<br> 
                        Keep in mind: when decreasing model size, you will also be decreasing wall thickness and the size of detail. This can result in a loss of quality when printing, if taken too far.</p>
                        <span class="close-btn">x</span>
                      </span>
                    </span>
                </label>
                <div class="input-group">
                <div class="input-group mb-3">
                    <input type="number" min="1" class="form-control" name="scale" value="<?php echo htmlspecialchars($job['scale']); ?>" aria-label="100" aria-describedby="basic-addon2" required>
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
            <label for="infill">Infill
              <span class="popup">
                &#9432
                <span class="popuptext" id="myPopup">
                  <p>The infill is the percentage of the inside of the print that is filled with material.<br>
                  A higher infill makes the object sturdy and able to bear more weight, but can significantly increase the cost.<br>
                  Most decorative items can have 5-10% infill.<br>
                  Having no infill is not advised, unless the object was specifically designed to be printed with zero infill.<br>
                  Increasing the size will also increase the amount of material used, increasing the cost. <br>
                  In the image above, you’ll see the number of shells each print has. The number of shells or shell thickness is how thick the outside layer is. A thicker shell increase strength but also increase cost. Thicker shells can also be useful for post processing, as sanding or refining a print can remove shells and extra shells prevent ruining the print.<br>
                  For additional shells please note this in the additional comment section of your print or jobs will be printed with default shell thickness.</p>
                  <span class="close-btn">x</span>
                </span>
              </span>
            </label>

                <div class="input-group">
                  <div class="input-group mb-3">
                    <input type="number" max="100" min="0" class="form-control" name="infill" value="<?php echo htmlspecialchars($job['infill']); ?>" aria-label="100" aria-describedby="basic-addon2" required>
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
            <label for="supports">Supports
              <span class="popup">
              &#9432
                <span class="popuptext" id="myPopup">
                  <p>It’s generally best to have Supports turned on.<br>
                  The printing software we use will only generate supports where needed. We offer two option for support materials; regular PLA supports, which break off after printing but can leave aesthetic marks and may require sanding to get rid off.<br>
                  The alternative is water soluble PVA supports, which allow supports to be built in places that PLA supports would be difficult or impossible to remove.<br>
                  PVA supports also dont leave aesthetic marks, creating a nicer initial finish. However, PVA costs more at 20 cents per gram and increases printing times.</p>
                  <span class="close-btn">x</span>
                </span>
              </span>
            </label>
            <select class="custom-select d-block w-100" name="supports" id="supports" required>
              <option value="1" <?php echo ($job['supports'] == "1") ? 'selected' : ''; ?>>Yes</option>
              <option value="0"<?php echo ($job['supports'] == "0") ? 'selected' : ''; ?>>No</option>
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
            <input type="number" class="form-control" name="copies" min="1" max="100" step="1" id="supports" value = "<?php echo htmlspecialchars($job['copies']); ?>" required />

            <div class="invalid-feedback">
              Please provide a valid response.
            </div>
          </div>
        </div>

        <hr class="mb-4">
        <h3 class="mb-2">3D Print Material Type</h3>
        <div class="d-block my-3">
          <div class="custom-control custom-radio">
            <input id="pla" name="print_material_type" value="PLA" type="radio" class="custom-control-input" <?php echo ($job['material_type'] == 'PLA') ? 'checked' : ''; ?> required>
            <label class="custom-control-label" for="pla">PLA 
            </label>
              <span class="popup">
                &#9432
                <span class="popuptext" id="myPopup">
                  <p>
                  PLA (Polylactic Acid): 
                  <ul>
                    <li>A form of plastic made from natural starches like cornstarch that will biodegrade in some industrial composting facilities.</li>
                    <li>We charge 10 cents per gram.</li>
                  </ul>
                  </p>
                  <span class="close-btn">x</span>
                </span>
              </span>
          </div>
          <div class="custom-control custom-radio">
            <input id="pla-pva" name="print_material_type" value="PLA + PVA" type="radio" class="custom-control-input" <?php echo ($job['material_type'] == 'PLA + PVA') ? 'checked' : ''; ?> required>
            <label class="custom-control-label" for="pla-pva">PLA + PVA
            </label>
            <span class="popup">
              &#9432
              <span class="popuptext" id="myPopup">
                <p>
                PVA Dissolving Filament:
                <ul>
                  <li>Also made from starch.</li>
                  <li>It dissolves when you put it in water.</li>
                  <li>Used for supports.</li>
                  <li>We charge 20 cents per gram.</li>
                </ul>
                </p>
                <span class="close-btn">x</span>
              </span>
            </span>
          </div>
          <div class="custom-control custom-radio">
            <input id="petg" name="print_material_type" value="PETG" type="radio" class="custom-control-input" required>
            <label class="custom-control-label" for="petg">PETG</label>
            <span class="popup">
              &#9432
              <span class="popuptext" id="myPopup">
                <p>
                PETG:
                <ul>
                  <li>This is a material similar to PLA, benefits of PETG include increased heat resistance.</li>
                  <li>Not great for projects with a lot of details since this material tends to be stringy.</li>
                  <li>Currently only available in red.</li>
                  <li>We charge 10 cents per gram.</li>
                </ul>
                </p>
                <span class="close-btn">x</span>
              </span>
            </span>
          </div>
          <div class="custom-control custom-radio">
            <input id="tpu95" name="print_material_type" value="TPU95" type="radio" class="custom-control-input" <?php echo ($job['material_type'] == 'TPU95') ? 'checked' : ''; ?> required>
            <label class="custom-control-label" for="tpu95">TPU95</label>
            <span class="popup">
              &#9432
              <span class="popuptext" id="myPopup">
                <p>
                TPU 95a:
                <ul>
                  <li>A material with medium flex</li>
                  <li>We charge 20 cents per gram</li>
                </ul>
                </p>
                <span class="close-btn">x</span>
              </span>
            </span>
          </div>
          <div class="custom-control custom-radio">
            <input id="other" name="print_material_type" value="Other" type="radio" class="custom-control-input" <?php echo ($job['material_type'] == 'Other') ? 'checked' : ''; ?> required>
            <label class="custom-control-label" for="other">Other</label>
              <span class="popup">
              &#9432
              <span class="popuptext" id="myPopup">
                <p>
                Q) How durable is the material?<br>
                  A) PLA is brittle so if you put any load on something printed with PLA plastic always use protective eyewear to protect against the piece shattering into tiny shards.<br>
                  Higher infill density as well and number of shells will increase the strength of a piece, but it also increases cost since it makes a printed object weigh more.<br>
                  <a href="https://onlineacademiccommunity.uvic.ca/dsc/how-to-3d-print/#printers">Click here to see</a> Printer Information and Filaments Used.<br><br>

                  About Colors:<br>
                  We have different coloured filament loaded into the printers at a given time. When you email your print job, you can ask what colours are currently loaded in the printer. If your job specifically requires a certain colour, email and ask if it is possible.<br>
                  Two-colour printing is possible with the DSC’s Ultimaker 3. Here are some things to note while creating your object:<br>
                  <ul>
                    <li>To complete a print, projects need to be exported in either a .’obj’ file (TinkerCad and other software such as SolidWorks) or ‘.amf’ file (SolidWorks).</li>
                    <li>Before exporting your complete .obj file, make sure to group objects of the same colour together and export each as a different file. These will fit together for printing to complete your two colour creation.</li>
                    <li>Make sure to communicate your colour preferences when requesting a print job; to make sure that your colour choices are available at the DSC.</li>
                    <li>Note: Dissolving PVA Supports are not available with two colour print jobs.</li>
                  </ul>
                </p>
                <span class="close-btn">x</span>
              </span>
              </span>
              <small class="text-muted"> - Elaborate in Additional Comments section</small>
          </div>
        </div>
    </div>

    <!-- script for first block -->

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
