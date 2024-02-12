<?php
session_start();
require ('auth-sec.php'); //Gets CAS & db
//auth-sec includes: $user, $user_email, $user_type, $user_name
//Is user Admin check
if ($user_type == 1) {
  header("Location: customer-dashboard.php");
  die();
}

// testing job name: "test 3d print type"

$job_id = 2516;

$stm = $conn->prepare("SELECT * FROM web_job INNER JOIN 3d_print_job ON id=3d_print_id WHERE id=$job_id");
$stm->execute();
$job=$stm->fetch();
echo($job['parent_job_id']);

//Get users name & email
$userSQL = $conn->prepare("SELECT * FROM users WHERE netlink_id = :netlink_id");
$userSQL->bindParam(':netlink_id', $job['netlink_id']);
$userSQL->execute();
$job_owner = $userSQL->fetch();

//get list of active jobs associated with the job's owner
$stm = $conn->prepare("SELECT web_job.id AS id, web_job.job_name AS name, web_job.status AS status, web_job.submission_date AS submission_date, web_job.priced_date AS priced_date, web_job.paid_date AS paid_date,web_job.printing_date AS printing_date,web_job.completed_date AS completed_date,web_job.delivered_date AS delivered_date,web_job.hold_date AS hold_date,web_job.hold_signer AS hold_signer,web_job.cancelled_signer AS cancelled_signer,  web_job.priced_signer AS priced_signer, web_job.paid_signer AS paid_signer, web_job.printing_signer AS printing_signer, web_job.completed_signer AS completed_signer, web_job.delivered_signer AS delivered_signer, web_job.job_purpose AS job_purpose, web_job.academic_code AS academic_code, web_job.course_due_date AS course_due_date, 3d_print_job.duration AS duration, web_job.parent_job_id AS parent_job_id FROM web_job INNER JOIN 3d_print_job ON web_job.id=3d_print_job.3d_print_id WHERE web_job.status NOT IN ('delivered', 'archived', 'cancelled') AND web_job.netlink_id = :netlink_id");

  $stm->bindParam(':netlink_id', $job['netlink_id']);
  $stm->execute();
  $user_web_jobs = $stm->fetchAll();

  $active_user_jobs = [];
  foreach ($user_web_jobs as $related_job) {
    $active_user_jobs[] = $related_job;
  }

  $linked_user_jobs = [];
  foreach ($user_web_jobs as $related_job) {
    if($job['id'] != $related_job['id'] && ($related_job['parent_job_id'] == $job['id'] || $related_job['id'] == $job['parent_job_id'])){
      $linked_user_jobs[] = $related_job;
    }
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {


  // change to source from web job and 3d_print_job
  $test_parent = 2530;
  $test_notes = "testing notes";
  $stmt = $conn->prepare("UPDATE web_job.parent_job_id=:parent_job_id, web_job.staff_notes = :staff_notes WHERE web_job.id = :job_id;");
  $stmt->bindParam(':parent_job_id', $test_parent);
  $stmt->bindParam(':staff_notes', $test_notes);
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
/*STYLES FOR USER ACTIVE JOBS STATUS TABS*/

body {font-family: Arial;}
/* Style for each checkbox */
      .job-checkbox {
        display: inline-block;
        margin: 3px;
      }

      .job-item {
        flex: 1 1 calc(33.333% - 10px); 
        box-sizing: border-box;
      }
      
/* Style the tab */
.tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
  font-size: 17px;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}

</style>

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

  <!--Basic information about job linking-->
  <p style="color:green;"> IF THIS IS A CHILD JOB...</p>
  <p>This job is priced under Job id <span style="color:blue;"><?php$job['parent_id']?></span>   <button onclick="unlinkJob()">Unlink this job</button></p>

  <p style="color:green;">OR IF THIS IS THE PARENT JOB...</p>
  <p>See other jobs in bundle below</p>


  <p style="color:green;">OR IF THIS IS AN UNLINKED JOB...</p>

  <!--ALL--> 
  <!--test update parent to 2530-->
  <p><br>Assign new parent </label> <input type="number" name="newParent" placeholder="parent ID"><label for="newParent"> <span style="color:orange;">"Save" job to refresh linked jobs.</span></p>

  <h2>Other User Jobs</h2>
  <p>Select the different tabs to see jobs with those jobs</p>


  <!--Status tabs for linked/ active user jobs-->
  <div class="tab">
      <button class="tablinks" onclick="openStatus(event, 'Linked') " id="linked_tab">Linked</button>
      <button class="tablinks" onclick="openStatus(event, 'NotPriced') " id="notpriced_tab">Not Priced</button>
      <button class="tablinks" onclick="openStatus(event, 'PendingPayment')" id="pending_payment_tab">Pending Payment</button>
    	<button class="tablinks" onclick="openStatus(event, 'Paid')" id="paid_tab">Paid</button>
      <button class="tablinks" onclick="openStatus(event, 'InProgress')" id="in_progress_tab">In Progress</button>
      <button class="tablinks" onclick="openStatus(event, 'Completed')" id="completed_tab">Completed</button>
     <button class="tablinks" onclick="openStatus(event, 'OnHold')" id="on_hold_tab">On Hold</button>
  </div>


  

  <div id="NotPriced" class="tabcontent">
    <h3>Not Priced</h3>
    <p>These are the user’s jobs that have been submitted and not processed further.</p> 
  </div>

  <div id="PendingPayment" class="tabcontent">
    <h3>Pending Payment</h3>
    <p>These are the user's jobs that have been priced and are awaiting payment by the customer.</p>
  </div>

  <div id="Paid" class="tabcontent">
    <h3>Paid</h3>
    <p>These are the jobs that have been paid for and not yet sent to the printer.</p>
  </div>

  <div id="InProgress" class="tabcontent">
    <h3>In Progress</h3>
    <p>These are the user's jobs that are currently on the printer.</p> 
  </div>

  <div id="Completed" class="tabcontent">
    <h3>Completed</h3>
    <p>These are the user's jobs that have been printed but not delivered to the front desk.</p>
  </div>

  <div id="OnHold" class="tabcontent">
    <h3>On Hold</h3>
    <p>These are the user's jobs that have been put on hold</p>
  </div>

  <button class="form-control" type="submit" data-inline="true">Save</button>
        

  </form>

  <script>
    function openStatus(evt, status) {
      var i, tabcontent, tablinks;
      tabcontent = document.getElementsByClassName("tabcontent");
      for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
      }
      tablinks = document.getElementsByClassName("tablinks");
      for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
      }
      document.getElementById(status).style.display = "block";
      evt.currentTarget.className += " active";
    }

    function unlinkJob(){
    //clear parent job id
    }

  //status_tab = <?php $job['parent']?> + '_tab';
  document.getElementById("linked_tab").click();
  if (<?php $job['parent']?> != '' ){
    document.getElementById("status_tab").click();
  }
  else{
    document.getElementById("linked_tab").click();
  }
  </script>
     

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script>window.jQuery || document.write('<script src="/docs/4.5/assets/js/vendor/jquery.slim.min.js"><\/script>')</script>
  <script src="/docs/4.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-1CmrxMRARb6aLqgBO7yyAxTOQE2AKb9GfXnEo760AUcUmFx3ibVJJAzGytlQcNXd" crossorigin="anonymous"></script>
  <script src="form-validation.js"></script>

</body>
</html> 
