<?php

    // Use extracted id (bind_new_user_snippet.php) to insert job information to the laser_cut table

    $laser_copies = intval($_POST["laser_copies"]);

    //create new object in laser cut table, webjob.id == laser_job.laser_cut_id to allow for joins.

    $stmt = $conn->prepare("INSERT INTO laser_cut_job (laser_cut_id, model_name, copies, material_type, specifications, comments) VALUES (:laser_cut_id, :model_name, :copies, :material_type, :specifications, :comments)");

    $stmt->bindParam('laser_cut_id', $curr_id);
    $stmt->bindParam(':model_name', $hash_name);
    $stmt->bindParam(':copies', $laser_copies);
    $stmt->bindParam(':material_type', $_POST["laser_material_type"]);
    $stmt->bindParam(':specifications', $_POST["user_specs"]);
    $stmt->bindParam(':comments', $_POST["comments"]);
    $stmt->execute();

?>