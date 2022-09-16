"use strict";

/*****************
PROTOTYPE - IF SOUND COULD DRAW
by Amanda Clement

Link to the project proposal: https://hybrid.concordia.ca/a_ment/proposal.html
Link to the project prototype: https://hybrid.concordia.ca/a_ment/projects/prototype/index.html
******************/

// SETTING SOME GLOBAL VARIABLES
// for audio input
let mic;
let sound;
let fft;

// for tracking changes in audio input (related to FREQUENCY)
// these are used to control the position of the ellipses on the canvas
let initialX = 0; // original position
let newX = initialX; // new position becomes original
let changeX = 0; // this will essentially be a counter to track the changes

// for the pattern drawing
let symmetry = 8; // how many times the pattern is reflected
let angle = 360 / symmetry; // they will be reflected in a circular pattern
let designLimit = 300; // limiting how large the design becomes before completion

// to control the rotation of the design
let rotationValue = 0;

// to generate design colours
let randomColor;
let randomColor2;
let r; let g; let b;

// boolean to determine if we should begin creating the design
let start = false;

// varibables that user can control to change design properties (dynamically)
// defaults set to 1
let sizeMultiplier = 1;
let rotationMultiplier = 1;
let intensityMultiplier = 1;

// array to store the ellipse info (used for JSON file data storage/retrieval)
let ellipsesArray = [];

// array to hold the custom ellipses (for JSON)
let shapesList = [];

// to handle how many ellipses have been "recorded" for JSON file
let numRecordedEllipses = 0;

// variables to control when the "recording" of the ellipses (for JSON storage) begins
// (to be randomized within a pre-determined range)
let ellipsesTimeout;
let ellipsesTimeoutMin = 1500; // range min is 1.5 seconds (so user has time to react)
let ellipsesTimeoutMax = 25000; // range max is 25 seconds

// setup()
//
function setup() {
  // preparing the canvas
  let cnv = createCanvas(windowWidth - 60, 500);
  cnv.parent("#designArea"); // putting canvas in the drawing area defined in html
  colorMode(RGB);
  angleMode(DEGREES);
  background(0);

  // setting up audio
  mic = new p5.AudioIn(); // getting the mic input
  mic.start();

  fft = new p5.FFT(); // audio frequency
  fft.setInput(mic); // set it to the input of the mic

  // prepping the RGB values for the first color
  r = random(255);
  g = random(255);
  b = random(255);

  // assigning the two random colours for the pattern
  randomColor = color(r, g, b); // using variables for the first color's RGB values to make it easier to use in JSON file
  randomColor2 = color(random(255), random(255), random(255));

  // assigning the initial value for the ellipsesTimeout
  ellipsesTimeout = random(ellipsesTimeoutMin, ellipsesTimeoutMax);
}

// draw()
//
function draw() {
  begin(); // make sure the user is ready
  controls();
  if (start == true) { // if boolean is true, design should be drawn
    drawingDesign();
  }
}

// function to handle whether the user is ready to begin
// by clicking the button (CLICK HEREE TO BEGIN), the design begins and the controls appear
function begin() {
  $('#liveAudio').click(function() { // live audio option
    start = true; // if liveAudio button is pressed, start drawing the design based on mic input
    $('.buttonVisibility').show(); // displaying the play/pause/reset controls
    $('#floatedControls').show(); // displaying the distance/size/rotation controls
  });
}

// function for the play, pause, and restart buttons for live audio
function controls() {
  // PAUSE/PLAY
  $('#pause').click(function() { // if pause button is pressed ...
    if (start == true) { // if the boolean is true (meaning the design is being drawn)
      start = false; // make boolean false to pause it
      let playButton = $('<i class="fas fa-play"></i>'); // play icon (Fton Awesome icon)
      $("#pause").html(playButton); // change button to play icon
    } else if (start == false) { // if the boolean is false
      start = true; // make it true for design to keep getting drawn
      let pauseButton = $('<i class="fas fa-pause"></i>'); // pause icon (Fton Awesome icon)
      $("#pause").html(pauseButton); // change button to pause icon
    }
  });
  // RESET
  $('#restart').click(function() {
    clear(); // clear the canvas
    changeX = 0; // reset to 0
    generateNewColours();
    generateNewTimeout();
    numRecordedEllipses = 0;
    start = true; // start it automatically, even if they were on pause mode
  });
}

