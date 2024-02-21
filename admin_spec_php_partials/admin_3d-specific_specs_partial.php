<hr class="mb-6">
<div class="col-md-12 order-md-1">
  <h4 class="mb-3">Specifications</h4>
    <div class="row">
        <div class="col-md-3 mb-3">
            <label for="username">Infill</label>
            <div class="input-group">
              <div class="input-group mb-3">
                <input type="text" name="infill" class="form-control" value="<?php echo $job["infill"]; ?>" placeholder="100" aria-label="100" aria-describedby="basic-addon2">
                <div class="input-group-append">
                <span class="input-group-text" id="basic-addon2">%</span>
                </div>
            </div>
            <div class="invalid-feedback" style="width: 100%;">
            Infill is required.
            </div>
          </div>
        </div>

        <div class="col-md-3 mb-3">
            <label for="username">Scale</label>
            <div class="input-group">
            <div class="input-group mb-3">
                <input type="text" class="form-control" name="scale" value="<?php echo $job["scale"]; ?>" placeholder="100" aria-label="100" aria-describedby="basic-addon2">
                <div class="input-group-append">
                    <span class="input-group-text" id="basic-addon2">%</span>
                </div>
                </div>
            <div class="invalid-feedback" style="width: 100%;">
                Scale is required.
            </div>
            </div>
        </div>
    </div>

    <div class="row">
      <div class="col-md-3 mb-3">
        <label for="layer-height">Layer Height</label>
        <select class="custom-select d-block w-100" name="layer_height" id="layer-height">
          <option <?php if ($job["layer_height"]== 0.2){echo "selected";} ?>>0.2</option>
          <option <?php if ($job["layer_height"]== 0.15){echo "selected";} ?>>0.15</option>
          <option <?php if ($job["layer_height"]== 0.1){echo "selected";} ?>>0.1</option>
          <option <?php if ($job["layer_height"]== 0.06){echo "selected";} ?>>0.06</option>
        </select>
      </div>
      <div class="col-md-3 mb-3">
        <label for="supports">Supports</label>
        <select class="custom-select d-block w-100" name="supports" id="supports">
          <option value = 1  <?php if ($job["supports"]== 1){echo "selected";} ?>>Yes</option>
          <option value = 0 <?php if ($job["supports"]== 0){echo "selected";} ?>>No</option>
        </select>
      </div>
    </div>

  <hr class="mb-4">

    <h5 class="mb-2">Material Type</h5>
    <div class="d-block my-3">
      <div class="custom-control custom-radio">
        <input id="pla" name="material_type" value="PLA" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "PLA"){echo "checked";} ?>>
        <label class="custom-control-label" for="pla">PLA</label>
      </div>
      <div class="custom-control custom-radio">
        <input id="pla-pva" name="material_type" value="PLA + PVA" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "PLA + PVA"){echo "checked";} ?>>
        <label class="custom-control-label" for="pla-pva">PLA + PVA</label>
      </div>
      <div class="custom-control custom-radio">
        <input id="petg" name="material_type" value="PETG" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "PETG"){echo "checked";} ?>>
        <label class="custom-control-label" for="petg">PETG</label>
      </div>
      <div class="custom-control custom-radio">
        <input id="tpu95" name="material_type" value="TPU95" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "TPU95"){echo "checked";} ?>>
        <label class="custom-control-label" for="tpu95">TPU95</label>
      </div>
      <div class="custom-control custom-radio">
        <input id="other" name="material_type" value="Other" type="radio" class="custom-control-input" <?php if ($job["material_type"]== "Other"){echo "checked";} ?>>
        <label class="custom-control-label" for="other">Other</label>
        <small class="text-muted"> - Elaborate in Additional Comments section</small>
      </div>
    </div>
  </div>
