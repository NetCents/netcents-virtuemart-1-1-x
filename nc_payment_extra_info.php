<?php

$callback_url = "/index.php?page=checkout.netcents_result&order_number=" . $db->f("order_number") . "&option=com_virtuemart";
$data = array(
  'external_id' => $db->f("order_id"),
  'amount' => number_format($db->f("order_total"), 2, '.', ''),
  'currency_iso' => $_SESSION['vendor_currency'],
  'callback_url' => SECUREURL . $callback_url,
  'first_name' => $user->first_name,
  'last_name' => $user->last_name,
  'email' => $user->email,
  'webhook_url' => SECUREURL."nc_notify.php",
  'merchant_id' => NETCENTS_API_KEY,
  'data_encryption' => array(
    'external_id' => $db->f("order_id"),
    'amount' => number_format($db->f("order_total"), 2, '.', ''),
    'currency_iso' => $_SESSION['vendor_currency'],
    'callback_url' => SECUREURL . $callback_url,
    'first_name' => $user->first_name,
    'last_name' => $user->last_name,
    'email' => $user->email,
    'webhook_url' => SECUREURL."nc_notify.php",
    'merchant_id' => NETCENTS_API_KEY,
  )
);

$payload = json_encode($data);

$ch = curl_init(NETCENTS_GATEWAY . "/api/v1/widget/encrypt");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt(
  $ch,
  CURLOPT_HTTPHEADER,
  array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($payload),
    'Authorization: Basic ' . base64_encode( NETCENTS_API_KEY. ':' . NETCENTS_SECRET_KEY)
  )
);
$result = curl_exec($ch);
$json = json_decode($result, true);
$redirect_url = NETCENTS_GATEWAY . "/merchant/widget?data=" . $json['token'] . '&widget_id=' . NETCENTS_HOSTED_PAYMENT_ID;
curl_close($ch);
if ($_GET['page'] != 'account.order_details') {

?>
<a style="font-size:18px" href="<?php echo $redirect_url ?>">Click here to redirect to NetCents and Pay your invoice</a>
<?php } ?>
