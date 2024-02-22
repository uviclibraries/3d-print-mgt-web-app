<?php

// Extract most recent use id from the web job table based on user netlink id

	$stmt = $conn->prepare("SELECT MAX(id) FROM web_job WHERE netlink_id=:user_netlink");
	$stmt->bindParam(':user_netlink', $user);
	$good_statement &= $stmt->execute();
	$curr_id = $stmt->fetch(PDO::FETCH_NUM)[0];

	if(!$good_statement){
	die("Error during SQL execution");
	}

	if(!$curr_id){
	die('No web job entry for username {$user}');
	}

?>