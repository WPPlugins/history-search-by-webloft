<?php

/* Søker i Universitetsmuseenes arkeologisamling og legger til:
 - treff i $treffliste
 - antall treff i $antalltreff['slug']
 - antall nye i denne materialtypen i $materialtreff['materialtype']
*/

// Info: http://www.unimus.no/nedlasting/mop.html

$rawurl = "http://www.unimus.no/artefacts/search/?q=<!QUERY!>&numberofrecords=" . $makstreff . "&f=xml&photo=";

$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm
$antalltreff['unimusark'] = ''; // nullstiller i tilfelle søket feiler

$unimusarktreff = '';

// LASTE TREFFLISTE SOM XML
$xml = get_content($rawurl);

if(substr($xml, 0, 5) == "<?xml") { // vi fikk en XML-fil tilbake
	$xmldata = simplexml_load_string($xml);

	// FINNE ANTALL TREFF
	$antalltreff['unimusark'] = (int) $xmldata->numberOfRecords;
	@$materialtreff['gjenstand'] += $antalltreff['unimusark']; 

	// ... SÅ HVERT ENKELT TREFF
	$teller = 0;
	foreach ($xmldata->MusitEntities->Entity as $entry) {

		// Genialt å lage URL med forkortelser for hvert museum? NEI
		$museum = strtolower($entry->Museum);
		if ($museum == "arkeologisk museum") { $museum = "am"; }
		if ($museum == "kulturhistorisk museum") { $museum = "khm"; }
		if ($museum == "naturhistorisk museum") { $museum = "nhm"; }
		if ($museum == "tromsø museum - universitetsmuseet") { $museum = "tmu"; }
		if ($museum == "universitetsmuseet i bergen") { $museum = "um"; }
		if ($museum == "ntnu vitenskapsmuseet") { $museum = "vm"; }

		$unimusarktreff[$teller]['kilde'] = 'Universitetsmuseene - arkeologi';
		$unimusarktreff[$teller]['slug'] = 'unimusark';
		$unimusarktreff[$teller]['materialtype'] = "gjenstand";

		$unimusarktreff[$teller]['id'] = (string) $entry->attributes()->id;
		$unimusarktreff[$teller]['bilde'] = (string) $entry->Photos->Photo->PhotoId;

		$unimusarktreff[$teller]['url'] = "http://www.unimus.no/artefacts/" . $museum . "/search/?oid=" . $unimusarktreff[$teller]['id'] . "&f=html";

		$unimusarktreff[$teller]['tittel'] = multibyte_ucfirst ($entry->Artefact);
		if (isset($entry->Photos->Photo->Photographer)) {
			$unimusarktreff[$teller]['ansvar'] = "Foto: " . $entry->Photos->Photo->Photographer;
		} else {
			$unimusarktreff[$teller]['ansvar'] = "N.N.";
		}

		// BESKRIVELSE
		$unimusarktreff[$teller]['beskrivelse'] = '';
		$unimusarktreff[$teller]['beskrivelse'] .= "<b>Funnsted: </b>" . $entry->CadastralName . ", " . $entry->Municipality . ", " . $entry->County . ". ";
		if (isset($entry->Period)) {
			$unimusarktreff[$teller]['beskrivelse'] .= "<b>Periode: </b>" . $entry->Period . ". ";
		}

		if (isset($entry->Form)) {
			$unimusarktreff[$teller]['beskrivelse'] .= "<b>Form: </b>" . $entry->Form . ". ";
		}

		if (isset($entry->Material)) {
			$unimusarktreff[$teller]['beskrivelse'] .= "<b>Materiale: </b>" . $entry->Material . ". ";
		}

		$unimusarktreff[$teller]['beskrivelse'] .= "<b>Samling: </b>" . $entry->Museum . ". ";
		if (isset($entry->Description)) {
			$unimusarktreff[$teller]['beskrivelse'] .= "<b>Beskrivelse: </b>" . $entry->Description;
		}

		$unimusarktreff[$teller]['dato'] = "N/A";
		$unimusarktreff[$teller]['digidato'] = "N/A";

		$teller++;
	} // SLUTT PÅ HVERT ENKELT TREFF

} // slutt på "vi fikk XML-fil tilbake

$treff = array_merge_recursive ((array) $unimusarktreff , (array) $treff);

// SLUTT
