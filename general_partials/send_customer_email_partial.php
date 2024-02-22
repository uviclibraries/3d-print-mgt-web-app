<?php 

$faq_href ="";
$job_href ="";
$library_href= "https://www.uvic.ca/library/";
$dsc_email = 'dscommons@uvic.ca';
switch($jobType){
  case ("3d print"):
    $faq_href = 'https://onlineacademiccommunity.uvic.ca/dsc/how-to-3d-print/';
    $job_href = 'https://webapp.library.uvic.ca/3dprint/customer-3d-job-information?job_id=';
    break;
  case("laser cut"):
    $faq_href = 'https://onlineacademiccommunity.uvic.ca/dsc/how-to-laser-cut/';
    $job_href = 'https://webapp.library.uvic.ca/3dprint/customer-laser-job-information.php?job_id=';
    break;
  case("large format print"):
    $faq_href = 'https://onlineacademiccommunity.uvic.ca/dsc/tools-tech/large-format-printer-and-scanner/';
    $job_href = 'https://webapp.library.uvic.ca/3dprint/customer-large-format-print-job-information?job_id=';
    break;
}

$job_href = $job_href.$job['id'];

//email is being triggered by submission of new job
if($status_email == "submitted"){

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

else{ // Email is being triggered by the admin specs page
	$userSQL = $conn->prepare("SELECT * FROM users WHERE netlink_id = :netlink_id");
      $userSQL->bindParam(':netlink_id', $job['netlink_id']);
      $userSQL->execute();
      $job_owner = $userSQL->fetch();

	if($status_email == "pending payment"){
		$msg = "
	      <html>
	      <head>
	      <title>HTML email</title>
	      </head>
	      <body>
	      <p> Hello, ". $job_owner['name'] .". This is an automated email from the DSC. </p>
	      <p> Your ".$jobType." job (".$job['job_name']. ") has been evaluated at a cost of $" . (number_format((float)$_POST["price"], 2, '.','')) . " </p>
	      <p> Please make your payment <a href=". $job_href .">here</a> for it to be placed in our printing queue.</p>
	      <p>If you have any questions please review our <a href=". $faq_href .">FAQ</a> or email us at ".$dsc_email. ".</p>
	      </body>
	      </html>";
	      $headers = "MIME-Version: 1.0" . "\r\n";
	      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	      $headers .= "From: dscommons@uvic.ca" .  "\r\n";
	      mail($job_owner['email'],"Your ".$jobType." is ready for payment",$msg,$headers);
		}
	if($status_email == "delivered"){
      $msg = "
      <html>
      <head>
      <title>HTML email</title>
      </head>
      <body>
      <p>Hello, ". $job_owner['name'] .". This is an automated email from the DSC. </p>
      <p> Your ".$jobType." job (".$job['job_name']. ") has been completed. You can pick it up from the front desk at the McPherson Library.</p>
      <p>Please check up to date library hours by checking the library website <a href=". $library_href .">here</a></p>
      </body>
      </html>";
      $headers = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers .= "From: dscommons@uvic.ca" .  "\r\n";
      mail($job_owner['email'], "Your ".$jobType." is ready for collection",$msg,$headers);
	}

}
?>