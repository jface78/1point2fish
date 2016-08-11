<!DOCTYPE html>
<html lang="US">
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script src="https://use.fontawesome.com/40eaec28fc.js"></script>
    <script src="js/main.js"></script>
  </head>
  <body>
    <div>
      <img src="img/logo_large.png" alt="1.2 Fish" title="1.2 Fish">
    </div>
    <div style="margin-top:20px;">
      <h2>1Point2.Fish</h2>
      software version monitor<br><br>
    </div>
    <nav class="tabber">
      <header>
        <h3 data-tab="1">Configure</h3>
        <h3 data-tab="2">Request a library</h3>
        <h3 data-tab="3">WUT?</h3>
      </header>
      <content id="tab-1">
        <header>Select which libraries you'd like to track.</header>
        <main></main>
        <footer>
        <hr>
        Now enter your email address: <input type="text">
        <div id="captcha">
          <div class="g-recaptcha" data-sitekey="6LdaYScTAAAAAARS1-RgGRiL9R7xJ1RzFiqFFadQ"></div>
        </div>
        <div id="formError">sdfdsf</div>
        </footer>
      </content>
      <content id="tab-2">
      TAB TWO
      </content>
      <content id="tab-3">
        <main>
          
          <ul>
            <h4>What is this? I'm easily confused and therefore angry.</h3>
            <li><b>1.2Fish</b> is a website that monitors various programming libraries, and emails users when a new release
        becomes available.</li>
            <h4>Okay, well how does it work?</h3>
            <li>You provide an email address, select whichever libraries you want to track, and 
            we email you a notification when and if that happens.</li>
            <h4>No, how does it <i>really</i> work?</h3>
            <li>We have a database of supported libraries. We scrape their download pages every day to see if there's a new release.</li>
            <h4>When does that happen?</h3>
            <li>Once a day at midnight EST.</li>
          </ul>
        </main>
      </content>
    </nav>
  </body>
</html>