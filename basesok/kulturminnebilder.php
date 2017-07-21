<?php

/* Søker i Riksantikvarens kulturminnebilder og legger til:
 - treff i $treffliste
 - antall treff i $antalltreff['slug']
 - antall nye i denne materialtypen i $materialtreff['materialtype']
*/

// Info: http://learn.fotoware.com/02_FotoWeb_8.0/Developing_with_the_FotoWeb_API/The_FotoWeb_Archive_Agent_API/04_Interface/Search_method

$rawurl = "http://kulturminnebilder.ra.no/fotoweb/fwbin/fotoweb_isapi.dll/ArchiveAgent/5001/Search?Search=<!QUERY!>&MaxHits=" . $makstreff . "&PreviewSize=400&FileInfo=1&MetaData=1";

$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm
$antalltreff['kulturminnebilder'] = ''; // nullstiller i tilfelle søket feiler

$kulturminnebildertreff = '';

// LASTE TREFFLISTE SOM XML
$xml = get_content($rawurl);

if(substr($xml, 0, 5) == "<?xml") { // vi fikk en XML-fil tilbake
	$xmldata = simplexml_load_string($xml);

	// FINNE ANTALL TREFF
	$antalltreff['kulturminnebilder'] = (int) $xmldata->attributes()->TotalHits;
	@$materialtreff['bilde'] += $antalltreff['kulturminnebilder']; 

	// ... SÅ HVERT ENKELT TREFF
	$teller = 0;
	foreach ($xmldata->File as $entry) {

		if ($teller < $makstreff) {

// ($entry);
			$kulturminnebildertreff[$teller]['kilde'] = 'Kulturminnebilder';
			$kulturminnebildertreff[$teller]['slug'] = 'kulturminnebilder';
			$kulturminnebildertreff[$teller]['materialtype'] = "bilde";

			$kulturminnebildertreff[$teller]['id'] = (string) $entry->attributes()->Id;
			$kulturminnebildertreff[$teller]['url'] = "http://kulturminnebilder.ra.no" . $entry->attributes()->{'X-Permalink'};

			$kulturminnebildertreff[$teller]['bilde'] = (string) $entry->PreviewLinks->PreviewUrl;

			foreach ($entry->MetaData->Text->Field as $tekst) { // masse info å hente her
				//domp ($tekst->attributes());

				if ($tekst->attributes()->Name == "Motiv/ Objektets egennavn") {
					$kulturminnebildertreff[$teller]['tittel'] = (string) $tekst;
				}

				if ($tekst->attributes()->Name == "Fotograf/ Tegnet av") {
					$kulturminnebildertreff[$teller]['ansvar'] = (string) $tekst;
				}

				if ($tekst->attributes()->Name == "Omtrentlig datering av foto/tegning") {
					$kulturminnebildertreff[$teller]['dato'] = str_replace ("Ca " , "" , (string) $tekst);
				}

				if ($tekst->attributes()->Name == "Digitalisert Dato") {
					$kulturminnebildertreff[$teller]['digidato'] = str_replace ("-" , "" , substr((string) $tekst , 0, 10));
				}

				if ($tekst->attributes()->Name == "Datering") {
					$kulturminnebildertreff[$teller]['dato'] = str_replace ("-" , "" , (string) $tekst);
				}

				if ($tekst->attributes()->Name == "Fototype/ Teknikk") {
					$teknikk = trim ((string) $tekst);
				}

				if ($tekst->attributes()->Name == "Eier/ Arkiveier") {
					$eier = trim ((string) $tekst);
				}

				if ($tekst->attributes()->Name == "Emneord/ Objekttype") {
					@$emneord[] = trim ((string) mb_strtolower($tekst));
				}

				if ($tekst->attributes()->Name == "Sogn") {
					$sogn = trim((string) $tekst);
				}

				if ($tekst->attributes()->Name == "Fylke") {
					$fylke = trim((string) $tekst);
				}

				if ($tekst->attributes()->Name == "Kommune") {
					$kommune = trim((string) $tekst);
				}

				if ($tekst->attributes()->Name == "Adresse/ Sted") {
					$adresse = trim((string) $tekst);
				}
			}

		// BESKRIVELSE
		$kulturminnebildertreff[$teller]['beskrivelse'] = '';

		if (isset($fylke)) {
			$kulturminnebildertreff[$teller]['beskrivelse'] .= '<b>Fylke: </b>' . $fylke . '. ';
		}

		if (isset($kommune)) {
			$kulturminnebildertreff[$teller]['beskrivelse'] .= '<b>Kommune: </b>' . $kommune . '. ';
		}

		if (isset($adresse)) {
			$kulturminnebildertreff[$teller]['beskrivelse'] .= '<b>Sted: </b>' . $adresse . '. ';
		}

		if (isset($sogn)) {
			$kulturminnebildertreff[$teller]['beskrivelse'] .= '<b>Sogn: </b>' . $sogn . '. ';
		}
		
		if (isset($teknikk)) {
			$kulturminnebildertreff[$teller]['beskrivelse'] .= '<b>Teknikk: </b>' . $teknikk . '. ';
		}

		if (isset($eier)) {
			$kulturminnebildertreff[$teller]['beskrivelse'] .= '<b>Eier: </b>' . $eier . '. ';
		}
		
		if (isset($emneord)) {
			$kulturminnebildertreff[$teller]['beskrivelse'] .= '<b>Emneord: </b>' . implode (", " , $emneord) . ". ";
		}

		unset ($emneord, $sted, $sogn, $teknikk, $eier, $fylke, $kommune, $adresse); // ferdig nå

		}
		$teller++;
	} // SLUTT PÅ HVERT ENKELT TREFF

} // slutt på "vi fikk XML-fil tilbake

$treff = array_merge_recursive ((array) $kulturminnebildertreff , (array) $treff);

// SLUTT
