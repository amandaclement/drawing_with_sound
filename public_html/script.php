<?php
// handling the ellipses array info to be stored in individual (unique) JSON files
if($_SERVER['REQUEST_METHOD'] == 'POST' &&  isset($_POST['ellipsesArray']) ){
  echo ("save");
  $str = $_POST['ellipsesArray'];
  //make a unique file name with a prefix
  $uniqueName = uniqid('cart351Proj_');
   file_put_contents( 'json/'.$uniqueName.'.json', $str );
}

if($_SERVER['REQUEST_METHOD'] == 'POST' &&  isset($_POST['getData']) ){
  //  get all the files in this directoty ("cwd()") -> gives me the current working dir.
  // scandir() -> gives me all the files (names)
    $path    = getcwd().'/json';
    $files = scandir($path);
    $testArray =[];
  // go through the files and get contents
   for($i=0; $i<count($files);$i++){
     //echo($files[$i]);
     //open or read json data
     $data_results = file_get_contents('json/'.$files[$i]);
     //put into array (DECODE)
     $tempArray = json_decode($data_results);

     // append the contents to an "master array"
    $testArray[]= $tempArray;

   }
   //send the master array -. ALL the data back to javascript .. .
     $jsonData = json_encode($testArray);
     echo($jsonData);
}

?>
