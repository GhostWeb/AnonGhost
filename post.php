<?php
session_start();

// Bad-bahavior script
$path_to_bb = '/var/www/bad-behavior';
require_once("$path_to_bb/bad-behavior-generic.php");

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
$color1 = substr(md5(sha1($extrasalt.$postersID.$extrasalt)), 14, -12);
$color2 = substr(md5(sha1($extrasalt.$color1.$postersID.$extrasalt)), 14, -12);
$color3 = substr(md5(sha1($extrasalt.$color2.$postersID.$extrasalt)), 14, -12);
$masknumber = $color1.$color2.$color3 ;
} else {
// If mask seed exists generates mask
$masknumber = substr(md5(sha1($extrasalt.$maskseed.$extrasalt)), 8, -6);
$_SESSION['maskseed'] = $maskseed;
}

// Mask number for own mask display
$_SESSION['masknumber'] = $masknumber;

if($posttext == ""){
// Nothing to input
} else {
// checks dump limits
$sql = 'SELECT masknumber, COUNT(masknumber) FROM `posts` WHERE masknumber = "'.$masknumber.'" AND timestamp > NOW() - INTERVAL '.$limitperiod.' MINUTE';
$query = mysql_query($sql);
while($row = mysql_fetch_array($query)) {
if( $row['COUNT(dumpersIP)'] > $dumplimit ) {
// if dump limits exceded it creates error message for display below
echo "<font size='3' color='red'>Post limited exceded from your mask</font><br>";
exit;
} else {
// if dump limit not exceded it enters the dump
$query_insert  ="INSERT INTO posts (masknumber,posttext) VALUES ('$masknumber','$posttext')" ;
$result=mysql_query ( $query_insert);
if(!$result){
die(mysql_error());
}
}
}
}

/* not needed anymore
if (file_exists("masks/".$masknumber.".".$filetype)) {
	// Does nothing
} else {
	// Makes mask
	include("makemask.php");
	// Will include if masks not generating quickly enough
#	sleep(1);
}
*/

header("Location: /");

?>
