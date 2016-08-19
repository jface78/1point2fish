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
    
    <meta property="og:url" content="http://1point2.fish" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="1Point2.Fish" />
    <meta property="og:description" content="A web service to keep developers up-to-date with the latest software library releases." />
    <meta property="og:image" content="http://1point2.fish/img/logo_large.png" />
    <meta property="og:image:width" content="200" />
    <meta name="google-site-verification" content="RjuLN5Hmdz3vY4cuXzYljakCzNI_3f55yMlhvv96MzI" />
    
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
    <link rel="stylesheet" type="text/css" href="css/dictumAlertBox.css">
    <link rel="stylesheet" type="text/css" media="(max-width: 767px)" href="css/mobile.css">
    <link rel="stylesheet" type="text/css" media="(max-width: 400px)" href="css/tiny.css"> 
    

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script src="https://use.fontawesome.com/40eaec28fc.js"></script>
    <script src="js/main.js"></script>
    <script src="js/DictumAlertBox.js"></script>
  </head>
  <body>
    <div class="page_wrap">
      <div>
        <img src="img/logo_large.png" alt="1.2 Fish" title="1.2 Fish" id="logo">
      </div>
      <div style="margin-top:20px;">
        <h2>1Point2.Fish</h2>
        software version tracking<br><br>
      </div>
      <div class="tabber">
        <nav>
          <header>
            <h3 data-tab="1">Configure</h3>
            <h3 data-tab="2">Request a library</h3>
            <h3 data-tab="3">WUT?</h3>
          </header>
        </nav>
        <section id="tab-1">
          <h4>Select which libraries you'd like to track</h4>
          <main></main>
          <footer>
            <div id="captcha">
              <span>Prove you're not an AI:</span>
              <div class="g-recaptcha" data-sitekey="6LdaYScTAAAAAARS1-RgGRiL9R7xJ1RzFiqFFadQ"></div>
            </div>
            <div id="emailForm">
              <span>Enter your email address:</span>
              <input type="email" id="userEmail">
            </div>
            <div class="formError">&nbsp;</div>
            <div id="createBtn" style="text-align:center;"><button>create</button></div>
          </footer>
        </section>
        <section id="tab-2">
          <div id="submitForm">
            <h4>Submit a code library to be monitored</h4>
            <span>Library name:</span><input type="text" id="libName"><br><br>
            <span>Download URL:</span><input type="text" id="libURL"><br><br>
            <div class="formError">&nbsp;</div><br><br>
            <div id="submitBtn" style="text-align:center;"><button>submit</button></div>
          </div>
        </section>
        <section id="tab-3">
          <h4>Questions</h4>
            <ul>
              <li><h3>What is this? I'm easily confused and therefore angry.</h3></li>
              <li><b>1.2Fish</b> is a website that monitors various programming libraries, and emails users when a new release becomes available.</li>
              <li><h3>Okay, well how does it work?</h3></li>
              <li>You provide an email address, select whichever libraries you want to track, and we email you a notification when and if that happens.</li>
              <li><h3>No, how does it <i>really</i> work?</h3></li>
              <li>We have a database of supported libraries. We scrape their download pages every day to see if there's a new release.</li>
              <li><h3>When does that happen?</h3></li>
              <li>Once a day at midnight EST.</li>
            </ul>
        </section>
      </div>
    </div>
    <footer class="site_footer">&copy;2016 1Point2.fish</footer>
  </body>
</html>