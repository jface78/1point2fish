<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include ('../../fish_credentials.php');
try {
  $dbh = new PDO('mysql:host=' .DB_HOST . ';dbname=' . DB_DATABASE, DB_USER, DB_PASS);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
}
if (!isset($_GET['hash']) || empty($_GET['hash'])) {
  http_response_code(400);
  exit();
}
$query = 'SELECT email FROM users WHERE hash=:hash AND active=:active';
$sth = $dbh -> prepare($query);
$sth -> execute([':hash' => $_GET['hash'], ':active' => 0]);

$message = "Unable to locate your account, or account already activated.";
if ($sth -> rowCount() > 0) {
  $email = $sth -> fetch()[0];
  $message = "Thank you. Your account has been activated.<br><br>";
  $message .= 'To alter your selections, update the chosen libraries on the main page using the same email address.<br><br>';
  $message .= 'To unsubscribe from all future notification emails, either follow the "unsubscribe" link at the bottom ';
  $message .= 'of your notification emails, or click <a href="unsubscribe.php?email=' . $email . '">here.</a>';
  $query = 'UPDATE users SET active=:active WHERE hash=:hash';
  $sth = $dbh -> prepare($query);
  $sth -> execute([':active' => '1', ':hash' => $_GET['hash']]);
}
?>

<!DOCTYPE html>
<html lang="en-US">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="Author" content="1.2.Fish">
    <meta name="Description" content="Web tools to keep developers up-to-date with the latest software library releases.">
    <meta name="Keywords" content="software libraries, version, versioning, tracker, releases, notifier, notifications">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="img/favicons/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    
    <title>1.2 Fish - Red Fish, Blue Fish</title>

    <link rel="apple-touch-icon" sizes="57x57" href="img/favicons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="img/favicons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="img/favicons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="img/favicons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="img/favicons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="img/favicons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="img/favicons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="img/favicons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="img/favicons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="img/favicons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="img/favicons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicons/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <script type="text/javascript">
      setTimeout(function() {
        window.location.href = 'http://1point2.fish';
      }, 30000);
    </script>
  </head>
  <body>
    <div>
      <img src="img/logo_large.png" alt="1.2 Fish" title="1.2 Fish">
    </div>
    <div style="margin-top:20px;">
      <h2>1Point2.Fish</h2>
      code library version tracking<br><br>
    </div>
    <div style="padding:20px;">
      <?php echo $message;?>
    </div>
  </body>
</html>