<?php
    
  

    //create new object in 3d table, webjob.id == 3d_print_job.3d_print_id to allow for joins.
    
    $infill_bind = intval($_POST["infill"]);
    $scale_bind = intval($_POST["scale"]);
    $layer_bind = floatval(number_format((float)$_POST["layer_height"], 2, '.',''));
    $support_bind = intval($_POST["supports"]);
    $copies_bind = intval($_POST["copies"]);


    $stmt = $conn->prepare("INSERT INTO 3d_print_job (3d_print_id, model_name, infill, scale, layer_height, supports, copies, material_type, comments) VALUES (:3d_print_id, :model_name, :infill, :scale, :layer_height, :supports, :copies, :material_type, :comments)");

    $stmt->bindParam(':3d_print_id', $curr_id);// Use extracted id (bind_new_user.php) to insert job information to the 3d_print table
    $stmt->bindParam(':model_name', $hash_name);
    $stmt->bindParam(':infill', $infill_bind, PDO::PARAM_INT);
    $stmt->bindParam(':scale', $scale_bind , PDO::PARAM_INT);
    $stmt->bindParam(':layer_height', $layer_bind);
    $stmt->bindParam(':supports', $support_bind, PDO::PARAM_INT);
    $stmt->bindParam(':copies', $copies_bind, PDO::PARAM_INT);
    $stmt->bindParam(':material_type', $_POST["print_material_type"]);
    $stmt->bindParam(':comments', $_POST["comments"]);
    $stmt->execute();
?>