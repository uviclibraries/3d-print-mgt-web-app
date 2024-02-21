<div class="py-5 text-center">
  <h1><?php echo " Job name: " . $job["job_name"];?></h1>
  <h2><?php echo "Customer: " . $job_owner["name"];?></h2>
</div> <!-- Job name and owner-->

<div class="col-md-12 order-md-1">
  <h4 class="mb-3">Submission Date</h4>
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
    <!--Job Purpose // academic vs. personal-->
    <div class="row">
      <div class="col-md-3 mb-3">
      <p><?php echo "Job purpose:<br>" .$job['job_purpose'];?></p>
    </div>
    <!--If Academic Purpose: course code-->
    <div class="col-md-3 mb-3">
      <p><?php 
        if ($job["job_purpose"] == "academic"){
          echo "Course Code:<br>" . $job['academic_code'];}
      ?></p>
    </div>
    <div class="col-md-3 mb-3">
      <p><?php 
        if ($job["job_purpose"] == "academic"){
          echo "Project deadline:<br>" . $job['course_due_date'];}
      ?></p>
    </div>
  </div>
</div> <!-- Submission date and purpose-->