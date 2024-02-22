<?php 

$duplicate_href ="";
switch($jobType){
  case ("3d print"):
    $duplicate_href = 'customer-duplicate-3d-job.php?job_id=';
    break;
  case("laser cut"):
    $duplicate_href = 'customer-duplicate-laser-job.php?job_id=';
    break;
  case("large format print"):
    $duplicate_href = 'customer-duplicate-large-format-job.php?job_id=';
    break;
}

$duplicate_href = $duplicate_href.$job["id"];
?>

<hr class="mb-4">
<center>
<!-- Button to trigger 'Duplicate Job' confirmation popup; button background color set to purple-->
<button id="duplicate-button" class="btn btn-primary btn-lg" style="background-color:#CF9FFF;">Duplicate Job</button> <!--duplicate button-->
<!-- The Duplicate Popup -->
<div id="DuplicateJobPopup" class="popup">
<div class="popup-content">
  <span class="close" data-popup="DuplicateJobPopup">&times;</span>
  <p>Are you sure you want to duplicate your job?</p>
    <a href=<?php echo $duplicate_href; ?>>
        <button class="btn btn-primary btn-lg" style="background-color:#CF9FFF;">Duplicate Job</button>
    </a>
</div>
</div>
</center>
<div class="col-md-12 order-md-1"></div>