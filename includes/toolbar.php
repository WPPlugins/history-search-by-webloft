<?php
/*******************************************************
Viser verktøylinje over søkeresultater
********************************************************/

$denneurl = wlbrekkurl(); // se functions.php

?>

<div class="wl-katalog-toolbar">
	
	<form method="GET" action="<?= $denneurl[0]; ?>">
<?php
if (is_array($denneurl[1])) {
	foreach ($denneurl[1] as $parameter => $value) {
		echo "<input type=\"hidden\" name=\"" . $parameter . "\" value=\"" . str_replace ("\"" , "&quot;" , urldecode($value)) . "\" />";
	}
}
?>

	<div class="wl-katalog-toolbar-item">
		<div class="wl-katalog-toolbar-item-header">Rekkef&oslash;lge</div>
		<div class="wl-katalog-toolbar-item-body">
			<select class="wl-katalog-toolbar-item-body" onchange="wlshowspinner();this.form.submit()" name="sortering">
				<option value="base"<?php if ($sortering == "base") { echo " selected"; } ?>>Etter kilde</option>
				<option value="tittel_asc"<?php if ($sortering == "tittel_asc") { echo " selected"; } ?>>Tittel A-Å</option>
				<option value="tittel_desc"<?php if ($sortering == "tittel_desc") { echo " selected"; } ?>>Tittel Å-A</option>
				<option value="digidato_asc"<?php if ($sortering == "digidato_asc") { echo " selected"; } ?>>Reg.-dato &uarr;</option>
				<option value="digidato_desc"<?php if ($sortering == "digidato_desc") { echo " selected"; } ?>>Reg.-dato &darr;</option>
				<option value="dato_asc"<?php if ($sortering == "dato_asc") { echo " selected"; } ?>>Dato &uarr;</option>
				<option value="dato_desc"<?php if ($sortering == "dato_desc") { echo " selected"; } ?>>Dato &darr;</option>
				<option value="ansvar_asc"<?php if ($sortering == "ansvar_asc") { echo " selected"; } ?>>Opphav A-Å</option>
				<option value="ansvar_desc"<?php if ($sortering == "ansvar_desc") { echo " selected"; } ?>>Opphav Å-A</option>
				<option value="tilfeldig"<?php if ($sortering == "tilfeldig") { echo " selected"; } ?>>Tilfeldig</option>
			</select>
		</div>
	</div>

	<div class="wl-katalog-toolbar-item">
		<div class="wl-katalog-toolbar-item-header">Materialtype</div>
		<div class="wl-katalog-toolbar-item-body">
			<select class="wl-katalog-toolbar-item-body" onchange="wlshowspinner();this.form.submit()" name="materialavgrensning">
			<option value=""<?php if ($materialavgrensning == "") { echo " selected"; } ?>>Alle typer</option>
<?php

	// ALLE MATERIALTYPER MÅ LIGGE HER!!
	$materialtyper = array ("bilde" => "Bilder", "bok" => "B&oslash;ker", "tekst" => "Tekst", "gjenstand" => "Gjenstander", "manuskript" => "Manuskripter", "lyd" => "Lyd", "artikkel" => "Artikler", "video" => "Videoer");

	foreach ($materialtyper as $key => $value) {
		if ($treffinfo['materialtreff'][$key] > 0) { // må ha treff i materialtypen for at den skal dukke opp i menyen
			echo '<option value="' . $key . '"';
			if ($materialavgrensning == $key) { echo " selected"; }
			echo '>' . $value . ' (' . $treffinfo['materialtreff'][$key] . ' treff)</option>' . "\n";
		}
	}

