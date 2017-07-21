<?php

$wldebug = "0"; // sett til 1 for debug

if (isset($_REQUEST['debug'])) {
	$wldebug = 1;
}

$time_start = microtime(true); 

// turn on for debug
$wl_error = error_reporting(); // ta vare på gjeldende feilrapporteringsnivå
error_reporting(E_ERROR);

/*
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);
*/

// Hvis vi kommer direkte hit trenger vi WP-funksjonalitet!
if ( ! defined( 'WPINC' ) ) {
	require_once("../../../wp-load.php");
}

require_once("includes/functions.php"); // funksjoner vi har bruk for

// Declare variables

$treff = '';
$tittel = '';
$forfatter = '';
$dc = '';

// get options

$wl_bruk_cache = (int) get_option('wl_bruk_cache' , '1');
$wl_uthev_term = (int) get_option('wl_uthev_term' , '1');
$wl_bitly_login = get_option('wl_bitly_login' , '');
$wl_bitly_key = get_option('wl_bitly_key' , '');
$wl_flickr_api_key = get_option('wl_flickr_api_key' , '');
$wl_youtube_api_key = get_option('wl_youtube_api_key' , '');

// Men hvis debug bruker vi aldri cache
if ($wldebug == "1") {
	$wl_bruk_cache = "0";
}

if (isset($_REQUEST['baser'])) {
	// ABSOLUTT ALLE-hack: Tar med absolutt alle installerte baser hvis hemmelig kode oppgis i URL
	if ($_REQUEST['baser'] == 'kulturmegapack') {
		$baseray = '';
		include ('includes/basenavn.php');
		foreach ($basenavn as $enbase) {
			$baseray[] = stristr ($enbase , "|x|" , TRUE);
		}
		$baser = implode ("," , $baseray);

	} else { // ikke kulturmegapack

		$baser = esc_attr($_REQUEST['baser']);
		$baser = str_replace ("+" , "" , $baser); // Ikke mellomrom
		$baser = str_replace (" " , "" , $baser); // IKKE mellomrom, sa jeg

	}

} else { $baser = ''; } // ingen baser angitt i URL

if (isset($_REQUEST['visning'])) {
$visning = esc_attr($_REQUEST['visning']);
} else { $visning = 'utvidet'; }

if (isset($_REQUEST['sortering'])) {
$sortering = esc_attr($_REQUEST['sortering']);
} else { $sortering = ''; }

if (isset($_REQUEST['materialavgrensning'])) {
$materialavgrensning = esc_attr($_REQUEST['materialavgrensning']);
} else { $materialavgrensning = ''; }

if (isset($_REQUEST['makstreff'])) {
$makstreff = (int) $_REQUEST['makstreff'];
} else { $makstreff = ''; }

if (isset($_REQUEST['wltreffperside'])) {
$wltreffperside = (int) $_REQUEST['wltreffperside'];
} else { $wltreffperside = '25'; }

if (isset($_REQUEST['baseavgrens'])) {
$baseavgrens = esc_attr($_REQUEST['baseavgrens']);
} else { $baseavgrens = ''; }

if (isset($_REQUEST['wlgjemverktoyover'])) {
$wlgjemverktoyover = (int) $_REQUEST['wlgjemverktoyover'];
} else { $wlgjemverktoyover = '0'; }

if (isset($_REQUEST['wlgjemverktoyunder'])) {
$wlgjemverktoyunder = (int) $_REQUEST['wlgjemverktoyunder'];
} else { $wlgjemverktoyunder = '0'; }

// Twitter- og Facebookikoner og andre bilder

