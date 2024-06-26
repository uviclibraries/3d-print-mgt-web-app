<hr class="mb-6">
<div class="col-md-12 order-md-1">
  <h4 class="mb-2">Material Type</h4>
  <div class="d-block my-3">
  <div class="custom-control custom-radio">
      <input id="plywood_3mm" name="material_type" value="Plywood 3mm" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "Plywood 3mm"){echo "checked";} ?>>
      <label class="custom-control-label" for="plywood_3mm">Plywood 3mm</label>
    </div>
    <div class="custom-control custom-radio">
      <input id="plywood_6mm" name="material_type" value="Plywood 6mm" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "Plywood 6mm"){echo "checked";} ?>>
      <label class="custom-control-label" for="plywood_6mm">Plywood 6mm</label>
    </div>
    <div class="custom-control custom-radio">
      <input id="mdf_3mm" name="material_type" value="MDF 3mm" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "MDF 3mm"){echo "checked";} ?>>
      <label class="custom-control-label" for="mdf_3mm">MDF 3mm</label>
    </div>
    <div class="custom-control custom-radio">
      <input id="mdf_6mm" name="material_type" value="MDF 6mm" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "MDF 6mm"){echo "checked";} ?>>
      <label class="custom-control-label" for="mdf_6mm">MDF 6mm</label>
    </div>
    <div class="custom-control custom-radio">
      <input id="laser_cut_other" name="material_type" value="Other" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "Other"){echo "checked";} ?>>
      <label class="custom-control-label" for="other">Other</label>
      <!-- <small class="text-muted"> - Elaborate in Additional Comments section</small> -->
    </div>
  </div>
    <!-- <div class="col-md-3 mb-3"> -->
  <label for="drawing-description">Drawing Description</label>
  <div class="input-group">
    <textarea rows="5" cols="50" class="form-control" aria-label="drawing-description" readonly><?php echo $job["specifications"]; ?></textarea>
  </div>
</div>