// function to handle the user's ability to intensity of the design (so effectively changing the distance between the dots) as it's been illustrated
function adjustingIntensity() {
  // if the user clicks the decrease button (left arrow)...
  $('#intensityDecrease').on("click", function() {
    if (intensityMultiplier > 0) { // setting a limit (minimum) - being 0 to avoid going into the negatives
      intensityMultiplier = intensityMultiplier - 0.00005; // decrease the intensity (so the distance between the dots)
    }
  });

  // if the user clicks the increase button (right arrow)...
  $('#intensityIncrease').on("click", function() {
    if (intensityMultiplier < 10) { // setting a limit (maximum)
      intensityMultiplier = intensityMultiplier + 0.000005; // increase the intensity (so the distance between the dots)
    }
  });
}

// function to handle the user's ability to change the size of the ellipses in the design as it's been illustrated
function adjustingSize() {
  // if the user clicks the decrease button (left arrow)...
  $('#sizeDecrease').on("click", function() {
    if (sizeMultiplier > 0) { // setting a limit (minimum) - being 0 to avoid going into the negatives
      sizeMultiplier = sizeMultiplier - 0.0005; // decrease the size of the ellipses
    }
  });

  // if the user clicks the increase button (right arrow)...
  $('#sizeIncrease').on("click", function() {
    if (sizeMultiplier < 4) { // setting a limit (maximum)
      sizeMultiplier = sizeMultiplier + 0.0005; // increase the size of the ellipses
    }
  });
}

// function to handle the user's ability to change the rotation of the design as it's been illustrated
// it can be rotated clockwise, or set to 0 (no counterclockwise as it gets too hectic/out of control)
function adjustingRotation() {
  // if the user clicks the decrease button (left arrow)...
  $('#rotationDecrease').on("click", function() {
    if (rotationMultiplier > 0) { // setting a limit (minimum) - being 0 to avoid going into the negatives
      rotationMultiplier = rotationMultiplier - 0.0005; // decrease the rotation
    }
  });

  // if the user clicks the increase button (right arrow)...
  $('#rotationIncrease').on("click", function() {
    if (rotationMultiplier < 20) { // setting a limit (maximum)
      rotationMultiplier = rotationMultiplier + 0.00005; // increase the rotation (clockwise)
    }
  });
}

// function to handle the resetting of the size, rotation, and intensity multipliers (called on canvas reset / design completion)
function resetMultipliers() {
  sizeMultiplier = 1;
  rotationMultiplier = 1;
  intensityMultiplier = 1;
}

