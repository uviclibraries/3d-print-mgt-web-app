<?php
session_start();
require ('auth-sec.php'); //Gets CAS & db
//auth-sec includes: $user, $user_email, $user_type, $user_name
//Is user Admin check
if ($user_type == 1) {
  header("Location: customer-dashboard.php");
  die();
}

$sql_line =array(); //sql builder //parameters
$getcheck = array_fill(0,3, FALSE); //where conditions
// echo $_GET['searchdate_start'];
//startdate_search is set onload to 10 days prior to the current date, as set in admin-dashboard.php
//if the start date is set, append to filter line
if (isset($_GET['searchdate_start']) && ($_GET['searchdate_start'] != "" && $_GET['searchdate_start'] != NULL)) {
  $getcheck[0] = True;
  $sql_line[] = "(web_job.submission_date >= :searchdate_start OR web_job.delivered_date >= :searchdate_start OR web_job.cancelled_date >= :searchdate_start OR web_job.completed_date >= :searchdate_start)";
}
//if the end date is set, append to filter line
if (isset($_GET['searchdate_end']) && ($_GET['searchdate_end'] != "" && $_GET['searchdate_end'] != NULL)) {
  $getcheck[1] = True;
  $sql_line[] = "(web_job.submission_date <= :searchdate_end OR web_job.delivered_date <= :searchdate_end OR web_job.cancelled_date <= :searchdate_end OR web_job.completed_date <= :searchdate_end)";
}

//if the search name is set, append to filter line
if (isset($_GET['search_id']) && ($_GET['search_id'] != "" && $_GET['search_id'] != NULL)) {
  $getcheck[2] = True;
  $sql_line[] = "(web_job.netlink_id LIKE :search_id OR users.name LIKE :search_id)";
}


//3D PRINT JOBS
//execute query if parameters are empty 
// if ($getcheck[0]==FALSE && $getcheck[1]==FALSE && $getcheck[2]==FALSE && $getcheck[3]==FALSE) {
if (!array_filter($getcheck)){
  $stm = $conn->query("SELECT web_job.id, web_job.job_name, web_job.netlink_id, web_job.job_purpose, web_job.status, web_job.price, web_job.completed_date, web_job.delivered_date, web_job.submission_date, web_job.cancelled_date, web_job.archived_date,users.name, 3d_print_job.duration, 3d_print_job.material_type FROM web_job INNER JOIN `3d_print_job` ON web_job.id=`3d_print_job`.`3d_print_id` INNER JOIN users ON web_job.netlink_id=`users`.`netlink_id` WHERE (web_job.status IN ('delivered', 'archived', 'cancelled')) ORDER BY web_job.completed_date DESC");

}

//find out what parameters are being searched for
else{
  //build sql query line based on search parameters
  $searchline = "SELECT web_job.id, web_job.job_name, web_job.netlink_id, web_job.job_purpose, web_job.status, web_job.price, web_job.completed_date, web_job.delivered_date, web_job.submission_date, web_job.cancelled_date, web_job.archived_date, users.name, 3d_print_job.duration, 3d_print_job.material_type FROM web_job INNER JOIN `3d_print_job` ON web_job.id=`3d_print_job`.`3d_print_id` INNER JOIN users ON web_job.netlink_id=`users`.`netlink_id` WHERE (web_job.status IN ('delivered', 'archived', 'cancelled')) AND " . implode(" AND ", $sql_line) . " ORDER BY web_job.completed_date DESC";
  $stm = $conn->prepare($searchline);
  //echo $searchline . "\n";

  //Bind search parameters
  if ($getcheck[0] == TRUE) {
    $stm->bindParam(':searchdate_start', $_GET['searchdate_start'], PDO::PARAM_STR);
  }if ($getcheck[1] == TRUE) {
    $stm->bindParam(':searchdate_end', $_GET['searchdate_end'], PDO::PARAM_STR);
  }if ($getcheck[2] == TRUE) {
    $temp = $_GET['search_id']."%";
    $stm->bindParam(':search_id', $temp, PDO::PARAM_STR);
  }
  
  
  $stm->execute();
  // print_r($stm);
}
//SQL results
$d_history = $stm->fetchAll();

//Get duration (in minutes) of all delivered, archived, or cancelled 3D Print jobs, as filtered by user
$d_duration = 0;
foreach ($d_history as $row) {
  $d_duration += $row["duration"];
}

