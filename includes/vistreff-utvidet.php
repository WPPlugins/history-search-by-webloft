<?php

// Viser treffliste i utvidet form, med FB/Twitter-knapper
$treffliste = '';
$treffhtml = '';
$pendel = 0;

$row = array('wlrowodd' , 'wlroweven');

// HVERT TREFF: slug, url, bilde, tittel, ansvar, beskrivelse

// MAL FOR HVERT TREFF: classString , urlString , omslagString, titleString, descriptionString, urltwitString, twitterdescriptionString, gotournString

$singlehtml = '';

$singlehtml = '<div class="pendelString">' . "\n";
$singlehtml .= "<div class=\"wlutvidet-omslag\">\n";
$singlehtml .= "<a href=\"urlString\" target=\"_blank\">";
$singlehtml .= "<img src=\"omslagString\" alt=\"illustrasjonsbilde\" />\n";
$singlehtml .= "</a>" . "\n";
$singlehtml .= "</div>" . "\n";

$singlehtml .= "<div class=\"wlutvidet-beskrivelse\">\n";
$singlehtml .= "<a href=\"urlString\" target=\"_blank\">" . "\n";
$singlehtml .= "<h3>nummerString. titleString</h3>\n";
$singlehtml .= "</a>\n";
$singlehtml .= "ansvarString" . "\n";
$singlehtml .= "descriptionString</b>" . "\n"; // </b> for å demme opp for at <b> noen ganger blir trunkert midt inni
$singlehtml .= "<br><b>Kilde: </b>kildeString\n";

$singlehtml .= '<br>';
$singlehtml .= '<div style="text-align: right;">' . "\n";
$singlehtml .= '<a title="Del på Twitter" target="_blank" href="https://twitter.com/intent/tweet?url=urltwitString&via=kultursok&text=twitterString&related=bibvenn,sundaune&lang=no"><img class="wlutvidet-some" src="' . $litentwitt . '" alt="Twitter-deling" /></a>&nbsp;' . "\n";
$singlehtml .= "<a title=\"Del på Facebook\" target=\"_self\" href=\"javascript:fbShare('gotournString', 700, 350)\"><img class=\"wlutvidet-some\" src=\"" . $litenface . "\" alt=\"Facebook-deling\" /></a>&nbsp;" . "\n";
$singlehtml .= "<a title=\"Del på e-post\" href=\"maillenkeString\"><img class=\"wlutvidet-some\" src=\"" . $litenmail . "\" alt=\"Send lenke på mail\" /></a>" . "\n";
$singlehtml .= '</div>' . "\n";

//$singlehtml .= '<a title="Direkte lenke til objektet" target="_self" href="urlString"><img class="wlutvidet-some" src="' . $lenke . '" alt="Direkte lenke til objektet" />' . "\n";

$singlehtml .= '</div><br style="clear: both;"></div>' . "\n\n";

foreach ($treff as $treffkey => $enkelttreff) {

	$pendel = (1 - $pendel);

	@$outhtml = str_replace ("classString" , $enkelttreff['slug'] , $singlehtml);
	@$outhtml = str_replace ("nummerString" , ($treffkey + 1) , $outhtml);
	@$outhtml = str_replace ("urlString" , $enkelttreff['url'] , $outhtml);
	@$outhtml = str_replace ("pendelString" , $row[$pendel] , $outhtml);
	@$outhtml = str_replace ("omslagString" , $enkelttreff['bilde'] , $outhtml);
	@$outhtml = str_replace ("titleString" , $enkelttreff['tittel'] , $outhtml);
	if ((isset($enkelttreff['ansvar'])) && ($enkelttreff['ansvar'] != '')) {
		$outhtml = str_replace ("ansvarString" , "<h4>" . $enkelttreff['ansvar'] . "</h4>", $outhtml);
	} else {
		$outhtml = str_replace ("ansvarString" , '' , $outhtml);
	}
	$outhtml = str_replace ("descriptionString" , trunc($enkelttreff['beskrivelse'] , 100) , $outhtml);
	$outhtml = str_replace ("kildeString" , $enkelttreff['kilde'] , $outhtml);

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

	// Facebook og Twitter-lenker
	$fbtext = strip_tags($enkelttreff['beskrivelse']);
	$fbtext .= " Kilde: " . $enkelttreff['kilde'] . ".";
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



	// Ferdig med å lage HTML for ett treff - legger dette til treffliste
	@$treffhtml .= $outhtml;

}

?>

<?= $treffhtml ?>

<a class="wl-tiltoppen" href="#wltoolbar">Til toppen</a>
