<?php
session_start();
include '../auth-sec.php'; //Gets CAS & db
include 'moneris.inc.php';




$order_id = $user ."-". date('YmdHis') ."-". $_SESSION['job_id'];
//
// initiate Moneris HPP preload request
//
$post_fields= [
    'ps_store_id' => $moneris_store_id,
    'hpp_key' => $moneris_hpp_key,
    'order_id' => $order_id,
    'charge_total' => $_SESSION['price'],
    'hpp_preload' => '',
    'cust_id' => $_SESSION['user_id'],
    'email' => $user_email,
    'note' => $moneris_note
  ];

  $post_fields_str = http_build_query($post_fields);

  $request = curl_init();
  curl_setopt($request, CURLOPT_URL, $moneris_url);
  curl_setopt($request, CURLOPT_POST, true);
  curl_setopt($request, CURLOPT_POSTFIELDS, $post_fields_str);
  curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($request);

  if ($moneris_log) {
    error_log("\n==== preload response begin ====\n", 3, $moneris_log_file);
    error_log($response, 3, $moneris_log_file);
    error_log("\n==== preload response end ======\n", 3, $moneris_log_file);
  }

  //
  // parse preload response and redirect to Moneris HPP using ticket
  //
  $xml = new SimpleXMLElement($response);
  $resp_ticket = $xml->ticket;
  $resp_hpp_id = $xml->hpp_id;
  $resp_order_id = $xml->order_id;
  $resp_code = $xml->response_code;

  if (intval($resp_code) >= 50) {
    $mesg = "Unable to preload Moneris data";
    $redirect_url = 'error.php?mesg=' . urlencode($mesg);
    header("Location: $redirect_url");
    exit;
  }
  if ($resp_order_id != $order_id) {
    $mesg = "Error preloading Moneris data";
    $redirect_url = 'error.php?mesg=' . urlencode($mesg);
    header("Location: $redirect_url");
    exit;
  }

  $redirect_url = $moneris_url . '?hpp_preload=&ticket=' . $resp_ticket . '&hpp_id=' . $resp_hpp_id;
  header("Location: $redirect_url");

?>
