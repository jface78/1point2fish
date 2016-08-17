<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include ('../../../fish_credentials.php');

if (!isset($_POST['libName']) || empty($_POST['libName']) || !isset($_POST['libURL']) || empty($_POST['libURL'])) {
  http_response_code(400);
  exit;
}
$headers = 'From: noreply@1point2.fish' . "\r\n" .
           'Reply-To: noreply@1point2.fish' . "\r\n" .
           'X-Mailer: PHP/' . phpversion();
$subject = 'New library request from 1point2.fish';
$message = 'A visitor requested the following library be added:' . "\n\n";
$message .= 'Name: ' . $_POST['libName'] . "\n\n";
$message .= 'URL: ' . $_POST['libURL'] . "\n\n";
mail(MY_EMAIL_ADDRESS, $subject, $message, $headers);
http_response_code(200);
?>