// function to handle drawing/displaying the design on the canvas
function drawingDesign() {
  // handling the adjustments when the users clicks an arrow button
  adjustingIntensity();
  adjustingSize();
  adjustingRotation();

  rotationValue += 0.05; // default rotation (kept clockwise)

  translate(width / 2, height / 2); // drawing from center point

  let vol = mic.getLevel(); // getting the current volume level from the mic input
  let mappingVol = map(vol, 0, 0.05, 0, width / 2); // mapping the volume to a more usable range in the context of the pattern

  // analyzing the frequency --> a waveform is returned, which will be averaged to get a single number
  let spectrum = fft.analyze();
  let spectrumSum = 0;

  // iterating through the waveform values, and suming them up
  for (let i = 0; i < spectrum.length; i++) {
    spectrumSum += parseInt(spectrum[i], 10);
  }
  // getting the average from the array of numbers in the array
  let spectrumAverage = spectrumSum / spectrum.length;

  // ellipse size is also based on volume
  let size = map(vol, 0, 0.6, 0, 20); // mapped to a more usable range
  size = constrain(size, 0, 5); // constrain the size

  if (changeX < designLimit) { // keep drawing until pattern reaches pre-determined limit
    // for loop for drawing the pattern
    for (let i = 0; i < symmetry; i++) { // sides are symmetrical and in circular shape
      rotate(angle);
      initialX = spectrumAverage; // position of ellipse is based on frequency average
      if (initialX > newX) { // if increase is detected ...
        changeX = changeX + 0.5; // increase value (related to drawing speed)
      } else if (initialX < newX) { // if decrease is detected ...
        changeX = changeX + 0.01; // increase too, but significantly less (related to drawing speed)
      }

      noStroke();
      fill(randomColor); // based on the first random colour generated in setup
      ellipse(initialX * intensityMultiplier, changeX * intensityMultiplier, size * sizeMultiplier); // positioning ellipse according to frequency, and sizing according to volume
      push();
      scale(1, -1); // to create mirror effect (essentially reflecting dots across axis)
      fill(randomColor2); // based on the second random colour generated in setup
      ellipse(initialX * intensityMultiplier, changeX * intensityMultiplier, size * sizeMultiplier); // positioning ellipse according to frequency, and sizing according to volume
      pop();
      // new position becomes original for the relevant variables
      newX = initialX;

// function to handle the "recording"/"saving" of the ellipses data
  setTimeout(function() {
        if (numRecordedEllipses < 201) { // "record" 200 ellipses for JSON storage
          // getting the values for the ellipses (x, y, s, r, g, b)
          // including x position, y position, size, and colour values(from first colour only)
          let newEllipse = {
              "x" : initialX * intensityMultiplier, // x position
              "y" : changeX * intensityMultiplier,  // y position
              "s" : size * sizeMultiplier, // size
              "r" : r, // red value
              "g" : g, // red value
              "b" : b  // blue value
          };
          // then push them into the ellipseArray
          ellipsesArray.push(newEllipse);
          numRecordedEllipses++;
        }
      },ellipsesTimeout);

      // handling the rotation of the design
      rotate(rotationValue * rotationMultiplier);
    }
  // once the edge of the canvas has been reach (in terms of width), run the following...
  } else {
    start = false; // set boolean to false since the pattern has reached the canvas width so we want to stop drawing
    setTimeout(function() { // short delay before running the handleDesign function so user can admire their work
      handleDesign(); // handling the options div
    }, 2000);
  }
}

// function to handle the closing of handleDesignOptions div (the one containing the save, upload, and delete buttons)
function exitButton() {
  $('#exitButton').click(function() { // if the x icon is pressed
    $('#exitButton').parent().hide(); // hide the options div
    resetCanvas(); // reset the canvas
  });
}

// function to handle user's decision concerning what they would like to do with their design (save, upload, or delete)
function handleDesign() {
  $('#hidingOptions').show(); // display the appropriate options div
  $('#exitButton').parent().show();

  $('#save').click(function() {
    noLoop(); // this should NOT LOOP as it would cause the canvas to be saved over and over again
    saveCanvas('cnv', 'png'); // save canvas content as png --> THIS WILL KEEP SAVING OVER AND OVER SINCE IT'S RUNNING IN LOOP
    loop(); // then we can continue looping
    // don't hide the options until the click the 'x' or delete, in case they would like to also upload to gallery
  });
  $('#uploadToGallery').click(function() {
    // requires further development
    $('form').show(); // and display the form
    $('#submitInfo').click(function() {
      // ensuring that the user filled out the keyword field (the rest is optional)
      let valid = true;
       $('[required]').each(function() {
         if ($(this).is(':invalid') || !$(this).val()) valid = false;
       });
  if (valid) { // if the keyword field is filled out, save data to JSON file
    saveToJSON(); // save the ellipses data to the JSON file
    $('form').hide(); // hide the form
    resetCanvas();
  }
})
  });
  $('#delete').click(function() {
    $('#exitButton').parent().hide(); // hide the options div
    resetCanvas();
  });
  exitButton(); // check status of exit button
}

