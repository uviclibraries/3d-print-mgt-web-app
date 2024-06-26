<hr class="mb-6">
<div class="col-md-12 order-md-1">

<h5 class="mb-2">Enable Email</h5>
<div class="d-block my-3">
  <div class="custom-control custom-checkbox">
    <input id="en_email" name="email_enabaled" value= "enabled" type="checkbox" class="custom-control-input" >
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

<script>
  document.getElementById('status-select').addEventListener("change", function() {
  if (document.getElementById('status-select').value == "pending payment" || document.getElementById('status-select').value == "delivered") {
    document.getElementById("en_email").checked = true;
  }else{    document.getElementById("en_email").checked = false;
}
console.log(document.getElementById("en_email").checked)
});

</script>