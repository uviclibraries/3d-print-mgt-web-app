<?php 

$faq_href ="";
$job_href ="";
$library_href= "https://www.uvic.ca/library/";
switch($jobType){
  case ("3d print"):
    $faq_href = 'https://onlineacademiccommunity.uvic.ca/dsc/how-to-3d-print/';
    $job_href = 'https://webapp.library.uvic.ca/3dprint/customer-3d-job-information.php?job_id=';
    break;
  case("laser cut"):
    $faq_href = 'https://onlineacademiccommunity.uvic.ca/dsc/how-to-laser-cut/';
    $job_href = 'https://webapp.library.uvic.ca/3dprint/customer-laser-job-information.php?job_id=';
    break;
  case("large format print"):
    $faq_href = 'https://onlineacademiccommunity.uvic.ca/dsc/tools-tech/large-format-printer-and-scanner/';
    $job_href = 'https://webapp.library.uvic.ca/3dprint/customer-large-format-print-job-information.php?job_id=';
    break;
}

$job_href = $job_href.$curr_id;

//email is being triggered by submission of new job
if($statusEmail == "submitted"){

	$job_name = $_POST["job_name"];
	//Send customer submission email
	$msg = "
	<html>
	<head>
	<title>HTML email</title>
	</head>
	<body>
	<p>Hello, ".$user_name.". This is an automated message from the DSC.</p>
	<p>Thank you for submitting your ".$jobType." (".$job_name.") request to the DSC at McPherson Library. We will evaluate the cost of the ".$jobType." and you'll be notified by email when it is ready for payment. If you have any questions about the process or the status of your ".$jobType.", please review our <a href=". $faq_href .">FAQ</a> or email us at DSCommons@uvic.ca.</p>
	</body>
	</html>";
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= "From: dscommons@uvic.ca" . "\r\n";
	mail($user_email,"DSC - New ".$jobType." job",$msg,$headers);
}
?>