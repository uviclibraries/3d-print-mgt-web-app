<div class="col-md-12 order-md-1">
<h4 class="mb-3">Price</h4>
  <div class="row">
    <div class="col-md-3 mb-3">
      <div class="input-group">
        <div class="input-group">
          <div class="input-group-prepend">
            <!-- ** catch non floatable input-->
            <span class="input-group-text">$</span>
            <input type="text" name="price" autocomplete="off" class="form-control" value="<?php echo number_format((float)$job["price"], 2, '.',''); ?>"
            <?php if ($job["status"] != "submitted" && $job["status"] != "pending payment"&& $job["status"] != "on hold"): ?>
              readonly
            <?php endif; ?>
            >
          </div>
        </div>
        <small class="text-muted">Reminder: Minimum payment is $2.00.</small>
        <div class="invalid-feedback" style="width: 100%;">
        Status is required.
        </div>
      </div>
    </div>

    <div class="col-md-3 mb-3">
      <div class="input-group">
        <input type="text" name="duration" autocomplete="off" class="form-control" value="<?php echo $job["duration"]; ?>">
      </div>
      <small class="text-muted">Number of minutes the machines were running to complete the project.</small>
    </div>
  </div>
</div>