$litentwitt = plugins_url ('g/twitter.png' , __FILE__);
$litenface = plugins_url ('g/fb.png' , __FILE__);
$litenmail = plugins_url ('g/mail.png' , __FILE__);
$twittikon = plugins_url ('g/twitter.png' , __FILE__);
$faceikon = plugins_url ('g/fb.png' , __FILE__);
$mailikon = plugins_url ('g/mail.png' , __FILE__);
$lenke = plugins_url ('g/lenke.png' , __FILE__);
$generiskomslag = plugins_url ('g/ikke_digital.jpg' , __FILE__);
$lokalhistoriewikiomslag = plugins_url ('g/lokalhistoriewiki.jpg' , __FILE__);
$artikkelbilde = plugins_url ('g/artikkel.png' , __FILE__);
$gammelradio = plugins_url ('g/gammelradio.jpg' , __FILE__);
$talebilde = plugins_url ('g/tale.jpg' , __FILE__);
$bildemangler = plugins_url ('g/bildemangler.png' , __FILE__);

// Get Search
$search_string = urlencode(stripslashes(strip_tags($_REQUEST['lokalhistquery'])));
$search_string = str_replace ("+" , " " , $search_string); // Fjerne mellomrom som ble til +
$search_string = trim($search_string);

/*
$search_string = str_replace ("\"", "" , $search_string); // vi fjerner rett og slett fnutter
$search_string = str_replace ("%22", "" , $search_string); // disse fnuttene også
$search_string = str_replace (" ", "%20" , $search_string);
*/

// Finne stamp, hente fra disk hvis cacher finnes
$cachestreng = mb_strtolower($search_string) . str_replace("," , "" , $baser) . $makstreff; 

$cachenavn = md5($cachestreng);
$cachefil = $cachenavn . ".bin";
$cachefilto = $cachenavn . ".bin2";

$cachefull = plugin_dir_path (__FILE__) . "cache/" . $cachefil;
$cachefullto = plugin_dir_path (__FILE__) . "cache/" . $cachefilto;

if ($wldebug == "1") {
	rop ("Cachefiler: " . $cachefil . " (bin og bin2)");
	}

