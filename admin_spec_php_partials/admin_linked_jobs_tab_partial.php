<?php 
//job hrefs in admin..-specification.php files

// echo "linked jobs: ". count($linked_jobs) . " ; active user jobs: " . count($active_user_jobs);
if(count($active_user_jobs) > 0){
    $permit_link_statuses = array('submitted', 'pending payment', 'paid', 'on hold');
?>
  <div class="col-md-12 order-md-1">
  
    <!-- <h4>Active Customer Jobs</h4> -->
    <!-- <p>Select the different tabs to see the customer’s active jobs with those statuses.</p> -->

   <!--Display parent identifer and/or set (new) parent if the customer has multiple active jobs-->

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
    <!-- <h4>Linked</h4> -->
    <p>These are the user's jobs that are financially linked to the job in this page. </p> 
    <div class="user_jobs_container">
      <?php 
        foreach ($linked_jobs as $linked_job) {
        if($linked_job != $job) {
          $isChecked = $linked_job['status'] == 'submitted' || $linked_job['status'] == 'pending payment' ?'checked':'';
            $print_relationship = $linked_job['id'] == $job['parent_job_id'] ? 'PARENT: ' : ($linked_job['parent_job_id'] == $job['id']? 'CHILD: ':'');
            $job_pointer = $type_href . $linked_job["id"] . '">' . $linked_job["id"] . '</a>';
            $unlinking_listener = in_array($linked_job['status'], $permit_link_statuses) && !$linked_job['is_parent'] && $print_relationship == 'CHILD: ' ? 'allow-unlinking' : 'prohibit-unlinking';
            echo '<div class="job-item">';
            echo '<input type="checkbox" class ="job-checkbox prohibit-linking '. $unlinking_listener . '" id="'. $linked_job['id'] . '" name="checked_jobs[]" value="' . $linked_job['id'] . '" ' . $isChecked . '>';                  
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
          document.getElementById('Linked').style.display='none';
        });
      </script>
    <?php 
      if(!$job['is_parent']){?>
        
      <?php }
      
    }
    else{ ?>
      <script>document.getElementById('Linked').style.display='block';</script>

      <?php }?> 
  </div> <!--show "linked jobs" tab if there are linked jobs-->



  <div id="NotPriced" class="tabcontent">
    <!-- <h4>Not Priced</h4> -->
    <!-- <p>These are the user's jobs that have been submitted and not processed further.</p>  -->
    <?php $submitted = 0;?>
    <div class="user_jobs_container">
      <?php foreach ($active_user_jobs as $active_job) {
        if($active_job != $job && $active_job['status'] == 'submitted') {
          $submitted++;
            $job_pointer = $type_href . $active_job["id"] . '">' . $active_job["id"] . '</a>';
            $linking_listener = !$active_job['is_parent'] ? 'allow-linking' : 'prohibit-linking';
            echo '<div class="job-item">';
            echo '<input type="checkbox" class ="job-checkbox ' . $linking_listener . ' prohibit-unlinking" id="'. $active_job['id'] . '" name="checked_jobs[]" value="' . $active_job['id'] . '">';                  
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
    <!-- <h4>Pending Payment</h4> -->
    <!-- <p>These are the user's jobs that have been priced and are awaiting payment by the customer.</p> -->
    <?php $priced = 0; ?>
    <div class="user_jobs_container">
      <?php foreach ($active_user_jobs as $active_job) {
        if($active_job != $job && $active_job['status'] == 'pending payment') {
          $priced++;
            $job_pointer = $type_href . $active_job["id"] . '">' . $active_job["id"] . '</a>';
            $linking_listener = !$active_job['is_parent'] && $active_job['parent_job_id'] == 0 && in_array($active_job['status'], $permit_link_statuses) ? 'allow-linking' : 'prohibit-linking';
            echo '<div class="job-item">';
            echo '<input type="checkbox" class ="job-checkbox ' . $linking_listener . ' prohibit-unlinking" id="'. $active_job['id'] . '" name="checked_jobs[]" value="' . $active_job['id'] . '">';                  
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
    <!-- <h4>Paid</h4> -->
    <!-- <p>These are the user's jobs that have been priced and are awaiting payment by the customer.</p> -->
    <?php $paid = 0;?>
    <div class="user_jobs_container">
      <?php foreach ($active_user_jobs as $active_job) {
        if($active_job != $job && $active_job['status'] == 'paid') {
          $paid++;
            $job_pointer = $type_href . $active_job["id"] . '">' . $active_job["id"] . '</a>';
            $linking_listener = !$active_job['is_parent'] ? 'allow-linking' : 'prohibit-linking';
            echo '<div class="job-item">';
            echo '<input type="checkbox" class ="job-checkbox ' . $linking_listener . ' prohibit-unlinking" id="'. $active_job['id'] . '" name="checked_jobs[]" value="' . $active_job['id'] . '">';                  
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
    <!-- <h4>In Progress</h4>   -->
    <!-- <p>These are the user's jobs that have been priced and are awaiting payment by the customer.</p> -->
    <?php $ip = 0;?>
    <div class="user_jobs_container">
      <?php foreach ($active_user_jobs as $active_job) {
        if($active_job != $job && $active_job['status'] == 'printing') {
          $ip++;
            $job_pointer = $type_href . $active_job["id"] . '">' . $active_job["id"] . '</a>';

            echo '<div class="job-item">';
            echo '<input type="checkbox" class ="job-checkbox prohibit-linking prohibit-unlinking" id="'. $active_job['id'] . '" name="checked_jobs[]" value="' . $active_job['id'] . '">';                  
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
    <!-- <h4>Completed</h4> -->
    <!-- <p>These are the user's jobs that have been been printed/cut but not delivered.</p> -->
    <?php $printed = 0;?>
    <div class="user_jobs_container">
      <?php foreach ($active_user_jobs as $active_job) {
        if($active_job != $job && $active_job['status'] == 'completed') {
          $printed++;
            $job_pointer = $type_href . $active_job["id"] . '">' . $active_job["id"] . '</a>';
            echo '<div class="job-item">';
            echo '<input type="checkbox" class ="job-checkbox prohibit-linking prohibit-unlinking" id="'. $active_job['id'] . '" name="checked_jobs[]" value="' . $active_job['id'] . '">';                  
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
    <!-- <h4>On Hold</h4> -->
    <?php $hold = 0;?>
    <div class="user_jobs_container">
      <?php foreach ($active_user_jobs as $active_job) {
        if($active_job != $job && $active_job['status'] == 'on hold') {
          $hold++;
            $job_pointer = $type_href . $active_job["id"] . '">' . $active_job["id"] . '</a>';
            $linking_listener = !$active_job['is_parent'] ? 'allow-linking' : 'prohibit-linking';
            echo '<div class="job-item">';
            echo '<input type="checkbox" class ="job-checkbox ' . $linking_listener . ' prohibit-unlinking" id="'. $active_job['id'] . '" name="checked_jobs[]" value="' . $active_job['id'] . '">';                  
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
    <div class="row">
    <!--add checkbox for "change status of selected "-->
    <div class="col-md-4 mb-3">
      <input type="checkbox" id="set-statuses-checkbox" name="set-statuses-checkbox" value="set_statuses" style="margin-top:3px;" checked>
      <label for="set-statuses-checkbox" style="margin-top:5px;margin-left: 10px;">Update statuses to match</label> 
    </div>

    <?php 
      if(($job['is_parent'] || ($job['parent_job_id'] == 0)) && in_array($job['status'], $permit_link_statuses)) {?>
      <div class="col-md-4 mb-3" id="link-children-div">
        <!--set to checkbox "Set selected as children"-->
        <input type="checkbox" id="set-children-checkbox" name="set-children-checkbox" value="set_children" style="margin-top:3px;">
        <label for="set-children-checkbox"style="margin-top:5px;">Set selected jobs as children</label> 
      </div>
    <?php 
    } ?>

  <?php } ?>
  <?php 
  if($linked_jobs && ($job['is_parent'] || ($job['parent_job_id'] == 0)) && in_array($job['status'], $permit_link_statuses)) {?>
    <div class="col-md-4 mb-3" id="unlink-children-div">
      <!--set to checkbox "Unlink selected as children"-->
      <input type="checkbox" id="unlink-children-checkbox" name="unlink-children-checkbox" value="unlink_children" style="margin-top:3px;">
      <label for="unlink-children-checkbox" style="margin-top:5px;">Abandon selected jobs as children</label> 
    </div>
  <?php } ?>
  </div> 
</div>
<?php } ?>

<?php if(count($active_user_jobs) > 0){?>
<hr class="mb-6">
<?php } ?>


<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php foreach ($active_statuses as $index => $isActive): ?>
        if (<?php echo $isActive ? 'true' : 'false'; ?>) {
            document.getElementById('<?php echo $tabs[$index]; ?>').click();
        }
    <?php endforeach; ?>
});
</script>


<!-- Contains the javascript to show/hide empty tabs -->
<script type="text/javascript" src="js/linked_jobs_function.js"></script>
<!--Contains the javascript to check/uncheck linked job action checkboxes-->
<script type="text/javascript" src="js/update_checked_linked.js"></script>
<!--Set jobs as children button
Unlink children
Update status to match $post(status dropdown)-->