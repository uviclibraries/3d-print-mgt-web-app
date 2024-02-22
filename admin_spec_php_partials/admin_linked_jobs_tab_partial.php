<?php 
// echo "linked jobs: ". count($linked_jobs) . " ; active user jobs: " . count($active_user_jobs);
if(count($active_user_jobs) > 0){?>
  <div class="col-md-12 order-md-1">
  <div id="job_relation">
    <h4>Active Customer Jobs</h4>
    <div class="row">
      <!--Shows 'Current parent: #parent id - parent name if the job has a parent, and allows for reassignment-->
      <?php if(!$job['is_parent'] && $job['parent_job_id'] != 0) { 
        $job_pointer = '<a href="admin-3d-job-specification.php?job_id=' . $parent["id"] . '">' . $parent["id"] . '</a>';
      ?>
        <div class="col-md-4 mb-3">
           <p>Current parent: <?php echo $job_pointer . ' - ' . $parent['name'];?></p>
        </div>
      <?php } ?>
        
      <?php if(!$job['is_parent']) { ?>
      <div class="col-md-4 mb-3">
        <label for="select_parent">Set a new parent:</label>
      </div>
      
    
      <div class="col-md-4 mb-3">
        <!--set which item in the dropdown will be set as the default value-->
        <?php $parent = $job['parent_job_id'];
        $default_set = false;

        // populating from $activeUserJobs PHP array
        // Create the select element
        echo '<select id="select_parent" name="select_parent" style="width:300px;" value="$job["parent"]">';
            // Loop through the array and create an option element for each item
            foreach ($active_user_jobs as $active_user_job) {
              if(!$active_user_job['is_parent']){
                // Check if this item is the default item
                if ($active_user_job['parent_job_id'] === $parent && !$default_set) {
                  echo '<option value="' . htmlspecialchars($active_user_job['id']) . '" selected>' . htmlspecialchars($active_user_job['id']) . ' - ' . htmlspecialchars($active_user_job['name']) . '</option>';
                  $default_set = true; // Mark that the default has been set
                } else {
                    echo '<option value="' . htmlspecialchars($active_user_job['id']) . '" >' . htmlspecialchars($active_user_job['id']) . ' - ' . htmlspecialchars($active_user_job['name']) . '</option>';
                }
              }
            }
            echo 'console.log("selected")';
            if(!default_set){
              echo '<option value=0 selected>None</option>';
              $default_set=true;
            }
            else{echo '<option value=0 >None</option>';
          }
        // Close the select element
        echo '</select>';
        ?> 
      </div>
    </div> <!--Setting or resetting a job's parent-->
    <?php
      echo '<p>Select the different tabs to see the customer’s active jobs with those statuses.</p>';
    }
    ?></div>
  <?php } ?> <!--Display parent identifer and/or set (new) parent if the customer has multiple active jobs-->

  <div class="tab">
    <!--show "linked jobs" tab if the job either a parent of other jobs but or has a parent thats not itself-->
    <?php if(count($linked_jobs) > 0) {?> 
      <button type="button" class="tablinks" onclick="event.preventDefault();openStatus(event, 'Linked')" id="linked_tab">Linked</button>
    <?php }?>
      <button class="tablinks" onclick="event.preventDefault();openStatus(event, 'NotPriced')" id="notpriced_tab">Not Priced</button>
      <button class="tablinks" onclick="event.preventDefault();openStatus(event, 'PendingPayment')" id="pending_payment_tab">Pending Payment</button>
      <button class="tablinks" onclick="event.preventDefault();openStatus(event, 'Paid')" id="paid_tab">Paid</button>
      <button class="tablinks" onclick="event.preventDefault();openStatus(event, 'InProgress')" id="in_progress_tab">In Progress</button>
      <button class="tablinks" onclick="event.preventDefault();openStatus(event, 'Completed')" id="completed_tab">Completed</button>
      <button class="tablinks" onclick="event.preventDefault();openStatus(event, 'OnHold')" id="on_hold_tab">On Hold</button>
  </div><!--Status tabs for user's other linked and active jobs-->

  <div id="Linked" class="tabcontent">
    <h4>Linked</h4>
    <p>These are the user's jobs that are financially linked to the job in this page. </p> 
    <div class="user_jobs_container">
      <?php foreach ($linked_jobs as $linked_job) {
        if($linked_job != $job) {
            $print_relationship = $linked_job['id'] == $job['parent_job_id'] ? 'PARENT: ' : ($linked_job['parent_job_id'] == $job['id']? 'CHILD: ':'');
            $job_pointer = '<a href="admin-3d-job-specification.php?job_id=' . $linked_job["id"] . '">' . $linked_job["id"] . '</a>';
            echo '<div class="job-item">';
            echo '<input type="checkbox" class ="job-checkbox" id="'. $linked_job['id'] . '" name="checked_jobs[]" value="' . $linked_job['id'] . '">';                  
            // Check if 'name' index is set
            if (isset($linked_job['name'])) {
                echo " " . $print_relationship . $job_pointer  . ' - ' . $linked_job['name'];
            } else {
                echo "No id available"; 
            }
            // echo '</label>';
            echo '</div>';
          }
        }
      ?>
    </div>
    <?php if(count($linked_jobs) == 0){ ?>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          removeStatus("Linked", "linked_tab");
        });
      </script>
    <?php } else{ ?>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
        document.getElementById("linked_tab").click();
        });
      </script>
    <?php } ?>
  </div> <!--show "linked jobs" tab if there are linked jobs-->



  <div id="NotPriced" class="tabcontent">
    <h4>Not Priced</h4>
    <!-- <p>These are the user's jobs that have been submitted and not processed further.</p>  -->
    <?php $submitted = 0;?>
    <div class="user_jobs_container">
      <?php foreach ($active_user_jobs as $active_job) {
        if($active_job != $job && $active_job['status'] == 'submitted') {
          $submitted++;
            $job_pointer = '<a href="admin-3d-job-specification.php?job_id=' . $active_job["id"] . '">' . $active_job["id"] . '</a>';
            echo '<div class="job-item">';
            echo '<input type="checkbox" class ="job-checkbox" id="'. $active_job['id'] . '" name="checked_jobs[]" value="' . $active_job['id'] . '">';                  
            // Check if 'name' index is set
            if (isset($active_job['name'])) {
                echo " " . $job_pointer  . ' - ' . $active_job['name'];
            } else { 
                echo "No id available"; 
            }
            // echo '</label>';
            echo '</div>';
        }
      }?>
    </div>
    <?php if($submitted == 0){ ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        removeStatus("NotPriced","notpriced_tab");
      });
    </script>
    <?php }?>
  </div> <!--show "submitted/not priced" tab if there are submitted/not priced jobs-->



  <div id="PendingPayment" class="tabcontent">
    <h4>Pending Payment</h4>
    <!-- <p>These are the user's jobs that have been priced and are awaiting payment by the customer.</p> -->
    <?php $priced = 0; ?>
    <div class="user_jobs_container">
      <?php foreach ($active_user_jobs as $active_job) {
        if($active_job != $job && $active_job['status'] == 'pending payment') {
          $priced++;
            $job_pointer = '<a href="admin-3d-job-specification.php?job_id=' . $active_job["id"] . '">' . $active_job["id"] . '</a>';
            echo '<div class="job-item">';
            echo '<input type="checkbox" class ="job-checkbox" id="'. $active_job['id'] . '" name="checked_jobs[]" value="' . $active_job['id'] . '">';                  
            // Check if 'name' index is set
            if (isset($active_job['name'])) {
                echo " " . $job_pointer  . ' - ' . $active_job['name'];
            } else { 
                echo "No id available"; 
            }
            // echo '</label>';
            echo '</div>';
            }
      }?>
    </div>
    <?php if($priced == 0){ ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        removeStatus("PendingPayment","pending_payment_tab");
      });
    </script>
    <?php }?>
  </div> <!--show "priced/ pending payment" tab if there are priced/ pending payment jobs-->
   


  <div id="Paid" class="tabcontent">
    <h4>Paid</h4>
    <!-- <p>These are the user's jobs that have been priced and are awaiting payment by the customer.</p> -->
    <?php $paid = 0;?>
    <div class="user_jobs_container">
      <?php foreach ($active_user_jobs as $active_job) {
        if($active_job != $job && $active_job['status'] == 'paid') {
          $paid++;
            $job_pointer = '<a href="admin-3d-job-specification.php?job_id=' . $active_job["id"] . '">' . $active_job["id"] . '</a>';
            echo '<div class="job-item">';
            echo '<input type="checkbox" class ="job-checkbox" id="'. $active_job['id'] . '" name="checked_jobs[]" value="' . $active_job['id'] . '">';                  
            // Check if 'name' index is set
            if (isset($active_job['name'])) {
                echo " " . $job_pointer  . ' - ' . $active_job['name'];
            } else { 
                echo "No id available"; 
            }
            // echo '</label>';
            echo '</div>';
            }
      }?>
    </div>        

    <?php if($paid == 0){ ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        removeStatus("Paid","paid_tab");
      });
    </script>
    <?php }?>
  </div> <!--show "paid" tab if there are paid jobs-->



  <div id="InProgress" class="tabcontent">
    <h4>In Progress</h4>  
    <!-- <p>These are the user's jobs that have been priced and are awaiting payment by the customer.</p> -->
    <?php $ip = 0;?>
    <div class="user_jobs_container">
      <?php foreach ($active_user_jobs as $active_job) {
        if($active_job != $job && $active_job['status'] == 'printing') {
          $ip++;
            $job_pointer = '<a href="admin-3d-job-specification.php?job_id=' . $active_job["id"] . '">' . $active_job["id"] . '</a>';
            echo '<div class="job-item">';
            echo '<input type="checkbox" class ="job-checkbox" id="'. $active_job['id'] . '" name="checked_jobs[]" value="' . $active_job['id'] . '">';                  
            // Check if 'name' index is set
            if (isset($active_job['name'])) {
                echo " " . $job_pointer  . ' - ' . $active_job['name'];
            } else { 
                echo "No id available"; 
            }
            // echo '</label>';
            echo '</div>';
            }
      }?>
    </div>
    <?php if($ip == 0){?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        removeStatus("InProgress","in_progress_tab");
      });
    </script>
    <?php }?>
  </div> <!--show in progress tab if there are jobs in progress-->
   
  <div id="Completed" class="tabcontent">
    <h4>Completed</h4>
    <!-- <p>These are the user's jobs that have been been printed/cut but not delivered.</p> -->
    <?php $printed = 0;?>
    <div class="user_jobs_container">
      <?php foreach ($active_user_jobs as $active_job) {
        if($active_job != $job && $active_job['status'] == 'printed') {
          $printed++;
            $job_pointer = '<a href="admin-3d-job-specification.php?job_id=' . $active_job["id"] . '">' . $active_job["id"] . '</a>';
            echo '<div class="job-item">';
            echo '<input type="checkbox" class ="job-checkbox" id="'. $active_job['id'] . '" name="checked_jobs[]" value="' . $active_job['id'] . '">';                  
            // Check if 'name' index is set
            if (isset($active_job['name'])) {
                echo " " . $job_pointer  . ' - ' . $active_job['name'];
            } else { 
                echo "No id available"; 
            }
            // echo '</label>';
            echo '</div>';
            }
      } ?>
    </div>
    <?php if($printed == 0){?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        removeStatus("Completed","completed_tab");
      });
    </script>
    <?php }?>
  </div> <!--show completed (not delivered) tab if there are completed/ not delivered jobs-->
  
  <div id="OnHold" class="tabcontent">
    <h4>On Hold</h4>
    <?php $hold = 0;?>
    <div class="user_jobs_container">
      <?php foreach ($active_user_jobs as $active_job) {
        if($active_job != $job && $active_job['status'] == 'on hold') {
          $hold++;
            $job_pointer = '<a href="admin-3d-job-specification.php?job_id=' . $active_job["id"] . '">' . $active_job["id"] . '</a>';
            echo '<div class="job-item">';
            echo '<input type="checkbox" class ="job-checkbox" id="'. $active_job['id'] . '" name="checked_jobs[]" value="' . $active_job['id'] . '">';                  
            // Check if 'name' index is set
            if (isset($active_job['name'])) {
                echo " " . $job_pointer  . ' - ' . $active_job['name'];
            } else { 
                echo "No id available"; 
            }
            // echo '</label>';
            echo '</div>';
            }
      }?>
      </div><?php if($hold == 0){?>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          removeStatus("OnHold","on_hold_tab");
        });
      </script>
      <?php }?>
  </div><!--show "on hold" tab if there are jobs on hold-->


<?php if(count($active_user_jobs) > 0){?>  
</div> 
<?php } ?>

<?php if($job['is_parent'] || ($job['parent_job_id'] != 0)) {?>
      
<button id="set-children-button" class="btn btn-primary">Set jobs as children</button> <!--duplicate button-->
<!-- The Duplicate Popup -->
<div id="set-children-popup" class="popup">
  <div class="popup-content">
    <span class="close" data-popup="set-children-popup">&times;</span>
    <p>Are you sure you want to set these jobs as children for the job: <?php echo $job['id'] . ' - ' . $job['name']?></p>
      <a href="customer-duplicate-3d-job.php?job_id=<?php echo $job["id"]; ?>">
          <button class="btn btn-primary">Set Children</button>
      </a>
  </div>
</div>

<?php } ?>

<?php if(count($active_user_jobs) > 0){?>
<hr class="mb-6">
<?php } ?>

<!-- Contains the javascript to show/hide empty tabs -->
<script type="text/javascript" src="js/linked_jobs_function.js"></script>

<!--Set jobs as children button
Unlink children
Update status to match $post(status dropdown)-->