//LASER CUT JOBS
//Check if parameters are empty 
// if ($getcheck[0]==FALSE && $getcheck[1]==FALSE && $getcheck[2]==FALSE && $getcheck[3]==FALSE) {
if (!array_filter($getcheck)){
  $stm = $conn->query("SELECT web_job.id, web_job.job_name, web_job.netlink_id, web_job.job_purpose, web_job.status, web_job.price, web_job.completed_date, web_job.delivered_date, web_job.submission_date, web_job.cancelled_date, web_job.archived_date, users.name, laser_cut_job.duration, laser_cut_job.material_type FROM web_job INNER JOIN `laser_cut_job` ON web_job.id=`laser_cut_job`.`laser_cut_id` INNER JOIN users ON web_job.netlink_id=`users`.`netlink_id` WHERE web_job.status IN ('delivered', 'archived', 'cancelled') ORDER BY web_job.completed_date DESC");
}
//find out what parameters are being searched for
else{
  //build sql query line based on search parameters
  $searchline = "SELECT web_job.id, web_job.job_name, web_job.netlink_id, web_job.job_purpose, web_job.status, web_job.price, web_job.completed_date, web_job.delivered_date, web_job.submission_date, web_job.cancelled_date,web_job.archived_date,users.name, laser_cut_job.duration, laser_cut_job.material_type FROM web_job INNER JOIN `laser_cut_job` ON web_job.id=`laser_cut_job`.`laser_cut_id` INNER JOIN users ON web_job.netlink_id=`users`.`netlink_id` WHERE (web_job.status IN ('delivered', 'archived', 'cancelled')) AND " . implode(" AND ", $sql_line) . " ORDER BY web_job.completed_date DESC";
  $stm = $conn->prepare($searchline);
  //echo $searchline . "\n";

  //Bind search parameters
  if ($getcheck[0] == TRUE) {
    $stm->bindParam(':searchdate_start', $_GET['searchdate_start'], PDO::PARAM_STR);
  }if ($getcheck[1] == TRUE) {
    $stm->bindParam(':searchdate_end', $_GET['searchdate_end'], PDO::PARAM_STR);
  }if ($getcheck[2] == TRUE) {
    $temp = $_GET['search_id']."%";
    $stm->bindParam(':search_id', $temp, PDO::PARAM_STR);
  }

  $stm->execute();

}
//SQL results
$l_history = $stm->fetchAll();

//Get duration (in minutes) of all delivered, archived, or cancelled Laser cut jobs, as filtered by user
$l_duration = 0;
foreach ($l_history as $row) {
  $l_duration += $row["duration"];
}

//LARGE FORMAT PRINT JOBS
//execute query if parameters are empty 
// if ($getcheck[0]==FALSE && $getcheck[1]==FALSE && $getcheck[2]==FALSE && $getcheck[3]==FALSE) {
if (!array_filter($getcheck)){
  $stm = $conn->query("SELECT web_job.id, web_job.job_name, web_job.netlink_id, web_job.job_purpose, web_job.status, web_job.price, web_job.completed_date, web_job.delivered_date, web_job.submission_date, web_job.cancelled_date, web_job.archived_date,users.name FROM web_job INNER JOIN `large_format_print_job` ON web_job.id=`large_format_print_job`.`large_format_print_id` INNER JOIN users ON web_job.netlink_id=`users`.`netlink_id` WHERE (web_job.status IN ('delivered', 'archived', 'cancelled')) ORDER BY web_job.completed_date DESC");

}

//find out what parameters are being searched for
else{
  //build sql query line based on search parameters
  $searchline = "SELECT web_job.id, web_job.job_name, web_job.netlink_id, web_job.job_purpose, web_job.status, web_job.price, web_job.completed_date, web_job.delivered_date, web_job.submission_date, web_job.cancelled_date,web_job.archived_date, users.name FROM web_job INNER JOIN `large_format_print_job` ON web_job.id=`large_format_print_job`.`large_format_print_id` INNER JOIN users ON web_job.netlink_id=`users`.`netlink_id` WHERE (web_job.status IN ('delivered', 'archived', 'cancelled')) AND " . implode(" AND ", $sql_line) . " ORDER BY web_job.completed_date DESC";
  $stm = $conn->prepare($searchline);
  //echo $searchline . "\n";

  //Bind search parameters
  if ($getcheck[0] == TRUE) {
    $stm->bindParam(':searchdate_start', $_GET['searchdate_start'], PDO::PARAM_STR);
  }if ($getcheck[1] == TRUE) {
    $stm->bindParam(':searchdate_end', $_GET['searchdate_end'], PDO::PARAM_STR);
  }if ($getcheck[2] == TRUE) {
    $temp = $_GET['search_id']."%";
    $stm->bindParam(':search_id', $temp, PDO::PARAM_STR);
  }
  
  
  $stm->execute();
  // print_r($stm);
  }
