<hr class="mb-6">
<div class="col-md-12 order-md-1">
  <h4 class="mb-3">Job Files</h4>
  <div class="row">
    <div class="col-md-4 mb-3">
      <h5 class="mb-3">Original File</h5>
        <?php
        if (is_file(("uploads/" . $job['model_name']))) {
            ?>
            <!--Grabs file and renames it to the job name when downloaded-->
            <a href="<?php echo "uploads/" . $job['model_name']; ?>" download="<?php
                $filetype = explode(".", $job['model_name']);
                echo $job['job_name'] . "." . $filetype[1]; ?>">
                Download File
            </a>
        <?php
        }
        else{ ?>
          <p>File Deleted</p>
        <?php } ?>
    </div>
        <br>

  <!--If in admin specs pages-->
  <?php if($userView == "admin"){?>
     <div class="col-md-8 mb-3">
      <h5 class="mb-3">Modified Model</h5>

    <?php //checks if there is a modify file
    if ($job['model_name_2'] != NULL && is_file(("uploads/" . $job['model_name_2']))) { ?>
      <a href="<?php echo "uploads/" . $job['model_name_2']; ?>" download>Download Modified File</a>
    <?php } else { echo 'No modified file has been saved';}?>
    <br/>
      <small class="text-muted">(Upload new modified file - max 200MB)</small>
      <input type="file" id="myFile" name="modify">
    <br>
  </div>
    <?php }?>

</div>
<?php if($jobType == "laserCut"){ ?>
    <h5 class="mb-2">Drawing Description</h5>
    <div class="input-group">
        <textarea rows="5" cols="50" class="form-control" aria-label="additional-comments" readonly><?php echo $job["specifications"]; ?></textarea>
    </div>
<?php } ?>


</div>