if (($baser != '') && ($search_string != '')) { // Hvis vi har valgt noen baser og gjort et søk så...

	// Hva om filene er for gamle?
	$agelimit = get_option('wl_keep_cache_for' , '1209600'); // Default: 2 * 7 * 24 * 60 * 60 = 2 uker i sekunder

	$filer = scandir(plugin_dir_path (__FILE__) . "cache/");
	$basepath = plugin_dir_path (__FILE__);
	foreach ($filer as $ffil) {
		$fil = $basepath . "cache/" . $ffil;
			if (time() - filemtime($fil) > $agelimit) {
				if ((!is_dir($fil))) {	
					unlink ($fil); // slett filen
				}
			}
	}

	if ((file_exists($cachefull)) && (file_exists($cachefullto)) && ($wl_bruk_cache == "1") && (!isset($_REQUEST['dorss']))) { // hvis de finnes, bruk dem HVIS satt (og vi ikke kjører RSS - da vil vi ha ferske treff!)
		if ($wldebug == "1") {
			rop ("Cache finnes, bruker den ikke!");
		}

		$lesinntreff = file_get_contents ($cachefull); // les det fra fil
		$deflatetreff = gzuncompress($lesinntreff);
		$treff = unserialize ($deflatetreff);

		$lesinntreffto = file_get_contents ($cachefullto); // les det fra fil
		$deflatetreffto = gzuncompress($lesinntreffto);
		$treffinfo = unserialize ($deflatetreffto);

	} else { // ellers: Gjør søk i alle basene og lag cachefilene (to stk.)
		$splittbaser = explode ("," , $baser);
		foreach ($splittbaser as $enbase) { 
			include ('basesok/' . $enbase . '.php');
			if ($wldebug == "1") {
				$brukt = round ((microtime(true) - $time_start) , 2);
				echo 'Ferdig med base ' . $enbase . ' - brukt ' . $brukt . " sekunder. Har " . $antalltreff[$enbase] . " treff.<br>";
			}
		}
			
		// Gjøre om alt til rene strenger (fra XML og sånt) - bare i tilfelle
		array_walk_recursive($treff,'tilstreng');

		// Gjøre klar array med treffinfo
		foreach ($antalltreff as $key => $value) {
			$treffinfo['antalltreff'][$key] = $value;
		}

		if (is_array($materialtreff)) {
			foreach ($materialtreff as $key => $value) { // dette er materialtypene (bilde, bok, tekst...)
				$treffinfo['materialtreff'][$key] = $value;
			}
		}
	
		// Timestamp
		$treffinfo['timestamp'] = time();

		// Skrive de ferdige resultatene til fil HVIS > 0 treff
		if (array_sum($treffinfo['antalltreff']) > 0) {	
			if ($wl_bruk_cache == "1") { // Hvis angitt i innstillingene
				$cachefp = fopen($cachefull ,"w"); 
				if (flock($cachefp, LOCK_EX)) {
					fwrite ($cachefp, gzcompress(serialize($treff)));
					flock($cachefp, LOCK_UN); // unlock the file
				}
				fclose ($cachefp);
	
				$cachefp = fopen($cachefullto ,"w"); 
				if (flock($cachefp, LOCK_EX)) {
					fwrite ($cachefp, gzcompress(serialize($treffinfo)));
					flock($cachefp, LOCK_UN); // unlock the file
				}
				fclose ($cachefp);
			}
		}
	}

	// RENS OPP I SØKERESULTATENE OG KALKULER NYE TALL FOR TREFF OG LØSE UMULIGHETER
	// (Umuligheter: f.eks. karusellvisning og paginering...
	include ('includes/sanitizesearch.php');

	// Så sortere materialtreff med flest treff først

	if (is_array($materialtreff)) {
		arsort ($treffinfo['materialtreff']);
	}

	// HVILKEN SORTERING ER VALGT?

	switch ($sortering) {
		case "base":
			 // Ingen ting, dette er standard
			break;

		case "tilfeldig":
			shuffle ($treff);			
			break;

		case "tittel_asc":
			usort($treff, 'wltittelasc');
			break;

		case "tittel_desc":
			usort($treff, 'wltitteldesc');
			break;

		case "digidato_asc":
			usort($treff, 'wldigidatoasc');
			break;

		case "digidato_desc":
			usort($treff, 'wldigidatodesc');
			break;

		case "dato_asc":
			usort($treff, 'wldatoasc');
			break;

		case "dato_desc":
			usort($treff, 'wldatodesc');
			break;

		case "ansvar_asc":
			usort($treff, 'wlansvarasc');
			break;

		case "ansvar_desc":
			usort($treff, 'wlansvardesc');
			break;
	}

	// SKAL VI BEGRENSE OSS TIL EN MATERIALTYPE?
	if ($materialavgrensning != '') { // vi skal avgrense
		$nyetreff = '';
		foreach ($treff as $materialtreff) {
			if (@$materialtreff['materialtype'] == $materialavgrensning) { // hvis type=begrensning
				$nyetreff[] = $materialtreff;
			}
		}
	$treff = $nyetreff;
	}

	// SKAL VI BEGRENSE OSS TIL EN BASE?
	if ($baseavgrens != '') {
		$nyetreff = '';
		foreach ($treff as $basetreff) {
			if (@$basetreff['slug'] == $baseavgrens) { // hvis base = begrensning
				$nyetreff[] = $basetreff;
			}
		}
	$treff = $nyetreff;
	}

	// FERDIG MED Å SØKE - SKRIVE UT RESULTATER
	if (!isset($_REQUEST['dorss'])) { // ikke dette hvis vi skal lage RSS-fil
		echo '<div id="gridcontainer">' . "\n";
		echo '<a name="wltoolbar"></a>' . "\n"; 
	}

	if ((count($treff) > 0) && ($wlgjemverktoyover != "1") && (!isset($_REQUEST['dorss']))) { // Bare hvis option er satt og vi ikke skal lage RSS-feed og antall treff > 0

		include ('includes/toolbar.php'); 
		if ($wldebug == "1") {
			$brukt = round ((microtime(true) - $time_start) , 2);
			echo 'Har inkludert toolbar over - brukt ' . $brukt . " sekunder";
		}
	}

	if (count($treff) > 0) { // må ha noen treff
		// Hvis ikke RSS så vis
		include ('includes/paginate.php'); // denne linjen lager $wloutpaginate = HTML for paginering
		if ($visning != 'rss') { // Visning er ikke RSS
			echo $wloutpaginate; 
			include ('includes/vistreff-' . $visning . '.php');
		if ($wldebug == "1") {
			$brukt = round ((microtime(true) - $time_start) , 2);
			echo 'Har inkludert trefflisten - brukt ' . $brukt . " sekunder";
		}

		} else { // visning er RSS
			if ((isset($_REQUEST['dorss'])) && ($_REQUEST['dorss'] == '1')) { // og vi skal kjøre ut RSS
				include ('includes/rss.php'); // lag og spytt ut RSS
			} else { // Vi skal bare vise lenke til RSS
				$serverdel = plugins_url( 'search.php', __FILE__ ); // det er search.php
				$argumenter = stristr ($_SERVER['REQUEST_URI'] , "?"); // alt etter ? er argumenter, må bli med videre
				$direkterss = $serverdel . $argumenter . "&dorss=1"; // og legge til dette

				echo '<div style="width: 100%; text-align: justify;">';
				echo 'Ikonet under er en lenke til trefflisten som RSS-str&oslash;m. Denne kan du putte inn i favorittleseren din, for eksempel <a target="_blank" href="http://www.feedreader.com/">Feedreader</a>, eller nettleseren din. Noen epost-programmer har ogs&aring; st&oslash;tte for RSS. NB! Hvis du sorterer trefflisten med de nyeste treffene f&oslash;rst vil dette fungere som en varsling for nye treff som blir tilgjengelige.<br><br>';
//				echo '<form method="POST" action="' . $direkterss . '">' . "\n";
				echo '<div style="width: 100%; text-align: center;">';
				echo '<a href="' . $direkterss . '">';
				echo '<img style="border: none; box-shadow: none; width: 80px;" src="' . plugins_url( 'g/rss.png', __FILE__ ) . '" alt="Hent RSS-feed" /><br>';
//				echo '<input style="width: 80px;" type="submit" name="submit" value="Hent RSS" />' . "\n";
//				echo '</form>';
				echo '</a>';
				echo '</div>';						
				echo '</div>' . "\n\n";
			}
		}
	} else { // ops, vi har ingen treff
		echo "<br><b>Ingen treff!</b><br><br>";
	}
	

	if ($visning != 'rss') { // bare vise paginering hvis ikke RSS	
		echo $wloutpaginate; // vis pagineringen en gang til
	}

	if (($wlgjemverktoyunder != "1") && (!isset($_REQUEST['dorss']))) { // Bare hvis option er satt og vi ikke skal lage RSS-feed
		include ('includes/toolbar_bottom.php'); 
		if ($wldebug == "1") {
			$brukt = round ((microtime(true) - $time_start) , 2);
			echo 'Har inkludert toolbar under - brukt ' . $brukt . " sekunder";
		}
	}

	if ($visning != 'rss') { // bare vise slutt på div hvis ikke RSS	
		echo '</div>' . "\n"; // </#gridcontainer>
	}



} else { // ops, ingen baser valgt
	echo "<i>Du m&aring; velge noen s&oslash;kekilder og skrive inn en s&oslash;keterm f&oslash;r du kan f&aring; noen treff!</i>";
}


// Alt gikk fint - sett feilrapportering tilbake til tidligere nivå:
error_reporting($wl_error);
?>
