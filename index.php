<?php
/*
    Anon Ghost is a secure communication medium for those in liberty voids.
    Masks are generated automatically with the users details or with a
    mask-seed to create private identities so users can create trusted
    relationships.
    Check out http://anon.gho.st for a working version.
    Copyright (C) 2013 Gregology
    Version 0.1

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

session_start();

// Includes the settings from the config file
include("config.php");
include("functions.php");
include("plugins.php");

// Opens database connections
$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die ('Error connecting to mysql');
mysql_select_db($dbname);

// gets info for MySQL insert
$dumpersIP = ip_address_to_number($_SERVER['REMOTE_ADDR']);

// Converts $dumpduration into seconds
$dumpdurationsec = $dumpduration * 60;

// Works out the page numbers
$page = preg_replace('[\D]', '', $_GET['p']);
$poststart = $postsperpage * $page ;
$sqllimit = $poststart.", ".$postsperpage ;

// Next a pervious pages
$nextpage = $page + 1 ;
$prepage = $page - 1 ;

if ( $_SESSION['masknumber'] == "" ){
$ownmask = "Guy_Fawkes";
} else {
$ownmask = $_SESSION['masknumber'];
}

?>
<html>
  <head>
    <title><?php echo $title; ?></title>
    <script language="javascript" type="text/javascript">
    function limitText(limitField, limitCount, limitNum) {
      if (limitField.value.length > limitNum) {
        limitField.value = limitField.value.substring(0, limitNum);
      } else {
        limitCount.value = limitNum - limitField.value.length;
      }
    }
    </script>
    <style type="text/css">
      body {
        font-family: arial, verdana, sans-serif;
            background-color: #FEFEFE }
      a.shadowtexttitle:link {
          text-shadow: 2px 2px 1px rgba(0,0,0,0.4);
        font-size:300% ;
        float: center;
        text-decoration: none;
     }
      a.shadowtexttitle:vlink {
          text-shadow: 2px 2px 1px rgba(0,0,0,0.4);
        font-size:300% ;
        float: center;
        text-decoration: none;
      }
      .shadowtexttag {
          text-shadow: 1px 1px 1px rgba(0,0,0,0.4);
        font-size:100%
        float: center;
      }
      pre {
       white-space: pre-wrap;       /* css-3 */
       white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
       white-space: -pre-wrap;      /* Opera 4-6 */
       white-space: -o-pre-wrap;    /* Opera 7 */
       word-wrap: break-word;       /* Internet Explorer 5.5+ */
      }
    </style>
    <META HTTP-EQUIV="refresh" CONTENT="<?php echo $refreshrate; ?>">
  </head>

  <body>
    <a alt="Forget me" href="/forget.php"><img src="masks/<?php echo $ownmask; ?>.jpg" style="float:right;margin:0 5px 0 0;-moz-transform: scaleX(-1); -o-transform: scaleX(-1); -webkit-transform: scaleX(-1); transform: scaleX(-1); filter: FlipH; -ms-filter: 'FlipH';" /></a>

    <center>
      <b><a href="/" class="shadowtexttitle"><?php echo $title; ?></a></br>
      <font class="shadowtexttag"><?php echo $tagline; ?></font><br></b>
    </center>

    <center>
      <form name="post" action="post.php" method="POST">
      <form name="myform">
      <textarea style="width:95%;" rows="8" name="limitedtextarea" onKeyDown="limitText(this.form.limitedtextarea,this.form.countdown,<?php echo $textlength; ?>);" 
onKeyUp="limitText(this.form.limitedtextarea,this.form.countdown,<?php echo $textlength; ?>);"></textarea><br>
      <font size="1">
      You have <input readonly type="text" name="countdown" size="3" value="<?php echo $textlength; ?>"> characters left.</br>
      Mask seed (optional) <input type="password" value="<?php echo $_SESSION['maskseed']; ?>" name="maskseed" title="Mask Seed" size="16" maxlength="32" /><INPUT TYPE=SUBMIT VALUE="Post"></font><br>
      </form>
    </center>
<?php

// creates extra query for hash and mask tags
// Gets hash and mask data
$hash = mysql_real_escape_string($_GET['h']);
$mask = mysql_real_escape_string($_GET['m']);

// Checks hash
if ($hash == "") {
  // do nothing
} else {
  $hashsearch = "`posttext` REGEXP  '#".$hash."' ";
  // also creates hashurl
  $hashtagurl = "&h=".$hash;
  $wherestatement = "WHERE ".$hashsearch." ";
}

// Checks mask
if ($mask == "") {
  // do nothing
} else {
  $masksearch = "`masknumber` =  '".$mask."' ";
  // also creates hashurl
  $masktagurl = "&m=".$mask;
  $wherestatement = "WHERE ".$masksearch." ";
}

// Creates where statement if mask AND hash exist
if ($hash != "" AND $mask != "") {
  $wherestatement = "WHERE ".$hashsearch." AND ".$masksearch." ";
}

// creates longer select statement
$sql = 'SELECT *  FROM `postview` '.$wherestatement.'ORDER BY `timestamp` DESC LIMIT '.$sqllimit.'';
// queries sql db
$query = mysql_query($sql);
while($row = mysql_fetch_array($query)) {
$textsize = round( ((( $textlength - strlen($row['posttext']) ) / $textlength ) * ( 14 - 4 )) + 4 ) ;

echo '
    <table width="100%" border="0">
      <tr>
        <td colspan="2">
          <hr>
        </td>
      </tr>
      <tr valign="top">
        <td style="width:140px;text-align:top;">
          <a href="/?m='.$row['masknumber'].'"><img src="https://anon.gho.st/masks/'.$row['masknumber'].'.'.$filetype.'" width="'.$width.'" height="'.$height.'"></a><br><font size="1">posted '.$row['sincetime'].' ago.</font>
        </td>
      <td style="width:100%;text-align:top;font-size:'.$textsize.'px;">
        <PRE>'.addhashtags(htmlentities($row['posttext'])).'</PRE>
      </tr>
    </table>';
}
?>
    <br>
    <a href="/?p=<?php echo $nextpage."".$hashtagurl.$masktagurl; ?>">Older posts</a>
    <hr size=2 color='#"555"'>
      <div align='center'>
        <font size="1" color="#888">This service is brought to you by <a href="http://gho.st">Gho.st community ISP</a>. Engine is open source and avaliable on <a href="https://github.com/GhostWeb/AnonGhost">GitHub</a>. The original Guy Fawkes image was created by <a href="http://openclipart.org/user-detail/rones">Rones</a>. Help develop this engine and join the anonymous conversation. Please respect intellectual property. Enjoy!
        </font>
      </div>
  </body>
</html>
