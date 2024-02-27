<!-- if its been asssigned a price, i.e. farther than "submitted"-->
<?php if($job["status"] != "submitted"){?>
  <div class="col-md-12 order-md-1">   
    <h4 class="mb-3">Price</h4>
    <div class="row">
      <div class="col-md-3 mb-3">    
        <?php if($job["status"] != 'submitted' && $job['parent_job_id'] == 0){?>
        <div class="input-group">
          <div class="input-group-prepend">
              <span class="input-group-text">$</span>
                <input type="text" name="price" class="form-control" value="<?php echo number_format((float)$job["price"], 2, '.',''); ?>" readonly>
          </div>
        </div>
      </div>
        <?php } ?>

        <?php if($job["price"] > 0 && $job["status"] == "pending payment"){
          // echo('is pending payment');
          $_SESSION['price'] = strval($job["price"]);
          $_SESSION['job_id'] = $job['id'];
          if($job["parent_job_id"] == 0 || $job['is_parent']){?>
            <a href="moneris/customer-payment.php">
              <button type="button" class="btn btn-primary btn-lg" type="submit">
                Pay here
              </button>
            </a>
          <?php }
        }?>
      </div>
      <!--If the job is a child, redirect to parent job-->
      <?php if($job["parent_job_id"] != 0 && $job["status"] == "pending payment"){?>
        <p style="color:red">This job is priced with another job. Go to <a href="customer-3d-job-information.php?job_id=<?php echo $job["parent_job_id"]; ?>">this job</a> to pay.</p>
      <?php }?>
  </div>
  <hr class="mb-6">

<?php } ?>
<!-- end if(if) else -->
