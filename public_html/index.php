<?php
 require('openDB.php');

// setting up the database on the index page, as it only runs at the start of the program
 try {
   // keys for keywords (title), contributors' names, and locations
   // only create the table if it doesn't not yet exist
   $theQuery = "CREATE TABLE IF NOT EXISTS artCollection (pieceID INTEGER PRIMARY KEY NOT NULL, title TEXT, name TEXT,location TEXT)";
   $file_db ->exec($theQuery);
   $file_db = null;
}
catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
  }
?>

<h3>
  <html>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0>

    <!-- Google font(s) -->
    <link href="https://fonts.googleapis.com/css2?family=Markazi+Text:wght@400;500;600;700&family=Rubik:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500;1,600&display=swap" rel="stylesheet">
    <!-- CSS stylesheet(s) -->
    <link rel="stylesheet" type="text/css" href="css/style.css" />

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/218ed566df.js" crossorigin="anonymous"></script>

    <!-- Library script(s) -->
    <script src="js/libraries/p5.min.js"></script>
    <script src="js/libraries/p5.sound.min.js"></script>
    <script src="js/libraries/p5.dom.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
  </head>

  <body>
    <div id="container">
    <!-- The index page basically just contains two buttons. The user may choose to either create a sound visualization or view the collective design(s) -->
    <div id="intro">
     <h1>Drawing With Sound</h1>
     <a href="create.php" id="create" class="buttonStyling">CREATE A SOUND VISUALIZATION</a>
     <a href="browse.php" id="browse" class="buttonStyling">VIEW THE COLLECTIVE DESIGN</a>
   </div>
 </div> <!-- closing the container -->

  </body>
  </html>
