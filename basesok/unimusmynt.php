<?php

/* Søker i Universitetsmuseenes mynt- og medaljesamling og legger til:
 - treff i $treffliste
 - antall treff i $antalltreff['slug']
 - antall nye i denne materialtypen i $materialtreff['materialtype']
*/

// Info: http://www.unimus.no/nedlasting/mop.html

$rawurl = "http://www.unimus.no/numismatica/search/?q=<!QUERY!>&numberofrecords=" . $makstreff . "&type=medal&f=xml&photo=";
$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm
$antalltreff['unimusmynt'] = ''; // nullstiller i tilfelle søket feiler

$unimusmynttreff = '';

// LASTE TREFFLISTE SOM XML
$xml = get_content($rawurl);

if(substr($xml, 0, 5) == "<?xml") { // vi fikk en XML-fil tilbake
	$xmldata = simplexml_load_string($xml);
//domp ($xmldata);
	// FINNE ANTALL TREFF
	$antalltreff['unimusmynt'] = (int) $xmldata->numberOfRecords;
	@$materialtreff['gjenstand'] += $antalltreff['unimusmynt']; 

	// ... SÅ HVERT ENKELT TREFF
	$teller = 0;
	foreach ($xmldata->MusitEntities->Entity as $entry) {
		$unimusmynttreff[$teller]['kilde'] = 'Universitetsmuseene - numismatikk';
		$unimusmynttreff[$teller]['slug'] = 'unimusmynt';
		$unimusmynttreff[$teller]['materialtype'] = "gjenstand";

		$unimusmynttreff[$teller]['id'] = (string) $entry->attributes()->id;
		$unimusmynttreff[$teller]['bilde'] = (string) $entry->Photos->PhotoId;

		$unimusmynttreff[$teller]['url'] = "http://www.unimus.no/numismatikk/#/L=no_BO/P=search/S=" . $search_string . "/I=" . $unimusmynttreff[$teller]['id'];

		$unimusmynttreff[$teller]['tittel'] = (string) $entry->ArtifactType;
		if (isset($entry->Category)) {
			$unimusmynttreff[$teller]['tittel'] = (string) $entry->Category;
		}

		if (isset($entry->Characterization)) {
			$unimusmynttreff[$teller]['tittel'] = (string) $entry->Characterization;
		} 

		// BESKRIVELSE

		$unimusmynttreff[$teller]['beskrivelse'] = "";
		if (isset($entry->Category)) {
			$unimusmynttreff[$teller]['beskrivelse'] .= "<b>Kategori: </b>" . $entry->Category . ". ";
		}
		if (isset($entry->Provenance)) {
			$unimusmynttreff[$teller]['beskrivelse'] .= "<b>Proveniens: </b>" . $entry->Provenance . ". ";
		}

		if (isset($entry->Weight)) {
			$unimusmynttreff[$teller]['beskrivelse'] .= "<b>Vekt: </b>" . $entry->Weight . ". ";
		}

		if (isset($entry->Diameter)) {
			$unimusmynttreff[$teller]['beskrivelse'] .= "<b>Diameter: </b>" . $entry->Diameter . ". ";
		}
		
		if (isset($entry->MotifAdverse)) {
			$unimusmynttreff[$teller]['beskrivelse'] .= "<b>Motiv forside: </b>" . $entry->MotifAdverse . ". ";
		}
		
		if (isset($entry->MotifReverse)) {
			$unimusmynttreff[$teller]['beskrivelse'] .= "<b>Motiv bakside: </b>" . $entry->MotifReverse . ". ";
		}
		
		if (isset($entry->InscriptionAdverse)) {
			$unimusmynttreff[$teller]['beskrivelse'] .= "<b>Inskripsjon forside: </b>" . $entry->InscriptionAdverse . ". ";
		}
		
		if (isset($entry->InscriptionReverse)) {
			$unimusmynttreff[$teller]['beskrivelse'] .= "<b>Inskripsjon bakside: </b>" . $entry->InscriptionReverse . ". ";
		}
		


		$materiale = (array) $entry->Materials;
		foreach ($materiale as $ettmateriale) {
			if (is_array($ettmateriale)) {
				foreach ($ettmateriale as $virkeligett) {
					$lagetarray[] = $virkeligett->Name;
				}
				$lagetav = implode ($lagetarray, " / ");
			} else {
				$lagetav = (string) $ettmateriale->Name;
			}
		}
		
		if (isset($lagetav)) {
			$unimusmynttreff[$teller]['beskrivelse'] .= "<b>Materiale: </b>" . $lagetav . ". ";
		}

		$unimusmynttreff[$teller]['dato'] = (string) $entry->Date;
		$unimusmynttreff[$teller]['digidato'] = "N/A";

		$teller++;
		unset ($lagetarray , $lagetav);
	} // SLUTT PÅ HVERT ENKELT TREFF

} // slutt på "vi fikk XML-fil tilbake"

$treff = array_merge_recursive ((array) $unimusmynttreff , (array) $treff);

// SLUTT
