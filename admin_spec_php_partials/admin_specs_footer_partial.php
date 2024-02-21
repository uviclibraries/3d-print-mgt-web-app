<hr class="mb-6">
<div class="col-md-12 order-md-1">

<h5 class="mb-2">Enable Email</h5>
<div class="d-block my-3">
  <div class="custom-control custom-checkbox">
    <input id="en_email" name="email_enabaled" value= "enabled" type="checkbox" class="custom-control-input" <?php if ($job["status"] != "pending payment" && $job["status"] != "delivered"){echo "checked";} ?>>
    <label class="custom-control-label" for="en_email">Send email for pending payment or delivered to desk when saved.</label>
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
    <a class="btn btn-primary btn-lg btn-block" href="admin-dashboard.php" role="button">Back to Dashboard</a>
  </div>
</div>
</form>
</div>

<?php 
$d_duplicate_href = $job_type == "3dPrint" ? 'customer-duplicate-3d-job.php?job_id='.$job["id"] : ($job_type == "laserCut" ? 'customer-duplicate-laser-job.php?job_id='.$job["id"] : 'customer-duplicate-large-format-job.php?job_id='.$job["id"]); ?>

<hr class="mb-4">
<center>
<!-- Button to trigger 'Duplicate Job' confirmation popup; button background color set to purple-->
<button id="duplicate-button" class="btn btn-primary btn-lg" style="background-color:#CF9FFF;">Duplicate Job</button> <!--duplicate button-->
<!-- The Duplicate Popup -->
<div id="DuplicateJobPopup" class="popup">
<div class="popup-content">
  <span class="close" data-popup="DuplicateJobPopup">&times;</span>
  <p>Are you sure you want to duplicate your job?</p>
    <a href=<?php echo $d_duplicate_href; ?>>
        <button class="btn btn-primary btn-lg" style="background-color:#CF9FFF;">Duplicate Job</button>
    </a>
</div>
</div>
</center>
<div class="col-md-12 order-md-1"></div>