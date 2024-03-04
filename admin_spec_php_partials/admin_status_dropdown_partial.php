      <div class="col-md-12 order-md-1">
        <h4 class="mb-3">Status</h4>
        <div class="row">
          <div class="col-md-3 mb-3">
            <select class="custom-select d-block w-100" name="status" id="status-select">
              <?php 
                if ($job["status"] == "cancelled") {?> 
                  <option value="cancelled" selected readonly>cancelled</option> 
                <?php } if ($job["status"] == "archived") {?> 
                  <option value="arachived" selected readonly>archived</option> 
                <?php } 
                else { ?>
                  <option value="submitted" <?php if ($job["status"]== "submitted"){echo "selected";} ?>>Not Priced</option>
                  <option value="pending payment" <?php if ($job["status"]== "pending payment"){echo "selected";} ?>>Pending Payment</option>
                  <option value="on hold" <?php if ($job["status"]== "on hold"){echo "selected";} ?>>On Hold</option>
                  <option value="paid" <?php if ($job["status"]== "paid"){echo "selected";} ?>>Paid</option>
                  <option value="printing" <?php if ($job["status"]== "printing"){echo "selected";} ?>>Printing</option>
                  <option value="completed" <?php if ($job["status"]== "completed"){echo "selected";} ?>>Completed</option>
                  <option value="delivered" <?php if ($job["status"]== "delivered"){echo "selected";} ?>>Delivered</option>
                  <?php if(!$job['is_parent']) { ?>
                  <option value="cancelled" <?php if ($job["status"]== "cancelled"){echo "selected";} ?>>Cancelled</option>
                <?php } ?>
                  <option value="archived" <?php if ($job["status"]== "archived"){echo "selected";} ?>>Archived</option>
              <?php } ?>
            </select>
          </div>
          <div class="col-md-3 mb-3">
          <!--Set in general_partials/declare_status_date.php-->
            <p><?php echo "Status changed: <br>" .$status_date;?></p>
          </div>
          <div class="col-md-3 mb-3">
            <p><?php echo "Status changed by: <br>" . $status_signer; ?></p>
          </div>
        </div>
      </div><!--Status-->