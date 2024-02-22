<?php

	$stmt = $conn->prepare("INSERT INTO web_job (netlink_id, job_name, job_purpose, academic_code, course_due_date, submission_date, status) VALUES (:netlink_id, :job_name, :job_purpose, :academic_code, :course_due_date,:submission_date, :job_status)");

	$stmt->bindParam(':netlink_id', $user);
	$stmt->bindParam(':job_name', $_POST["job_name"]);
	$stmt->bindParam(':job_purpose', $_POST["job_purpose"]);
	$stmt->bindParam(':academic_code', $_POST["academic_code"]);
	$stmt->bindParam(':course_due_date',$_POST["academic_deadline"]);
	$stmt->bindParam(':job_status', $status);
	$stmt->bindParam(':submission_date', $current_date);

    $good_statement &= $stmt->execute();
	
	if(!$good_statement){
	    die("Error during SQL execution");
	  }

?>