// function to handle resetting the canvas so the design can be re-drawn
function resetCanvas() {
  clear(); // clear the canvas
  start = true; // start over
  changeX = 0; // reset to 0
  generateNewColours(); // generate two new colours
  resetMultipliers(); // reset the multipliers (user adjustments)
  numRecordedEllipses = 0; // reset the number of recorded ellipses
  generateNewTimeout(); // generate new number for ellipsesTimeout
  drawingDesign(); // start drawing the design
}

// function to generate two new random colours to be used in the design
// this will be called when the canvas resets
function generateNewColours() {
  // prepping the RGB values for the first color
  r = random(255);
  g = random(255);
  b = random(255);
  // assigning the two random colours for the pattern
  randomColor = color(r, g, b); // using variables for the first color's RGB values to make it easier to use in JSON file
  randomColor2 = color(random(255), random(255), random(255));
}

// function to generate a new number for ellipsesTimeout so it "records" at a different time
// this will be called when the canvas resets
function generateNewTimeout() {
 ellipsesTimeout = random(ellipsesTimeoutMin, ellipsesTimeoutMax);
}

// CustomEllipse class
class CustomEllipse {
  // constructor for position x, position y, size, and RGB values of the custom ellipse
  constructor(x,y,s,r,g,b) {
    this.x = x;
    this.y = y;
    this.s = s;
    this.r = r;
    this.g = g;
    this.b = b;
  }

  // for first and second iterations on browse page
  display() {
    noStroke();
    fill(color(this.r, this.g, this.b));
    ellipse(this.x, this.y, this.s);
  }

  // for third iteration on browse page
  displayConnections() {
    stroke(color(this.r, this.g, this.b));
    strokeWeight(this.s / 10);
    line(0, 0, this.x, this.y);
  }

  // for fourth iteration on browse page
  displayLines() {
    stroke(color(this.r, this.g, this.b));
    strokeWeight(this.s / 10); // proportionally reduced size/weight for more usable numbers
    beginShape();
     vertex(this.x, this.y);
     vertex(this.y, this.x);
    endShape(CLOSE);

  }
}

// function to store the ellipses data a JSON file // this function is called when the UPLOAD TO GALLERY form is submitted
function saveToJSON() {
  console.log(ellipsesArray);
  let stringified = JSON.stringify(ellipsesArray); // stringifying the ellipsesArray data to use in JSON file

// using AJAX POST to handle adding the ellipses data to a JSON file (to be retrieved and displayed on the browse.php page)
$.ajax({
    type: 'POST',
    data: {ellipsesArray:stringified},
    url: '../browse.php', // handled in the browse.php page
    success: function(data){
        console.log("success");
        console.log(data);
        retrieveData();
    },
    error: function(){
        console.log("error");
    }
});

}

// functio to handle to retrieval of the ellipses data using AJAX
function retrieveData(){
  console.log("retrieve");
  $.ajax({
      type: 'POST',
      data: {getData:"get_ellipses"},
      url: 'https://hybrid.concordia.ca/a_ment/projects/finalProject/cart351_IfSoundCouldDraw/public_html/script.php',
      success: function(data){
          console.log("success");
          console.log(data);
          let dataFromPhp = JSON.parse(data);
          console.log(dataFromPhp);
          //is an array (number of files === length of array)::
          for(let i = 0; i < dataFromPhp.length; i++){
            //each file has its own array of objects
            if(dataFromPhp[i]!==null){
            let arrayOfObjects = dataFromPhp[i];
            //go through each ...
            for(let j = 0; j < arrayOfObjects.length; j++){
              //access each one (x,y,.....)
              shapesList.push(new CustomEllipse(arrayOfObjects[j].x, arrayOfObjects[j].y, arrayOfObjects[j].s, arrayOfObjects[j].r, arrayOfObjects[j].g, arrayOfObjects[j].b));
            }
          }
          }
      },
      error: function(){
          console.log("error");
      }
  });
}