//SQL results
$lf_history = $stm->fetchAll();



$get_line = array();
//Seach button clicked
if(isset($_POST["Search"])){
// if($_SERVER['REQUEST_METHOD'] === 'POST'){
  if (isset($_POST["searchdate_start"])) {
    $get_line[] = "searchdate_start=" . $_POST["searchdate_start"];
  }
  if (isset($_POST["searchdate_end"])) {
    $get_line[] = "searchdate_end=" . $_POST["searchdate_end"];
  }
  if (isset($_POST["search_id"])) {
    $get_line[] = "search_id=" . $_POST["search_id"];
  }
  header("Location: admin-print-history.php?". implode("&", $get_line));
}


//3D Print Jobs REPORT
//If clicked download CSV (input name="get3DCSV")
if (isset($_POST["get3DCSV"])) {
  $filename = "3d-job-history.csv";
  header("Content-Type: text/csv; charset=utf-8;");
  header("Content-Disposition: attachment; filename=".$filename);


  $fp = fopen("php://output", "w");
  $d_column = array("Job_ID", "Job_Name", "Netlink_ID", "Job_Purpose", "Status", "Price", "Duration", "Material_Type", "Delivered_Date","Cancelled_Date", "Archived_Date",);
  fputcsv($fp, $d_column);
  foreach ($d_history as $row) {
    $d_reportColumns = [
        'Job_ID' => $row['id'],
        'Job_Name' => $row['job_name'],
        'Netlink_ID' => $row['netlink_id'],
        'Job_Purpose' => $row['job_purpose'],
        'Status' => $row['status'],
        'Price' => $row['price'],
        'Duration' => $row['duration'],
        'Material_Type' => $row['material_type'],
        'Delivered_Date' => $row['delivered_date'],
        'Cancelled_Date' => $row['cancelled_date'],
        'Archived_Date' => $row['archived_date'],
    ];
    fputcsv($fp, $d_reportColumns);
  }
  fclose($fp);
  exit();
}

//Laser Cut Jobs REPORT
//If clicked download CSV (input name="getLaserCSV")
if (isset($_POST["getLaserCSV"])) {
  
  $filename = "laser-cut-job-history.csv";
  header("Content-Type: text/csv; charset=utf-8;");
  header("Content-Disposition: attachment; filename=".$filename);

  $fp = fopen("php://output", "w");
  $lc_column = array("Job_ID", "Job_Name", "Netlink_ID", "Job_Purpose", "Status", "Price", "Duration", "Material_Type", "Delivered_Date","Cancelled_Date", "Archived_Date",);
  fputcsv($fp, $lc_column);
  foreach ($l_history as $row) {
    $lc_reportColumns = [
        'Job_ID' => $row['id'],
        'Job_Name' => $row['job_name'],
        'Netlink_ID' => $row['netlink_id'],
        'Job_Purpose' => $row['job_purpose'],
        'Status' => $row['status'],
        'Price' => $row['price'],
        'Duration' => $row['duration'],
        'Material_Type' => $row['material_type'],
        'Delivered_Date' => $row['delivered_date'],
        'Cancelled_Date' => $row['cancelled_date'],
        'Archived_Date' => $row['archived_date'],
      ];
    fputcsv($fp, $lc_reportColumns);
  }
  fclose($fp);
  exit();
}

