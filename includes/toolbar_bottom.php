<?php
/*******************************************************
Viser verktøylinje under søkeresultater
********************************************************/

// Del søket - gjøre klart alt

$prefix  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$serverurl = $prefix . $_SERVER['SERVER_NAME'];
$heleurl = $serverurl . htmlspecialchars($_SERVER['REQUEST_URI']);

// Forkorte med bitly??

	if (($wl_bitly_login != '') && ($wl_bitly_key != '')) { // angitt i settings
		$heleurl = wl_make_bitly_url($heleurl,$wl_bitly_login,$wl_bitly_key, 'txt');
	}

$bilde = plugins_url ('../g/webloft_logo.jpg' , __FILE__);
$fbquery = stripslashes(strip_tags(str_replace("\\\"" , "" , $_REQUEST['lokalhistquery'])));
$twiturl = rawurlencode($heleurl);
$twiturl = str_replace ("%26amp%3B" , "%26" , $twiturl);

$twitterstring = htmlspecialchars("Treffliste ved søk etter '" . urlencode(stripslashes(strip_tags($_REQUEST['lokalhistquery']))) . "' i Webløft %23kultursok");

$fbparams['tittel'] = "Treffliste ved søk etter '" . $fbquery . "' i Webløft Kultursøk";
$fbparams['beskrivelse'] = htmlentities("Webløft Kultursøk lar deg søke i en mengde norske kilder etter lokalhistorisk materiale og kulturminner.");

// Forkorte med bitly??

	if (($wl_bitly_login != '') && ($wl_bitly_key != '')) { // angitt i settings
		$fbparams['url'] = wl_make_bitly_url($_SERVER['REQUEST_URI'],$wl_bitly_login,$wl_bitly_key, 'txt');
	} else {
		$fbparams['url'] = $_SERVER['REQUEST_URI'];
	}

$fbparams['url'] = $heleurl;

$fbparams['bilde'] = $bilde;
$fbparams['ansvar'] = "Webløft";

$fbparamser = base64_encode (serialize($fbparams));
$fburl = plugins_url('' , __FILE__) . "/gotourn.php?params=" . $fbparamser;

// Konvolutt

$epostlenke = "mailto:?subject=Et lite tips fra Kultursøk&body=";
$epostlenke .= "Hei!%0D%0A%0D%0A";
$epostlenke .= "Jeg ville bare dele dette med deg:%0D%0A";
$epostlenke .= urlencode($heleurl) . "%0D%0A%0D%0A";
$epostlenke .= "BESKRIVELSE: " . "Treffliste ved søk etter '" . $fbquery . "' i Webløft Kultursøk";
$epostlenke .= "%0D%0A%0D%0A";
$epostlenke .= "************************************************************************************%0D%0A";
$epostlenke .= "Du kan prøve ut Kultursøk, lese mer eller laste det ned på http://www.kultursøk.no%0D%0A";
$epostlenke .= "************************************************************************************%0D%0A%0D%0A";

// Og så skrive ut ikoner

echo '<br>Del trefflisten:<br>';
echo '<a title="Del på Twitter" target="_blank" href="https://twitter.com/intent/tweet?url=' . $twiturl . '&amp;via=kultursok&amp;text=' . $twitterstring . '&amp;related=bibvenn,sundaune,kultursok&amp;lang=no"><img class="wlutvidet-some" src="' . $litentwitt . '" alt="Del s&oslash;ket p&aring; Twitter" /></a>&nbsp;' . "\n";
echo "&nbsp;";
echo "<a title=\"Del på Facebook\" target=\"_self\" href=\"javascript:fbShare('" . $fburl . "', 700, 350)\"><img class=\"wlutvidet-some\" src=\"" . $litenface . "\" alt=\"Facebook-deling\" /></a>" . "\n";
echo "<a title=\"Del på e-post\" href=\"" . $epostlenke . "\"><img class=\"wlutvidet-some\" src=\"" . $litenmail . "\" alt=\"Send lenke på mail\" /></a>" . "\n";

// Fortsett søket
$keepurl = '';

include_once ("basenavn.php");

$denneurl = wlbrekkurl(); // se functions.php
foreach ($denneurl[1] as $key => $value) {
	if ($key == "baser") {
		$thisbaser = str_replace ("%2C" , "," , $value);
		$thisbaser = str_replace ("+" , "" , $thisbaser);
		$thisbaser = str_replace ("%20" , "" , $thisbaser);
		$thisbaser = explode ("," , $thisbaser);
	}
} 

// Sortere treff på slug slik at vi vet hvilke knapper å vise (bare de med treff)

$rodeknapperhtml = '';

foreach ($GLOBALS['basenavn'] as $enbasenavn) {
	$enbase = explode ("|x|" , $enbasenavn);
	$slug = $enbase[0];
	$navn = $enbase[1];
	$url = $enbase[3];
	foreach ($thisbaser as $enthisbaser) {
		if ((trim($enthisbaser) == $slug) && ($treffinfo['antalltreff'][$slug] > $makstreff)) {
			$keepurl = str_replace ("<!QUERY!>" , $search_string , $url);
			$keepnavn = $navn;
			// Orker ikke å gjøre dette elegant
			$hitme = $treffinfo['antalltreff'][$slug];
			if ($hitme > 1) { $repr = "1+"; }
			if ($hitme > 10) { $repr = "10+"; }
			if ($hitme > 100) { $repr = "100+"; }
			if ($hitme > 1000) { $repr = "1000+"; }
			if ($hitme > 10000) { $repr = "10000+"; }
			if ($hitme > 100000) { $repr = "100000+"; }

			// Men nå droppet vi avrundingen, så vi bruker bare $hitme direkte
		
			$rodeknapperhtml .= '<button class="rundknapp" onclick="window.open(' . "'" . $keepurl . "')" . '">' . $keepnavn . ' (' . $hitme . ' treff)</button>' . "\n";

		}
	}
}
if (($rodeknapperhtml != '') && ($pagination->get_page() == $pagination->get_pages())) {
	echo '<div class="wl-katalog-toolbar-bottom">' . "\n";
	echo 'Viser <b>' . count($treff) . '</b> av totalt <b>' . array_sum($treffinfo['antalltreff']) . '</b> treff i <b>' . $antallbasermedtreff . '</b> baser. S&oslash;kte totalt i <b>' . count($treffinfo['antalltreff']) . '</b> baser. ';
	echo '<b>Se flere treff:</b><br>' . "\n";
	echo $rodeknapperhtml;
	echo '</div>' . "\n\n";
}

?>

