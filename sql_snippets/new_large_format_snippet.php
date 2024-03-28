<?php
    
    // Check if the unit of measurement is 'cm' and convert if necessary
    function convertToInches($cm) {
      return floatval($cm) / 2.54; // 1 cm = 0.393701 inches
    }

    $lengthInches =$_POST["length_input"];
    $widthInches = $_POST["width_input"];
    

    if($_POST["unit_measurement"] == 'cm'){
      $lengthInches = convertToInches($_POST["length_input"]);
      $widthInches = convertToInches($_POST["width_input"]);
      
    }
  

    $large_format_copies = intval($_POST["large_format_copies"]);


    //create new object in large format table, webjob.id == large_format_print_job.large_format_print_id to allow for joins.
    $stmt = $conn->prepare("INSERT INTO large_format_print_job (large_format_print_id, model_name, copies, width_inches, length_inches, comments) VALUES (:large_format_print_id, :model_name, :copies, :width_inches, :length_inches, :comments)");
    $stmt->bindParam('large_format_print_id', $curr_id); // Use extracted id (bind_new_user.php) to insert job information to the large_format_print_jobs table
    $stmt->bindParam(':model_name', $hash_name);
    $stmt->bindParam(':copies', $large_format_copies);
    $stmt->bindParam(':width_inches', $widthInches);
    $stmt->bindParam(':length_inches', $lengthInches);
    $stmt->bindParam(':comments', $_POST["comments"]);
    $stmt->execute();
?>