//Large Format Print Jobs REPORT
//If clicked download CSV (input name="getLargeFormatCSV")
if (isset($_POST["getLargeFormatCSV"])) {

  $filename = "large-format-print-job-history.csv";
  header("Content-Type: text/csv; charset=utf-8;");
  header("Content-Disposition: attachment; filename=".$filename);


  $fp = fopen("php://output", "w");
  $lf_column = array("Job_ID", "Job_Name", "Netlink_ID", "Job_Purpose", "Status", "Price", "Delivered_Date","Cancelled_Date", "Archived_Date",);
  fputcsv($fp, $lf_column);
  foreach ($lf_history as $row) {
    $lf_reportColumns = [
        'Job_ID' => $row['id'],
        'Job_Name' => $row['job_name'],
        'Netlink_ID' => $row['netlink_id'],
        'Job_Purpose' => $row['job_purpose'],
        'Status' => $row['status'],
        'Price' => $row['price'],
        'Delivered_Date' => $row['delivered_date'],
        'Cancelled_Date' => $row['cancelled_date'],
        'Archived_Date' => $row['archived_date'],    
      ];
    fputcsv($fp, $lf_reportColumns);
  }
  fclose($fp);
  exit();
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
    <title>Admin print history</title>

    <!--header link-->
    <link rel="stylesheet" href="css/uvic_banner.css">
    <link rel="stylesheet" href="css/accordion_styles.css">
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
  </head>

  <body class="bg-light">
    <div id="custom_header">
      <div class="wrapper" style="min-height: 6em;" id="banner">
        <div style="position:absolute; left: 5px; top: 26px;">
          <a href="http://www.uvic.ca/" id="logo"><span>University of Victoria</span></a>
        </div>
        <div style="position:absolute; left: 176px; top: 26px;">
          <a href="http://www.uvic.ca/library/" id="unit"><span>Libraries</span></a>
        </div>
        <div class="edge" style="position:absolute; margin: 0px;right: 0px; top: 0px; height: 96px; width:200px;">&nbsp;</div>
      </div><!--Header end-->

    <div class="text-center">
      <h1>Print History</h1>
    </div>

    <div class="row">
      <div class="container">
        <div class="py-5 px-5 text-left">
          <h3 id="topOfPage">Delivered, Archived &amp; Cancelled Jobs</h3>
           <br>
          <!--Search bar-->
          <div class="row">
          <form method="POST">
            <div class="row">
              <div class="mb-2">
                <label for = "searchdate_start">Start date:</label>
                <input type="date" id= "searchdate_start" name="searchdate_start" style='width:150px;' value="<?php echo isset($_GET['searchdate_start']) ? $_GET['searchdate_start'] : $_POST['searchdate_start']; ?>">
              </div>
              <div class="mb-2">
                <label for = "searchdate_end">End date:</label>
                <input type="date" id= "searchdate_end" name="searchdate_end" style='width:150px;' value="<?php echo isset($_GET['searchdate_end']) ? $_GET['searchdate_end'] : $_POST['searchdate_end']; ?>">
              </div>
            </div>
            <div class="row">
              <div class="mb-2">
                <label for = "search_id">Name or netlink id:</label>
                  <input type="text" id="search_id" name="search_id" style='width:250px;' value="<?php echo isset($_GET['search_id']) ? $_GET['search_id'] : '';?>">
              </div>
            </div>
            <input type="submit" name="Search" value="Search">
          </div>

          <hr class="mb-6">

          <div class="row">
            <div class="col-md-4 mb-3">
              <input type="submit" name="get3DCSV" value="Download 3D Print CSV" class="btn btn-warning">
            </div>
            <div class="col-md-4 mb-3">
              <input type="submit" name="getLaserCSV" value="Download Laser Cut CSV" class="btn btn-warning">
            </div>
            <div class="col-md-4 mb-3">
              <input type="submit" name="getLargeFormatCSV" value="Download Large Format Print CSV" class="btn btn-warning">
            </div>
          </div>
          </form>
        <div class="col-md-4 offset-md-4">
          <a class="btn btn-md btn-primary btn-" href="admin-dashboard.php" role="button">Back to Dashboard</a>
        </div>   


        <?php 
          function displayJobName($row) {
            $no_link_statues = array("cancelled", "archived", "delivered");
            $display_name = "";

            if (!in_array($row['status'], $no_link_statues)) {
              if ($row['parent_job_id'] > 0) {
                $display_name = '&copy ';
                // echo $display_name;
              } elseif ($row['is_parent']) {
                $display_name = '&#9413 ';
                // echo $display_name;
              }
            } 
            $display_name = $display_name . $row["job_name"];
              // echo $display_name;
            return $display_name;
          }

          $d_href='admin-3d-job-specification.php?job_id=';
          $l_href= 'admin-laser-job-specification.php?job_id=';
          $lf_href='admin-large-format-print-job-specification.php?job_id=';

          function generateTable($table_id, $rel_jobs, $job_ref) {
              echo '  <div class="table-responsive" style="width: 100%; table-layout: fixed;">';
              echo '    <table id="' . $table_id . '" class="table table-striped table-md">';
              echo '      <thead>';
              echo '        <tr>';
              echo '          <th style="width:15%;">Job id</th>';
              echo '          <th style="width:15%;">Username</th>';
              echo '          <th style="width:50%;">Job Name</th>';
              echo '          <th style="width:20%;">Date</th>';
              echo '          <th style="width:15%;">Status</th>';
              echo '        </tr>';
              echo '      </thead>';
              echo '      <tbody>';

              foreach ($rel_jobs as $row) {
                $date = $row['status'] == 'cancelled' ? $row['cancelled_date'] : $row['delivered_date'];
                  echo '        <tr>';
                  echo '          <td style="width:15%;">' . $row["id"] . '</td>';
                  echo '          <td style="width:15%;">' . $row["name"] . '</td>';
                  echo '          <td style="width:50%;"><a href="' .$job_ref. $row["id"] . '">' . displayJobName($row) . '</a></td>';
                  echo '          <td style="width:20%;">' . $date. '</td>';
                  echo '          <td style="width:15%;">' . $row["status"] . '</td>';
                  echo '        </tr>';
              }

              echo '      </tbody>';
              echo '    </table>';
              echo '  </div>';
          }
          ?>
        <hr class="mb-12">

        <div style="text-align: right;">
          <p id = "3dSection"><a href="#laserSection">(Jump to Laser Cut jobs)</a><br><a href="#largeFormatSection">(Jump to Large Format Print jobs)</a></p>
        </div>
        
        <button class="accordion active">3D Print Jobs</button>
        
        <div class="panel" style="display:block;">
          <div style="text-align: right;">
            <p><b><?php echo "Total print duration of jobs displayed (minutes): ". $d_duration; ?></b></p>
          </div>
          <div class="py-3"></div>
              <?php generateTable('d_history', $d_history, $d_href); ?>
        </div><!--End of 3D jobs-->

        <hr class="mb-12">


        <div style="text-align: right;">
          <p id = "laserSection"><a href="#3dSection">(Jump to 3D Print jobs)</a><br><a href="#largeFormatSection">(Jump to Large Format Print jobs)</a></p>
        </div>
        
        <button class="accordion active">Laser Cut Jobs</button>
        
        <div class="panel" style="display:block;">
          <div style="text-align: right;">
            <p><b><?php echo "Total cut duration of jobs displayed (minutes): ". $l_duration; ?></b></p>
          </div>  
          <div class="py-3"></div>
          <?php generateTable('l_history', $l_history, $l_href); ?>
        </div><!--End of laser jobs-->
        
        <hr class="mb-12">


    <!--LARGE FORMAT PRINT JOBS-->
        <div style="text-align: right;">
          <p id="largeFormatSection"><a href="#3dSection">(Jump to 3D Print jobs)</a><br><a href="#laserSection">(Jump to Laser Cut jobs)</a></p>
        </div>
  
        <button class="accordion active">Large Format Print Jobs</button>
        
        <div class="panel" style="display:block;">
          <div class="py-3"></div>
          <?php generateTable('lf_history', $lf_history, $lf_href); ?>
        </div>


        <div>
          <p><a href="#topOfPage">(Jump to Top)</a></p>
        </div>

        <hr class="mb-12">

  <!--<a class="btn btn-md btn-block" href="login.php" role="button">Log Out</a>
  -->
        </div>
      </div>
    </div>
  <div class="text-center">
    <a class="btn btn-md btn-primary btn-lg py-5" href="admin-dashboard.php" role="button">Back to Dashboard</a>
  </div>
<script>

function sortTable(tableId) {
  var table, rows, switching, i, shouldSwitch;
  table = document.getElementById(tableId);
  switching = true;

  // Convert rows to an array
  rows = Array.from(table.rows).slice(1); // skip the header row

  // sort by job purpose then by date
  rows.sort(function(rowA, rowB) {
    var statusOrder = { 'delivered': 1, 'archived': 2, 'cancelled': 3 };

    var col4A = rowA.cells[4].textContent.toLowerCase(); // Column status
    var col4B = rowB.cells[4].textContent.toLowerCase();
    var statusA = statusOrder[col4A] || statusOrder['null']; 
    var statusB = statusOrder[col4B] || statusOrder['null']; 

    if (statusA < statusB) return -1;
    if (statusA > statusB) return 1;
    var col3A = rowA.cells[3].textContent; // Column date
    var col3B = rowB.cells[3].textContent;
    var dateA = new Date(col3A);
    var dateB = new Date(col3B);

    return dateB - dateA;
    
  });

  // Re-adding the sorted rows to the table
  for (i = 0; i < rows.length; i++) {
    table.appendChild(rows[i]);
  }
}

function sortAllTablesPurpose() {
  var tables_to_sort=['d_history','l_history','lf_history'];


  tables_to_sort.forEach(sortTable);
  
}
window.onload = sortAllTablesPurpose();
</script>
<script>
  var acc = document.getElementsByClassName("accordion");
  var i;

  for (i = 0; i < acc.length; i++) {
    acc[i].addEventListener("click", function() {
      this.classList.toggle("active");
      var panel = this.nextElementSibling;
      if (panel.style.display === "block") {
        panel.style.display = "none";
      } else {
        panel.style.display = "block";
      }
    });
  }
</script>
  

</body>
</html>