?>
			</select>
		</div>
	</div>

	<div class="wl-katalog-toolbar-item">
		<div class="wl-katalog-toolbar-item-header">Visning</div>
		<div class="wl-katalog-toolbar-item-body">
			<select class="wl-katalog-toolbar-item-body" onchange="wlshowspinner();this.form.submit()" name="visning">
				<option value="utvidet"<?php if ($visning == "utvidet") { echo " selected"; } ?>>Utvidet liste</option>
				<option value="enkelliste"<?php if ($visning == "enkelliste") { echo " selected"; } ?>>Enkel liste</option>
				<option value="flislagt"<?php if ($visning == "flislagt") { echo " selected"; } ?>>Flislagt</option>
				<option value="karusell"<?php if ($visning == "karusell") { echo " selected"; } ?>>Karusell</option>
				<option value="rss"<?php if ($visning == "rss") { echo " selected"; } ?>>Lenke til RSS-feed</option>
			</select>
		</div>
	</div>

	<div class="wl-katalog-toolbar-item">
		<div class="wl-katalog-toolbar-item-header">Base</div>
		<div class="wl-katalog-toolbar-item-body">
			<select class="wl-katalog-toolbar-item-body" onchange="wlshowspinner();this.form.submit()" name="baseavgrens">
				<option value=""<?php if ($baseavgrens == "") { echo " selected"; } ?>>Alle med treff</option>
<?php 
// alle baser som er valgt i søket listes opp som avgrensningsmulighet slugtilbase(slug)

$antallbasermedtreff = 0;
$basefil = plugin_dir_path (__FILE__) . "basenavn.php";
require_once ($basefil);
global $basenavn;
$basemuligheter = explode ("," , $baser);
foreach ($basemuligheter as $enkeltbase) {
	$enkeltbase = trim($enkeltbase);
	if ($treffinfo['antalltreff'][$enkeltbase] > 0) { // Må ha treff for å vise denne i listen
		$antallbasermedtreff++;
		echo ('<option value="' . $enkeltbase . '"');
		if ($baseavgrens == $enkeltbase) { echo ' selected'; }
		echo ('>' . slugtilbase($enkeltbase) . '</option>' . "\n");

//		echo ('>' . slugtilbase($enkeltbase) . ' (' . $treffinfo['antalltreff'][$enkeltbase] . ' treff)</option>' . "\n"); // Med trefftall listet opp bak hver base

	}
}

?>
			</select>
		</div>
	</div>

	</form>

<?php
	echo 'Viser <b>' . count($treff) . '</b> av totalt <b>' . array_sum($treffinfo['antalltreff']) . '</b> treff i <b>' . $antallbasermedtreff . '</b> baser. S&oslash;kte totalt i <b>' . count($treffinfo['antalltreff']) . '</b> baser. ';


/*
	KODESNUTT SOM LISTER OPP MATERIALTYPENE MED ANTALL TREFF

	$materialhtml = '';
	foreach ($treffinfo['materialtreff'] as $key => $value) {
		$materialhtml[] = "<b>" . $key . "</b> (" . $value . ")";
	}
	echo implode ($materialhtml , ", ");
*/
?>


<?php
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

echo 'Del trefflisten:&nbsp;';
echo '<a title="Del på Twitter" target="_blank" href="https://twitter.com/intent/tweet?url=' . $twiturl . '&amp;via=kultursok&amp;text=' . $twitterstring . '&amp;related=bibvenn,sundaune,kultursok&amp;lang=no"><img class="wlutvidet-some" src="' . $litentwitt . '" alt="Del s&oslash;ket p&aring; Twitter" /></a>&nbsp;' . "\n";
echo "&nbsp;";
echo "<a title=\"Del på Facebook\" target=\"_self\" href=\"javascript:fbShare('" . $fburl . "', 700, 350)\"><img class=\"wlutvidet-some\" src=\"" . $litenface . "\" alt=\"Facebook-deling\" /></a>" . "\n";
echo "<a title=\"Del på e-post\" href=\"" . $epostlenke . "\"><img class=\"wlutvidet-some\" src=\"" . $litenmail . "\" alt=\"Send lenke på mail\" /></a>" . "\n";

?>

</div>

