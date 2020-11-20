<?php
//
//  phpCAS/index.php  -  CAS authentication using phpCAS module
//                    -  based on:  https://github.com/apereo/phpCAS/blob/master/docs/examples/example_simple.php
//
require_once 'CAS.php'; //required for uvic CASphp
require ('db.php');			//required to access db

//"Global" Variables
$user = "";	//user netlink_id
$user_email = "@uvic.ca";
$user_type = 1; //Regular
$user_name = "non-admin";

//Not signed in, no session itmes.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
	phpCAS::setDebug();
	phpCAS::setVerbose(true);

	phpCAS::client(CAS_VERSION_2_0, 'www.uvic.ca', 443, '/cas');
	phpCAS::setNoCasServerValidation();
	phpCAS::forceAuthentication();


	$user = phpCAS::getUser();
	$usersearch = $conn->query("SELECT name, user_type, email FROM users WHERE netlink_id = '$user'");
	//if user is not in the db create entry for new user.
	if ($usersearch->rowCount() < 1) {

		//LDAP Start
		require('ldap.inc.php');
		list($user_name, $user_email, $error) = get_personal_info($user);
		if ($error) {
			//Do this if error occurs.
	    $redirect_url = 'error.php?mesg=' . urlencode($error);
	    header("Location: $redirect_url");
	    exit;
	  }
		//LDAP end

		//Insert into db
		$stm = $conn->prepare("INSERT INTO users (netlink_id, name, user_type, email) VALUES (:netlink_id, :name, :user_type, :email)");
		$stm->bindParam(':netlink_id', $user);
		$stm->bindParam(':name', $user_name);
		$user_type = 1;
		$stm->bindParam(':user_type', $user_type);
		$stm->bindParam(':email', $user_email);
		$stm->execute();
	}
	//user exists in db
	else {
		echo "\n\n\n\n\nexists and creating sessions deats\n\n\n\n\n" . session_status();
		foreach ($usersearch as $key) {
			$_SESSION['user'] = $user;
			$_SESSION['user_name'] = $key["name"];
		  $_SESSION['user_email'] = $key["email"];
			$_SESSION['user_type'] = $key["user_type"];
		}
	}
	$_SESSION['loggedin'] = true;
}
//user = loggedin

$user = $_SESSION['user'];
$user_name =$_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
$user_type = $_SESSION['user_type'];

// logout if desired
if (isset($_REQUEST['logout'])) {
	$_SESSION['loggedin'] = false;
	phpCAS::logout();
	session_unset();
	session_destroy();
}


?>
