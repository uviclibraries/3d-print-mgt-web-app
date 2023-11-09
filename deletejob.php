<?php

require ('db.php');

$stmt = $conn->prepare("INSERT INTO users (name, netlink_id, user_type, email) VALUES (:name, :material_type, :copies, :status)");
$user_name = "Mike Cave";
$user = "mcave";
$type = 1;
$email = //""mcave@uvic.ca";
$stmt->bindParam(':name', $user_name);
$stmt->bindParam(':copies', $type, PDO::PARAM_INT);
$stmt->bindParam(':material_type', $user);
$stmt->bindParam(':status', $email);
$stmt->execute();

$stmt = $conn->prepare("INSERT INTO print_job (netlink_id, job_name, model_name, infill, scale, layer_height, supports, copies, material_type, status) VALUES (:netlink_id, :job_name, :model_name, :infill, :scale, :layer_height, :supports, :copies, :material_type, :status)");

$jobName = "Mike Cave - PLayer Board";
$fileName = "deleted-file.stl";
$matType = "PLA";
$status = "submitted";
$infill_bind = intval(10);
$scale_bind = intval(100);
$layer_bind = floatval(number_format((float)0.2, 2, '.',''));
$support_bind = intval(1);
$copies_bind = intval(2);
$stmt->bindParam(':netlink_id', $user);
$stmt->bindParam(':job_name', $jobName);
$stmt->bindParam(':model_name', $fileName);
$stmt->bindParam(':infill', $infill_bind, PDO::PARAM_INT);
$stmt->bindParam(':scale', $scale_bind , PDO::PARAM_INT);
$stmt->bindParam(':layer_height', $layer_bind);
$stmt->bindParam(':supports', $support_bind, PDO::PARAM_INT);
$stmt->bindParam(':copies', $copies_bind, PDO::PARAM_INT);
$stmt->bindParam(':material_type', $matType);
$stmt->bindParam(':status', $status);
$stmt->execute();

?>
