<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include ('../../../fish_credentials.php');

if (isset($_POST['email']) && !empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
  http_response_code(400);
  exit;
}
$url = 'https://www.google.com/recaptcha/api/siteverify';
$data = array('secret' => CAPTCHA_SECRET, 'response' => $_POST['recaptcha']);

// use key 'http' even if you send the request to https://...
$options = array(
  'http' => array(
    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
    'method'  => 'POST',
    'content' => http_build_query($data),
  ),
);
$context  = stream_context_create($options);
$result = json_decode(file_get_contents($url, false, $context));
if ($result -> success == true) {
  http_response_code(200);
} else {
  http_response_code(409);
}