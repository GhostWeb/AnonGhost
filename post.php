<?php
session_start();

// checks for no lanugage spam
$language = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ; //language code
if($language == ""){
  die ('Error connecting to mysql');
}

/*
if (strpos($dumpedtext, 'http') !== true) {
die ('No links because of naughty spam bots :(');
}
*/

// Includes the settings from the config file
include("config.php");
include("functions.php");
include("plugins.php");

// Opens database connections
$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die ('Error connecting to mysql');
mysql_select_db($dbname);

// gets info for MySQL insert
$postersID = $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'];
$posttext = mysql_real_escape_string($_POST['limitedtextarea']);
$maskseed = mysql_real_escape_string($_POST['maskseed']);

// Check for mask seed
if($maskseed == ""){
  // If no mask seed use postersID to generate mask
  $color1 = substr(md5(hash('sha512', $bacon.$postersID.$bacon)), 14, -12);
  $color2 = substr(md5(hash('sha512', $bacon.$color1.$postersID.$bacon)), 14, -12);
  $color3 = substr(md5(hash('sha512', $bacon.$color2.$postersID.$bacon)), 14, -12);
  $masknumber = $color1.$color2.$color3 ;
  } else {
  // If mask seed exists generates mask
  $masknumber = substr(md5(hash('sha512', $bacon.$maskseed.$bacon)), 8, -6);
  $_SESSION['maskseed'] = $maskseed;
}

// Mask number for own mask display
$_SESSION['masknumber'] = $masknumber;

// Creates Floodhash
$floodhash = md5(hash('sha512', $pretzels.$postersID.( round ( time() / $limitperiod ) * $limitperiod )));

if($posttext == ""){
// Nothing to input
} else {
  // updates dump limit table
  $sql = "
    INSERT INTO floodlimit (hash) VALUES ('$floodhash')
    ON DUPLICATE KEY UPDATE count = count + 1";
  mysql_query ($sql);
  // checks dump limits
  $sql = "
    SELECT count FROM floodlimit WHERE hash = '$floodhash'";
  $query = mysql_query($sql);
  while($row = mysql_fetch_array($query)) {
    if( $row['count'] > $postlimit ) {
    // if dump limits exceded it creates error message for display below
      echo "<font size='3' color='red'>Post limited exceded from your hashed (ip + rounded timestamp + salt)</font><br>";
      exit;
      } else {
      // if dump limit not exceded it enters the dump
      $query_insert = "
        INSERT INTO posts (masknumber,posttext) VALUES ('$masknumber','$posttext')" ;
      $result = mysql_query($query_insert);
      if(!$result){
        die(mysql_error());
      }
    }
  }
}

header("Location: /");

?>
