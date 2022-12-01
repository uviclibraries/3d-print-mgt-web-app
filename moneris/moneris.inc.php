<?php
//
// moneris.inc.php  -  moneris HPP parameters
//

$moneris_url = 'https://www3.moneris.com/HPPDP/index.php';
$moneris_store_id = 'UFRCT79462';
$moneris_hpp_key = 'hpIKCP6P9SUJ';
$moneris_note = 'UVic Libraries 3D Print Job Payment';

// If $moneris_log is enabled, then $moneris_log_file must be writable
// by the apache process
$moneris_log = True;
$moneris_log_file = '../log/moneris.log';

// The Moneris HPP POST response variables
$moneris_response_fields = array (
    'response_order_id',
    'response_code',
    'date_stamp',
    'time_stamp',
    'result',
    'trans_name',
    'cardholder',
    'card',
    'charge_total',
    'f4l4',
    'message',
    'iso_code',
    'bank_approval_code',
    'bank_transaction_id',
    'txn_num',
    'avs_response_code',
    'cavv_result_code',
    'INVOICE',
    'ISSCONF',
    'ISSNAME'
);

?>
