<?php
error_reporting(E_ERROR); // Vi vil bare ha beskjed hvis det går skikkelig, skikkelig åt skogen

$prefix  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$serverurl = $prefix . $_SERVER['SERVER_NAME'];
$heleurl = $serverurl . htmlspecialchars($_SERVER['REQUEST_URI']);
$parturl = stristr ($heleurl , "search.php" , TRUE);

//header('Content-Type: application/xhtml+xml');
header('Content-Type: application/rdf+xml');

/*
*
* Nå må vi lage feeden
*
*/

$feedtittel = "RSS-feed fra Webløft Kultursøk, totalt " . count($treff) . " treff";

$feed = "";
$feed .= "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
$feed .= "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n";
$feed .= "<channel>\n";
$feed .= "<atom:link href=\"" . $serverurl . str_replace ("&" , "&amp;" , $_SERVER['REQUEST_URI']) . "\" rel=\"self\" type=\"application/xhtml+xml\" />\n";
$feed .= "<title><![CDATA[Webløft Kultursøk]]></title>\n";
$feed .= "<link>http://www.kultursok.no</link>\n";
$feed .= "<description><![CDATA[WL Kultursøk - en Wordpress-utvidelse fra Webløft]]></description>\n";
$feed .= "<lastBuildDate>" . date("r") . "</lastBuildDate>\n";
$feed .= "<language>no</language>\n";

$feed .= "<image>\n";
$feed .= "<title><![CDATA[Webløft Kultursøk]]></title>\n";
$feed .= "<url>" . $parturl . "g/webloft_rss.jpg</url>\n";
$feed .= "<width>140</width>\n";
$feed .= "<height>136</height>\n";
$feed .= "<link>http://www.kultursok.no</link>\n";
$feed .= "</image>\n";


foreach ($treff as $enkelttreff) { // Her kommer hvert item

	$feed .= "<item>\n";
	$feed .= "<title><![CDATA[" . str_replace ("<br>" , " : " , $enkelttreff['tittel']) . "]]></title>\n";


	$feed .= "<description><![CDATA[<img width=\"200\" hspace=\"5\" vspace=\"5\" align=\"left\" src=\"" . $enkelttreff['bilde'] . "\" alt=\"" . str_replace ("<br>" , " : " , $enkelttreff['tittel']);
	$feed .= "\" />";

	$riktigdato = date("r", strtotime($enkelttreff['digidato']));	

//	$tempbesk = str_replace ("&" , "&amp;" , $enkelttreff['beskrivelse']);
//	$tempbesk = str_replace ("<" , "&lt;" , $enkelttreff['beskrivelse']);
//	$tempbesk = str_replace (">" , "&gt;" , $tempbesk);
//	$tempbesk = str_replace ("]" , "&#93;" , $tempbesk);
//	$tempbesk = str_replace ("[" , "&#91;" , $tempbesk);
	$tempbesk = str_replace ('"' , '&#34;' , $enkelttreff['beskrivelse']);
	$tempbesk = str_replace ('«' , '"' , $tempbesk);
	$tempbesk = str_replace ('»' , '"' , $tempbesk);
	$tempbesk = str_replace ("’" , "'" , $tempbesk);
	$tempbesk = str_replace ("…" , "..." , $tempbesk);
	
	$feed .= $tempbesk . " <b>Kilde: </b>" . $enkelttreff['kilde'] . ".]]></description>\n";

	$feed .= "<link><![CDATA[" . htmlentities($enkelttreff['url']) . "]]></link>\n";
	$feed .= "<guid><![CDATA[" . htmlentities($enkelttreff['url']) . "]]></guid>\n";
	$feed .= "<pubDate><![CDATA[" . $riktigdato . "]]></pubDate>\n";

	$feed .= "<category>" . $enkelttreff['kilde'] . "</category>\n";
	$feed .= "</item>\n";

	unset ($tempbesk , $riktigdato);
}	// Slutt på hvert item

$feed .= "</channel>\n"; 
$feed .= "</rss>";

echo $feed;
?>
