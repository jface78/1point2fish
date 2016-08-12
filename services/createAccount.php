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
  try {
    $dbh = new PDO('mysql:host=' .DB_HOST . ';dbname=' . DB_DATABASE, DB_USER, DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
  }
  $query = 'SELECT userID FROM users WHERE email=:email AND verified=:verify';
  $sth = $dbh -> prepare($query);
  $sth -> execute([':verify' => '1', ':email' => $_POST['email']]);
  $count = $sth -> rowCount();
  if ($count) {
    $userID = $sth -> fetch()[0];
    $query = 'DELETE from user_libraries WHERE userID=:userID';
    $sth = $dbh -> prepare($query);
    $sth -> execute([':userID' => $userID]);
  } else {
    $hash = md5($_POST['email'] . HASH_SALT);
    $query = 'INSERT INTO users(email, hash)VALUES(:email, :hash)';
    $sth = $dbh -> prepare($query);
    $sth -> execute([':email' => $_POST['email'], ':hash' => $hash]);
    $userID = $dbh->lastInsertId();
  }
  echo $userID;
  for ($i=0; $i < count($_POST['libs']); $i++) {
    $query = 'INSERT INTO user_libraries(userID, libraryID) VALUES(:user, :lib)';
    $sth = $dbh -> prepare($query);
    $sth -> execute([':user' => $userID, ':lib' => $_POST['libs'][$i]]);
  }
  
  http_response_code(200);
} else {
  http_response_code(409);
}