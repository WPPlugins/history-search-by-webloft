<?php
// Viser treffliste SOM ENKEL LISTE
$treffliste = '';
$treffhtml = '';

require_once ('basenavn.php');
	
$pendel = 0;
$row = array('wlrowodd' , 'wlroweven');

$singlehtml = '<div class="pendelString">' . "\n";


$singlehtml .= '<div class="wlenkel-beskrivelse" style="float: left; width: 100%;">' . "\n";
$singlehtml .= '<a target="_blank" href="urlString">' . "\n";
$singlehtml .= "<b>nummerString. titleString</b><br>";
//$singlehtml .= "<h3 style=\"margin: 0; padding: 0; line-height: 1.1em;\">nummerString. titleString</h3>";
$singlehtml .= '</a>' . "\n";
$singlehtml .= "ansvarString ";
$singlehtml .= "<b>Kilde: </b>basenavnString. \n";
$singlehtml .= "descriptionString";


$singlehtml .= "<br><a title=\"Del på e-post\" href=\"maillenkeString\"><img class=\"wlenkel-some\" src=\"" . $litenmail . "\" alt=\"Send lenke på mail\" /></a>&nbsp;" . "\n";
$singlehtml .= '<a title="Del på Twitter" target="_blank" href="https://twitter.com/intent/tweet?url=urltwitString&via=kultursok&text=twitterString&related=bibvenn,sundaune&lang=no"><img class="wlenkel-some" src="' . $twittikon . '" alt="Twitter-deling" /></a>&nbsp;' . "\n";
$singlehtml .= "<a title=\"Del på Facebook\" target=\"_self\" href=\"javascript:fbShare('gotournString', 700, 350)\"><img class=\"wlenkel-some\" src=\"" . $faceikon . "\" alt=\"Facebook-deling\" /></a>&nbsp;" . "\n";

//$singlehtml .= '<a title="Direkte lenke til objektet" target="_self" href="urlString"><img class="wlenkel-some" src="' . $lenke . '" alt="Direkte lenke til objektet" />' . "\n";

$singlehtml .= '</div><br style="clear: both;"></div>' . "\n\n";

foreach ($treff as $treffkey => $enkelttreff) {

	$pendel = (1 - $pendel);

	@$outhtml = str_replace ("urlString" , $enkelttreff['url'] , $singlehtml);
	@$outhtml = str_replace ("titleString" , $enkelttreff['tittel'] , $outhtml);
	@$outhtml = str_replace ("titlealtString" , strip_tags($enkelttreff['tittel']) , $outhtml);
	@$outhtml = str_replace ("pendelString" , $row[$pendel] , $outhtml);
	@$outhtml = str_replace ("nummerString" , ($treffkey + 1), $outhtml);
	@$outhtml = str_replace ("bildeString" , $enkelttreff['bilde'] , $outhtml);

	@$outhtml = str_replace ("descriptionString" , trunc($enkelttreff['beskrivelse'], 50) , $outhtml);
	@$outhtml = str_replace ("basenavnString" , $enkelttreff['kilde'] , $outhtml);
	if ($enkelttreff['ansvar'] != '') {
		@$outhtml = str_replace ("ansvarString" , "(" . $enkelttreff['ansvar'] . ")" , $outhtml);
	} else {
		@$outhtml = str_replace ("ansvarString" , "" , $outhtml);
	}

	// Facebook og Twitter-lenker
	$fbtext = strip_tags($enkelttreff['beskrivelse']);
	$fbtext = htmlentities ($fbtext);

	$fbparams['tittel'] = strip_tags($enkelttreff['tittel']);
	$fbparams['beskrivelse'] = trunc ($fbtext, 100);
	$fbparams['url'] = $enkelttreff['url'];
	$fbparams['bilde'] = $enkelttreff['bilde'];
	$fbparams['ansvar'] = strip_tags($enkelttreff['ansvar']);
	$fbparamser = base64_encode (serialize($fbparams));
	$fburl = plugins_url('' , __FILE__) . "/gotourn.php?params=" . $fbparamser;

	$outhtml = str_replace ("gotournString" , $fburl , $outhtml);
	$outhtml = str_replace ("urltwitString" , urlencode(trim($enkelttreff['url'])) , $outhtml);

	$twitter = $enkelttreff['tittel'];
	if ((isset($enkelttreff['ansvar'])) && ($enkelttreff['ansvar'] != '')) {
		$twitter .= " - " . $enkelttreff['ansvar'];
	}
	$twitter = preg_replace('/[ \t]+/', ' ', preg_replace('/[\r\n]+/', "\n", $twitter)); //
	$twitter = str_replace ("<br>" , " " , $twitter);
	$twitter = strip_tags ($twitter);

	$twitter .= " %23kultursok";

	$outhtml = str_replace ("twitterString" , htmlspecialchars($twitter) , $outhtml);

	// Konvolutt

	$epostlenke = "mailto:?subject=Et lite tips fra Kultursøk&body=";
	$epostlenke .= "Hei!%0D%0A%0D%0A";
	$epostlenke .= "Jeg ville bare dele dette med deg:%0D%0A%0D%0A";
	$epostlenke .= "TITTEL: " . strip_tags(str_replace ("\"" , "'" , $enkelttreff['tittel'])) . "%0D%0A";
	$epostlenke .= "URL: " . urlencode($enkelttreff['url']) . "%0D%0A%0D%0A";
	$epostlenke .= "************************************************************************************%0D%0A";
	$epostlenke .= strip_tags(str_replace ("\"" , "'" , $enkelttreff['beskrivelse'])) . "%0D%0A";
	$epostlenke .= "************************************************************************************%0D%0A";
	$epostlenke .= "%0D%0A%0D%0A%0D%0A";
	$epostlenke .= "Du kan prøve ut Kultursøk, lese mer eller laste det ned på http://www.kultursøk.no%0D%0A%0D%0A%0D%0A";
	

	$outhtml = str_replace ("maillenkeString" , $epostlenke, $outhtml);



	// Ferdig med å lage HTML for ett treff - legger dette til riktig treffliste
	
	@$treffhtml .= $outhtml;

}

?>

<?= $treffhtml ?>

<a class="wl-tiltoppen" href="#wltoolbar">Til toppen</a>

