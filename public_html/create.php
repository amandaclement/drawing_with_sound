<?php
require('openDB.php');

//check if there has been something posted to the server to be processed
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
  // need to process the inputted keywords, contributors' names and their locations
 $title = $_POST['a_title']; // title = keyword in this case
 $name = $_POST['a_name'];
 $location = $_POST['a_location'];

  try{
      $title_es =$file_db->quote($title);
      $name_es = $file_db->quote($name);
      $location_es = $file_db->quote($location);

      // handling the insertion of content into database
      $queryInsert ="INSERT INTO artCollection(title, name, location)VALUES ($title_es,$name_es,$location_es)";
      $file_db->exec($queryInsert);
      $file_db = null;
      exit;
    }

    catch(PDOException $e) {
        // Print PDOException message
        echo $e->getMessage();
      }
} // POST
?>

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
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <!-- HTML2CANVAS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js" integrity="sha512-s/XK4vYVXTGeUSv4bRPOuxSDmDlTedEpMEcAQk0t/FMd9V6ft8iXdwSBxV0eD60c6w/tjotSlKu9J2AAW1ckTA==" crossorigin="anonymous"></script>

  <!-- My script(s) -->
  <script src="js/script.js"></script>
</head>

<body>
<header>
  <h2>If Sound Could Draw</h2>
  <!-- NAVIGATION -->
  <a href="browse.php" id="browse" class="buttonStyling">VIEW THE COLLECTIVE DESIGN</a>
  <a href="create.php" id="create" class="buttonStyling">CREATE A SOUND VISUALIZATION</a>
</header>

<div id="createContentArea">
  <div id="designArea">
    <!-- P5 CANVAS GOES HERE -->
</div>

<!-- BUTTON OPTIONS: LIVE AUDIO -->
<div id="inputOptions">
  <button type="button" name="liveAudio" value="liveAudio" id="liveAudio" class="buttonStyling">CLICK HERE TO BEGIN</button><br>
  <!-- basic controls  -->
  <button type="button" name="pause" value="pause" id="pause" class="buttonStyling buttonVisibility"><i class="fas fa-pause"></i></button>&nbsp;&nbsp;
  <button type="button" name="restart" value="restart" id="restart" class="buttonStyling buttonVisibility"><i class="fas fa-redo-alt"></i></button>

<div id="floatedControls">
  <!-- some additional user controls for them to customize certain aspects of the design as it gets illustrated -->
  <button type="button" name="intensityDecrease" value="intensityDecrease" id="intensityDecrease" class="addedControls"><i class="fas fa-arrow-circle-left"></i></button>&nbsp;
  DISTANCE
  <button type="button" name="intensityIncrease" value="intensityIncrease" id="intensityIncrease" class="addedControls"><i class="fas fa-arrow-circle-right"></i></i></button>&nbsp;<br>

  <button type="button" name="sizeDecrease" value="sizeDecrease" id="sizeDecrease" class="addedControls"><i class="fas fa-arrow-circle-left"></i></button>&nbsp;
  SIZE
  <button type="button" name="sizeIncrease" value="sizeIncrease" id="sizeIncrease" class="addedControls"><i class="fas fa-arrow-circle-right"></i></i></button>&nbsp;<br>

  <button type="button" name="rotationDecrease" value="rotationDecrease" id="rotationDecrease" class="addedControls"><i class="fas fa-arrow-circle-left"></i></button>&nbsp;
  ROTATION
  <button type="button" name="rotationIncrease" value="rotationIncrease" id="rotationIncrease" class="addedControls"><i class="fas fa-arrow-circle-right"></i></i></button>&nbsp;
</div>

<!-- DESIGN OPTIONS THAT APPEAR WHEN DESIGN IS COMPLETE -->
<div id="hidingOptions"> <!-- these are generally hidden, so created a div to simplify the handling of hiding/revealing -->
  <div id="handleDesignOptions">
  <button type="button" name="exit" value="exit" id="exitButton"><i class="fas fa-times"></i></button> <!-- button to exit handleDesignOptions div -->
  <p>YOUR DESIGN IS COMPLETE!<br>WHAT WOULD YOU LIKE TO DO WITH IT?</p>
  <button type="button" name="save" value="save" id="save" class="buttonStyling">SAVE TO DEVICE</button><br>
  <button type="button" name="uploadToGallery" value="uploadToGallery" id="uploadToGallery" class="buttonStyling">ADD TO COLLECTIVE DESIGN</button><br>
  <button type="button" name="delete" value="delete" id="delete" class="buttonStyling">DELETE</button><br>
<!-- HTML FORM -->
<!-- NEW for the result -->
<div id = "result"></div>
<div class= "formContainer">
<form id="insertGallery" action="" enctype ="multipart/form-data">
<fieldset>
<!-- title is the only required field, everything else may be left blank -->
<p>TELL US ABOUT YOUR DESIGN.</p>
<p><label>DESCRIPTIVE KEYWORD: &nbsp;</label><input type="text" size="24" maxlength = "50" name = "a_title" required><span class="formRequirement">* REQUIRED</span></p>
<p><label>YOUR NAME: &nbsp;</label><input type="text" size="24" maxlength = "50" name = "a_name"><span class="formRequirement">* OPTIONAL</span></p>
<p><label>CITY, COUNTRY: &nbsp;</label><input type="text" size="24" maxlength = "50" name = "a_location"><span class="formRequirement">* OPTIONAL</span></p>
<p class ="submit"><input type = "submit" name = "submit" value = "SUBMIT" id ="submitInfo"></p>
</fieldset>
</form>
</div>
</div>
</div>
</div>
</div> <!-- closing createContent -->

<script>
$(document).ready (function(){
  // processed on submit (when uses finalizes HTML form)
    $("#insertGallery").submit(function(event) {
    //stop submit the form, we will post it manually. PREVENT THE DEFAULT behaviour ...
     event.preventDefault();
     let form = $('#insertGallery')[0];
     let data = new FormData(form);

     // handling php POST via AJAX
     $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: "create.php",
            data: data,
            processData: false, // prevents from converting into a query string
            contentType: false,
            cache: false,
            timeout: 600000, // timeout for good practice
            success: function (response) {
            console.log("we had success!");
            console.log(response);
            $('#insertGallery')[0].reset();
           },
           error:function(){ // checking if error occurred
          console.log("error occurred");

        }
      });
   });
});

</script>
</body>
</html>
