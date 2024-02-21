<hr class="mb-4">
<h5 class="mb-2">Additional Comments</h5>
<div class="input-group">
    <textarea rows="5" cols="50" class="form-control" aria-label="additional-comments" readonly><?php echo $job["comments"]; ?></textarea>
</div>

<?php if($user_view == "admin"){?>
<hr class="mb-4">
<h5 class="mb-2">Staff Notes</h5>
<div class="input-group">
    <textarea rows="5" cols="50" class="form-control" name="staff_notes" aria-label="additional-comments"><?php echo $job["staff_notes"]; ?></textarea>
</div>
<div class="invalid-feedback">
Please enter additional comments.
</div>
<?php }?>