<?php
$servername = "3d_print_jobs";
$username = "3d_print";
$password = "Hx_27!PfQ";
try {
  $conn = new PDO("mysql:host=$servername;dbname=3d_print_mgt", $username, $password);
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
