<?php

/* Søker i Universitetsmuseenes fotosamlinger og legger til:
 - treff i $treffliste
 - antall treff i $antalltreff['slug']
 - antall nye i denne materialtypen i $materialtreff['materialtype']
*/

// Info: http://www.unimus.no/nedlasting/mop.html

$rawurl = "http://www.unimus.no/photos/search/?q=<!QUERY!>&numberofrecords=" . $makstreff . "&f=xml&photo=";

$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm
$antalltreff['unimusfoto'] = ''; // nullstiller i tilfelle søket feiler

$unimusfototreff = '';

// LASTE TREFFLISTE SOM XML
$xml = get_content($rawurl);

if(substr($xml, 0, 5) == "<?xml") { // vi fikk en XML-fil tilbake
	$xmldata = simplexml_load_string($xml);

	// FINNE ANTALL TREFF
	$antalltreff['unimusfoto'] = (int) $xmldata->numberOfRecords;
	@$materialtreff['gjenstand'] += $antalltreff['unimusfoto']; 

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


		$unimusfototreff[$teller]['kilde'] = 'Universitetsmuseene - foto';
		$unimusfototreff[$teller]['slug'] = 'unimusfoto';
		$unimusfototreff[$teller]['materialtype'] = "bilde";

		$unimusfototreff[$teller]['id'] = (string) $entry->attributes()->id;
		$unimusfototreff[$teller]['bilde'] = (string) $entry->Photos->Photo->PhotoId;

		$unimusfototreff[$teller]['url'] = "http://www.unimus.no/photos/" . $museum . "/search/?oid=" . $unimusfototreff[$teller]['id'] . "&f=html";

		if (isset($entry->Motif)) {
			$unimusfototreff[$teller]['tittel'] = (string) $entry->Motif;
		} else {
			$unimusfototreff[$teller]['tittel'] = "Uten tittel";
		}

		if (isset($entry->Photos->Photo->Photographer)) {
			$unimusfototreff[$teller]['ansvar'] = "Foto: " . $entry->Photos->Photo->Photographer;
		} else {
			$unimusfototreff[$teller]['ansvar'] = "N.N.";
		}

		if (isset($entry->Time->Date)) {
			$unimusfototreff[$teller]['dato'] = (string) $entry->Time->Date;
			// Ta bort "mai", "august" og andre fiffige katalogiseringer
			$unimusfototreff[$teller]['dato'] = filter_var($unimusfototreff[$teller]['dato'], FILTER_SANITIZE_NUMBER_INT); 
		} else {
			$unimusfototreff[$teller]['dato'] = "N/A";
		}

		if (isset($entry->RegDate)) {
			$unimusfototreff[$teller]['digidato'] = str_replace ("-" , "" , (string) $entry->RegDate);
		} else {
			$unimusfototreff[$teller]['digidato'] = "N/A";
		}

		// BESKRIVELSE
		$unimusfototreff[$teller]['beskrivelse'] = "";
		$sted = '';
		if (isset($entry->Places->Place)) {
			if (isset($entry->Places->Place->CadastralName)) {
				$sted[] = $entry->Places->Place->CadastralName;
			}
			if (isset($entry->Places->Place->PlaceName)) {
				$sted[] = $entry->Places->Place->PlaceName;
			}

			if (isset($entry->Places->Place->Area)) {
				$sted[] = $entry->Places->Place->Area;
			}

			if (isset($entry->Places->Place->StreetAddress)) {
				$sted[] = $entry->Places->Place->StreetAddress;
			}

			if (isset($entry->Places->Place->Municipality)) {
				$sted[] = $entry->Places->Place->Municipality;
			}

			if (isset($entry->Places->Place->County)) {
				$sted[] = $entry->Places->Place->County;
			}

			if (isset($entry->Places->Place->Country)) {
				$sted[] = $entry->Places->Place->Country;
			}

			$stedstring = implode (" / " , $sted);
		} else {
			$stedstring = "";
		}

		if ((isset($stedstring)) && ($stedstring != "")) {
			$unimusfototreff[$teller]['beskrivelse'] .= "<b>Sted: </b>" . $stedstring . ". ";			
		}

		if (isset($entry->SubjectHeading)) {
			$unimusfototreff[$teller]['beskrivelse'] .= "<b>Emneord: </b>" . $entry->SubjectHeading . ". ";			
		}

		if (isset($entry->CopyrightNotice)) {
			$unimusfototreff[$teller]['beskrivelse'] .= "<b>Copyright: </b>" . $entry->CopyrightNotice . ". ";			
		}

		$unimusfototreff[$teller]['beskrivelse'] .= "<b>Dato: </b>" . $unimusfototreff[$teller]['dato'] . ". ";

		unset ($sted, $stedstring);
		$teller++;
	} // SLUTT PÅ HVERT ENKELT TREFF

} // slutt på "vi fikk XML-fil tilbake

$treff = array_merge_recursive ((array) $unimusfototreff , (array) $treff);
//domp ($unimusfototreff);
// SLUTT
