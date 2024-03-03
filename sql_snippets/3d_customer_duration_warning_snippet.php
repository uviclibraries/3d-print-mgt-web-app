<?php 
//Calculates total duration of all print jobs completed during the current 1/3 of the year by that user
//Displays warning in admin-3d-job-specification if exceeds 30 h
$today=date('Y-m-d');
$currentYear = date('Y');

$term1 = $currentYear.'-01-01';
$term2 = $currentYear.'-04-01';
$term3 = $currentYear.'-09-01';
$term4 = $currentYear.'-12-31';
$termStart='Y-m-d';
$termEnd='Y-m-d';


if($today > $term1 && $today <=$term2){
	$termStart = $term1;
	$termEnd = $term2;
}
elseif($today > $term2 && $today <=$term3){
	$termStart = $term2;
	$termEnd = $term3;
}
elseif($today > $term3 && $today <=$term4){
	$termStart = $term3;
	$termEnd = $term4;
}

$durvals = $conn->prepare("SELECT SUM(3d_print_job.duration) AS total_duration FROM `3d_print_job` INNER JOIN `web_job` ON `3d_print_job`.`3d_print_id` = web_job.id WHERE (web_job.delivered_date BETWEEN '{$termStart}' AND '{$today}' AND web_job.netlink_id = :netlink_id)");
  $durvals->bindParam(':netlink_id', $job['netlink_id']);

$durvals->execute();
$duration = $durvals->fetch(PDO::FETCH_ASSOC)['total_duration'];

if(!$duration){
	$duration = 0;
}

function convertMinutesToHoursAndMinutes($duration) {
    $hours = intdiv($duration, 60); // Calculate total hours
    $remainingMinutes = $duration % 60; // Calculate remaining minutes
    return sprintf("%d hours and %d minutes", $hours, $remainingMinutes); // Format and return the string
}

$duration_hm = convertMinutesToHoursAndMinutes($duration); // Outputs: 2 hours and 30 minutes
?>

