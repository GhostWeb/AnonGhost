<?php
function makelinks($text,$RGB) {
$text = preg_replace("
  #((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie",
  "'<a style=\"color:#$RGB\" href=\"$1\" target=\"_blank\">$1</a>$4'",
  $text
);
return $text;
}

function addhashtags($text) {
$text = preg_replace('/#([\\d\\w]+)/', '<a href="/?h=$1">$0</a>', $text);
return $text;
}

function ip_address_to_number($IPaddress) { 
 if(!$IPaddress) {
  return false;
 } else {
  $ips = split('\.',$IPaddress);
  return($ips[3] + $ips[2]*256 + $ips[1]*65536 + $ips[0]*16777216);
 }
}

function HSVtoRGB(array $hsv) {
	list($H,$S,$V) = $hsv;
	//1
	$H *= 6;
	//2
	$I = floor($H);
	$F = $H - $I;
	//3
	$M = $V * (1 - $S);
	$N = $V * (1 - $S * $F);
	$K = $V * (1 - $S * (1 - $F));
	//4
	switch ($I) {
		case 0:
			list($R,$G,$B) = array($V,$K,$M);
			break;
		case 1:
			list($R,$G,$B) = array($N,$V,$M);
			break;
		case 2:
			list($R,$G,$B) = array($M,$V,$K);
			break;
		case 3:
			list($R,$G,$B) = array($M,$N,$V);
			break;
		case 4:
			list($R,$G,$B) = array($K,$M,$V);
			break;
		case 5:
		case 6: //for when $H=1 is given
			list($R,$G,$B) = array($V,$M,$N);
			break;
	}
	// converts to HEX
	$R = dechex($R * 255);
	$G = dechex($G * 255);
	$B = dechex($B * 255);
	// Adds leading zeros where needed
	if ( strlen($R) == 1) {
	$R = "0".$R;
	}
	if ( strlen($G) == 1) {
	$G = "0".$G;
	}
	if ( strlen($B) == 1) {
	$B = "0".$B;
	}
	$RGB = $R.$G.$B;
	return $RGB;
}
?>