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

    <!-- My script(s) -->
    <script src="js/script.js"></script>
    <script>
retrieveData(); // retriving the JSON (ellipses) data

// boolean variables to handle which iteration (design) to display
let showOne = true;
let showTwo = false;
let showThree = false;
let showFour = false;

// using instance mode to create canvas (making sure avoid confliction with drawing canvas on create.php page)
const s = ( sketch ) => {
 // setup()
 sketch.setup = () => {
   // create canvas and parent to appropriate div
    sketch.canvas = createCanvas(width, height).parent('#displayCollectiveDesign');
    sketch.colorMode(RGB);
    sketch.angleMode(DEGREES);
    translate(width / 2, 250); // drawing from width center point
  };

  // draw()
  sketch.draw = () => {
    sketch.canvas.mousePressed(switchVisual);
    if (showOne == true) {
      sketch.canvas.clear(); // clear canvas before displaying next iteration (design)
      arrayIterationOne();
    }
    else if (showTwo == true) {
      sketch.canvas.clear(); // clear canvas before displaying next iteration (design)
      arrayIterationTwo();
    }
    else if (showThree == true) {
      sketch.canvas.clear(); // clear canvas before displaying next iteration (design)
      arrayIterationThree();
    }
    else if (showFour == true) {
      sketch.canvas.clear(); // clear canvas before displaying next iteration (design)
      arrayIterationFour();
    }
  };
};

// function to handle whcih design to display - changes when user clicks canvas
function switchVisual() {
  if (showOne == true) {
    showOne = false;
    showTwo = true;
    showThree = false;
    showFour = false;
  } else if (showTwo == true) {
    showOne = false;
    showTwo = false;
    showThree = true;
    showFour = false;
  } else if (showThree == true) {
    showOne = false;
    showTwo = false;
    showThree = false;
    showFour = true;
  } else if (showFour == true) {
    showOne = true;
    showTwo = false;
    showThree = false;
    showFour = false;
  }
}

// function for the first iteration
// data is displayed in circular pattern
function arrayIterationOne() {
  push();
  scale(0.8); // scale down so less of the design gets cut off
  // iterate through array and display the ellipses
  for(let i = 0; i < shapesList.length; i++){
      rotate(360); // rotate to create circular pattern
      shapesList[i].display();
}
pop();
}

// function for the second iteration
// data is display in dotted lines
function arrayIterationTwo() {
  push();
  rotate(1.5); // rotate for vertical alignment
  scale(1.6); // scale up
  translate(-90, -120); // positioning iteration
  // iterate through array and display the ellipses
  for(let i = 0; i < shapesList.length; i++){
      shapesList[i].display();
}
pop();
}

// function for the third iteration
// data is displayed as lines rotated along central axis (all lines start from mid point)
function arrayIterationThree() {
  push();
  scale(0.8); // scale down
  // iterate through array and display the ellipses
  for(let i = 0; i < shapesList.length; i++){
    rotate(360); // rotate to create circular pattern
     shapesList[i].displayConnections();
}
pop();
}

// function for the fourth iteration
// data is displayed in lines (vertically)
function arrayIterationFour() {
  push();
  rotate(2.35); // rotate to display vertically
  scale(1.3); // scale up
  translate(-90, -120); // positioning iteration
  // iterate through array and display the ellipses
  for(let i = 0; i < shapesList.length; i++){
      shapesList[i].displayLines();
}
pop();
}

let myp5 = new p5(s);
    </script>
  </head>

  <body>
    <div id="container">
    <header>
      <h2>If Sound Could Draw</h2>
      <!-- NAVIGATION -->
      <a href="browse.php" id="browse" class="buttonStyling">VIEW THE COLLECTIVE DESIGN</a>
      <a href="create.php" id="create" class="buttonStyling">CREATE A SOUND VISUALIZATION</a>
    </header>

    <div id="browseContentArea">
      <h3>COLLECTIVE DESIGN<h3>
        <h4 style="margin-top:-10px;font-size:14px;"><i>A VISUALIZATION BASED ON ALL USER ENTRIES - CLICK ON THE DESIGN TO TOGGLE THROUGH THE ITERATIONS</i><h4>
        <!--form done using more current tags... -->
      <div id="displayCollectiveDesign">
      </div>
      <p style="float:left;width:46%;color:#CCC;">CONTRIBUTORS:</p>
      <p style="float:right;width:46%;color:#CCC;">DESCRIPTIVE KEYWORDS:</p>
    </div>
  </div> <!-- closing the container -->

  <!--- PHP --->
  <!--- retrieving database info from HTML form (contributor's name, descriptive keyword, contributor's location) --->
  <?php
require('openDB.php');
// get the contents from the db and output. ..
try {
$sql_select='SELECT * FROM artCollection'; // get content from artCollection

$result = $file_db->query($sql_select);
if (!$result) die("Cannot execute query.");

// using a while loop to fetch each row from the result set using fetch() - the while loop will automatically terminate when all rows have been read
// within the while loop, go through each field within the row (using a foreach loop), display each one and then go onto the next row
echo "<div id='details'>"; // div to hold the content (for styling purposes)
while($row = $result->fetch(PDO::FETCH_ASSOC))
{
  // echo the contributors names/locations (on left side of the page) and the keywords (on the right side of the page)
  echo "<div class='names'>".$row['name']." "."<div class='locations'>".$row['location']."</div>"."</div>";
  echo "<div class='keywords'>".$row['title']."</div>";
} // close while loop
echo "</div>"; // closing details div

}
catch(PDOException $e) {
  // Print PDOException message
  echo $e->getMessage();
}

?>
  </body>
  </html>
