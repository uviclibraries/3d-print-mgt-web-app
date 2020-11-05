<?php
//
//  phpCAS/index.php  -  CAS authentication using phpCAS module
//                    -  based on:  https://github.com/apereo/phpCAS/blob/master/docs/examples/example_simple.php
//
require_once 'CAS.php'; //required for uvic CASphp
require ('db.php');			//required to access db

phpCAS::setDebug();
phpCAS::setVerbose(true);

phpCAS::client(CAS_VERSION_2_0, 'www.uvic.ca', 443, '/cas');
phpCAS::setNoCasServerValidation();
phpCAS::forceAuthentication();

// logout if desired
if (isset($_REQUEST['logout'])) {
	phpCAS::logout();
}

//Access to db

$user = phpCAS::getUser();
$usersearch = $conn->query("SELECT id FROM users WHERE netlink_id = '$user'");
//if user is not in the db create entry for new user.
if ($usersearch->rowCount() < 1) {
	$stm = $conn->prepare("INSERT INTO users (netlink_id, name, user_type, email) VALUES (:netlink_id, :name, :user_type, :email)");
	$stm->bindParam(':netlink_id', $user);
	$ldapName = "ldap name";
	$stm->bindParam(':name', $ldapName);
	$userType = 1;
	$stm->bindParam(':user_type', $userType);
	$ldapEmail = "kenziewong+newuser@gmail.com";
	$stm->bindParam(':email', $ldapEmail);
	$stm->execute();
}

//have email address
//check if in db, add if not.

?>
