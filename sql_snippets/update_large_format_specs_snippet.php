<?php 
$length_inches = intval($_POST["length_inches"]);
  $stmt->bindParam(':length_inches', $length_inches, PDO::PARAM_INT);
  $width_inches = intval($_POST["width_inches"]);
  $stmt->bindParam(':width_inches', $width_inches, PDO::PARAM_INT);
  
?>