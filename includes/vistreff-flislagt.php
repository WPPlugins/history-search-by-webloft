<?php

// Viser treffliste FLISLAGT
$treffliste = '';
$treffhtml = '';

require_once ('basenavn.php');

// HVERT TREFF: slug, url, bilde, tittel, ansvar, beskrivelse, basenavn

// MAL FOR HVERT TREFF: classString , urlString , omslagString, titleString, descriptionString, urltwitString, twitterdescriptionString, gotournString, basenavnString

$singlehtml = '<div class="grid-item">' . "\n";
$singlehtml .= '<a target="_blank" href="urlString">' . "\n";
$singlehtml .= "<img src=\"omslagString\" alt=\"illustrasjonsbilde\" />\n";
$singlehtml .= "defaultOmslag\n";
$singlehtml .= "<div class=\"gridinfotekst\">\n";
$singlehtml .= "<h3>titleString</h3>\n";
$singlehtml .= "<h4>ansvarString</h4><br>\n";
$singlehtml .= "descriptionString\n";
$singlehtml .= "<br><b>Kilde: </b>basenavnString\n";
$singlehtml .= "</div>\n";
$singlehtml .= '</a>' . "\n";
$singlehtml .= '</div>' . "\n";

foreach ($treff as $enkelttreff) {

	$thisslug = $enkelttreff['slug'];

	@$outhtml = str_replace ("omslagString" , $enkelttreff['bilde'] , $singlehtml);
	@$outhtml = str_replace ("urlString" , $enkelttreff['url'] , $outhtml);
	@$outhtml = str_replace ("titleString" , $enkelttreff['tittel'] , $outhtml);
	@$outhtml = str_replace ("ansvarString" , $enkelttreff['ansvar'] , $outhtml);
	@$outhtml = str_replace ("descriptionString" , trunc($enkelttreff['beskrivelse'], 50) , $outhtml);
	@$outhtml = str_replace ("basenavnString" , $enkelttreff['kilde'] , $outhtml);
	
	if (stristr($enkelttreff['bilde'] , "lokalhistoriewiki.jpg")) {
		@$outhtml = str_replace ('defaultOmslag' , '<div class="defaultomslag">' . $enkelttreff['tittel'] . '</div>', $outhtml);
	} else {
		@$outhtml = str_replace ('defaultOmslag' , '', $outhtml);
	}

	// Ferdig med å lage HTML for ett treff - legger dette til riktig treffliste
	
	if ($enkelttreff['bilde'] != '') { // à propos... har vi egentlig bilde?
		@$treffhtml .= $outhtml;
	}
}

?>

<div class="grid">

<?= $treffhtml ?>

</div>

<a class="wl-tiltoppen" href="#wltoolbar">Til toppen</a>
