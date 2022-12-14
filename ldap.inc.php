<?php
//
// ldap.inc.php  -  LDAP lookup subroutine
//

//$ldap_url = 'ldaps://ldap1p.uvic.ca';  // production server
$ldap_url = "ldaps://ldap1r.uvic.ca";  // test server
$ldap_binddn = "cn=Auth Manager,ou=LIBR,ou=administrators,dc=uvic,dc=ca";
$ldap_bindpw = "PASSWORD HERE";


//
// get_personal_info($netlink_id)
//
// - returns ($name, $email, $errormesg) for $netlink_id
// - if $errormesg is NULL the other two are set
//
function get_personal_info($netlink_id) {

  global $ldap_url, $ldap_binddn, $ldap_bindpw;

  $conn = ldap_connect($ldap_url);
  if (!$conn) {
	return array('', '', "Can't connect to LDAP");
  }

  $status = ldap_bind($conn, $ldap_binddn, $ldap_bindpw);
  if (!$status) {
	return array('', '', "Can't bind to LDAP");
  }

  $srch = ldap_search($conn, "ou=people,dc=uvic,dc=ca", "uid=$netlink_id");
  $info = ldap_get_entries($conn, $srch);
  if ($info['count'] < 1) {
	return array('', '', "$netlink_id not found in LDAP");
  }

  $name = $info[0]['cn'][0];
  if (!$name) {
	  // Removed because the user's cn or canonical name is no longer available to external apps
    // return array('', '', "No cn for $netlink_id in LDAP"); 
    $name = $netlink_id;
  }

  $email = $info[0]['mail'][0];
  if (!$email) {
	return array('', '', "No mail for $netlink_id in LDAP");
  }

  // test LDAP server obfuscates email addresses, so fix that...
  $email = preg_replace('/\.xxxyyyzzz$/', '', $email);
  return array($name, $email, NULL);
}

?>
