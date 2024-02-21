<hr class="mb-6">
<div class="col-md-12 order-md-1">
  <h4 class="mb-3">Dimensions</h4>

  <div class="row">
    <div class="col-md-3 mb-3">
        <label for="length_input">Length<span class="error"></span></label>
        <div class="input-group">
          <div class="input-group mb-3">
            <input type="number" step="0.01" name="length_inches" class="form-control" value="<?php echo $job["length_inches"]; ?>" placeholder="100" aria-label="100" aria-describedby="basic-addon2" oninput="validateDimensions()" style="width: 200px;">
        </div>
        <div class="invalid-feedback" style="width: 100%;">
        Please enter the desired length.
        </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <label for="length">Width</label>
        <div class="input-group">
        <div class="input-group mb-3">
            <input type="number" step="0.01" class="form-control" name="width_inches" value="<?php echo $job["width_inches"]; ?>" placeholder="Width" aria-label="100" aria-describedby="basic-addon2" oninput="validateDimensions()" style="width: 200px;">
        </div>
        <div class="invalid-feedback" style="width: 100%;">
            Please enter the desired width.
        </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
      <label for="unit_selector">Unit</label><br>
      <select id="unit_selector" name="unit_measurement" onchange="validateDimensions()" style="width: 100px;"> <!-- Adjust the width as needed -->
        <option value="in">in</option>
        <option value="cm">cm</option>
      </select>
      <div class="invalid-feedback">
        Please select a unit of measurement.
      </div>
    </div>
  </div>
  <div>
    <span id="dimension_warning"></span>
  </div>
</div>


<script>
function validateDimensions(){
  console.log('validating dimensions');
  var unit = document.getElementById('unit_selector').value;
  var width = document.getElementById('width_input').value;
  var length = document.getElementById('length_input').value;
  var dimension_warning = document.getElementById("dimension_warning");

  var maxDimension = unit === 'cm' ? 91.44 : 36; // 36 inches in cm
  // console.log("unit: " + unit + "; length: " + length + ";width" + width);

  if (width > maxDimension && length > maxDimension) {
    // console.log('too large');
    dimension_warning.textContent = `Both width and length cannot exceed ${maxDimension} ${unit}. Please decrease one of the measurements.`;
    document.getElementById('length_input').value = "";
    document.getElementById('width_input').value = "";
    // if(event.type === "submit") {
    //     event.preventDefault(); // Prevent form submission
    // }
  }
  else{
    // console.log("not too large");
    dimension_warning.textContent = "";
  }
}
</script><!--Prevent oversize prints-->
