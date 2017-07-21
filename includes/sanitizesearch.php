<?php

// Ordne inkonsekvenser i visninger

if ($visning == "karusell") {
	$wltreffperside = '99999';
}

// Fjerne rusk og rask i treff

// array_filter uten argumenter fjerner tomme elementer
$treff = @array_filter($treff);

// Får orden på indeksene slik at de går fra 0 og oppover
$treff= array_values ($treff);

if (count($treff) > 0) {
	foreach($treff as &$changetreff) {

		// Kompensere for manglende url
		if (trim($changetreff['url']) == '') {
			$changetreff['url'] = "javascript:alert('Dette elementet har visst ingen URL!');";
		}

		// Whitespace før og etter tittel
		$changetreff['tittel'] = trim($changetreff['tittel']);

		// Fjerner linjeskift (/r og /n) samt tab (/t) og whitespace foran og bak (trim)
		if (isset($changetreff['beskrivelse'])) {
			$changetreff['beskrivelse'] = trim(preg_replace('/[\s\t\n\r\s]+/', ' ', $changetreff['beskrivelse']));
			$changetreff['beskrivelse'] = str_replace ("<p>" , " " , $changetreff['beskrivelse']);
 		}
		// Ingen digidato? Vel, vi trenger det til RSS, så da får det briste eller bære
		if ($changetreff['digidato'] == "") {
			$changetreff['digidato'] = "19700101";
		}

		// Mangle bilde? Da viser vi et "mangler bilde"-bilde

		if (trim($changetreff['bilde']) == "") {
			$changetreff['bilde'] = $bildemangler;
		}
	
		// Utheve søketerm?
		if (($wl_uthev_term == "1") && (!isset($_REQUEST['dorss']))) { // bare hvis angitt i innstillinger OG vi ikke skal lage RSS
			$replace = str_replace ("%22" , "" , $search_string);
	//		$replace = "/\b" . $replace . "\b/i";
			$replace = "/" . $replace . "/i";
			$changetreff['tittel'] = preg_replace($replace , '<span class="wlsearchhighlight">$0</span>', $changetreff['tittel']);
			$changetreff['ansvar'] = preg_replace($replace , '<span class="wlsearchhighlight">$0</span>', $changetreff['ansvar']);
			$changetreff['beskrivelse'] = preg_replace($replace , '<span class="wlsearchhighlight">$0</span>', $changetreff['beskrivelse']);
		}
	}	
}

?>
