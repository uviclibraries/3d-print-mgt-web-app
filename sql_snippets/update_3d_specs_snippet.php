<?php 
$infill = intval($_POST["infill"]);
  $stmt->bindParam(':infill', $infill, PDO::PARAM_INT);
  $scale = intval($_POST["scale"]);
  $stmt->bindParam(':scale', $scale, PDO::PARAM_INT);
  $stmt->bindParam(':layer_height', $_POST["layer_height"], PDO::PARAM_STR);
  $supports = intval($_POST["supports"]) ;
  $stmt->bindParam(':supports', $supports , PDO::PARAM_INT);

?>