<?php
//$servername = "3d_print_jobs";
$username = "3d_print";
$password = "Hx_27!PfQ";
$dbname = "3d_print_jobs";
try {
  $conn = new PDO("mysql:host=localhost;dbname=$dbname", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  #echo “Connected successfully”;
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
#$stm = $conn->query(“SELECT VERSION()“);
#$version = $stm->fetch();
#echo $version;
?>
