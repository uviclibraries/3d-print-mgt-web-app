<?php
session_start();
include '../auth-sec.php'; //Gets CAS & db
include 'moneris.inc.php';

$order_id = $user ."-". date('YmdHis') ."-". $_SESSION['job_id'];
$result = moneris_preload($order_id, $_SESSION['price']);
$ticket = $result[0];
$error = $result[1];
?>
<!doctype html>
<html lang="en">
<head>
 <meta charset="utf-8" />
 <link rel="stylesheet" type="text/css" href="css/style.css">
 <title>Moneris Checkout Demo</title>
</head>
<body>
<div class="banner">
<a href="https://www.uvic.ca/library/"><img src="https://devwebapp.library.uvic.ca/alma/sms/image/thumbnail_01VIC_INST-01UVIC.png" width="180" alt="University of Victoria Libraries"></a>
</div>
<p>
<h4>Moneris Checkout</h4>
</p>
<div id="outerDiv" style="width:400px;height=300px">
<div id="monerisCheckout"></div>
<script src="https://gateway.moneris.com/chktv2/js/chkt_v2.00.js"></script>
<script>
  var co = new monerisCheckout();
  co.setMode("prod");
  co.setCheckoutDiv("monerisCheckout");
  co.startCheckout("<?php echo $ticket ?>");

  var pageload_handler = function(data) {
    console.log("preload", data);
  };

  var cancel_handler = function(data) {
    console.log("cancel", data);
    co.closeCheckout();
    window.location.replace("/3dprint/customer-dashboard.php");
  };

  var error_handler = function(data) {
    console.log("error", data);
    const json = JSON.parse(data);
    co.closeCheckout();
    window.location.replace("/3dprint/customer-dashboard.php");
  };

  var receipt_handler = function(data) {
    const json = JSON.parse(data);
    console.log("receipt", json);
    var req = new XMLHttpRequest();
    req.overrideMimeType("application/json");
    req.open("GET", "/3dprint/moneris/receipt.php?amount=<?php echo $_SESSION['price'] ?>&order_id=<?php echo $order_id ?>&ticket=" + json.ticket, true);
    req.onload = function() { console.log("receipt response", this.jsonResponse); }
    req.send(null);
  };

  var complete_handler = function(data) {
    window.location.replace("/3dprint/customer-dashboard.php");
  };

  co.setCallback("page_loaded", pageload_handler);
  co.setCallback("cancel_transaction", cancel_handler);
  co.setCallback("error_event", error_handler);
  co.setCallback("payment_receipt", receipt_handler);
  co.setCallback("payment_complete", complete_handler);
</script>
</body>
</html>

