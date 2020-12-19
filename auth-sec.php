<?php
require ('db.php');			//required to access db

//Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
	//go to login page.
	header("Location: index.php");
	die();
}

//get session variables
$user = $_SESSION['user'];
$user_name =$_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
$user_type = $_SESSION['user_type'];

// logout if desired
if (isset($_REQUEST['logout'])) {
	require_once 'CAS.php'; //required for uvic CASphp
	$_SESSION['loggedin'] = false;
	session_unset();
	session_destroy();
	phpCAS::client(CAS_VERSION_2_0, 'www.uvic.ca', 443, '/cas');
	phpCAS::logout();
}

?>
