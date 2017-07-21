<?php

/* Søker i Virksomme Ord og legger til:
 - treff i $treffliste
 - antall treff i $antalltreff['slug']
 - antall nye i denne materialtypen i $materialtreff['materialtype']
*/

$rawurl = "http://bulmeurt.uib.no:8080/retorikk/servlet/retorikk.mellomlag.Esok?sok=<!QUERY!>&kategori=alle";
$virksommeord_search_string = utf8_decode(urldecode($search_string));

$rawurl = str_replace ("<!QUERY!>" , $virksommeord_search_string , $rawurl); // sette inn søketerm
$antalltreff['virksommeord'] = ''; // nullstiller i tilfelle søket feiler

$virksommeordtreff = '';

// LASTE TREFFLISTE SOM XML
$xmlfile = get_content($rawurl);

if(substr($xmlfile, 0, 5) == "<?xml") { // vi fikk en XML-fil tilbake
	$xmldata = simplexml_load_string($xmlfile);

	// FINNE ANTALL TREFF
	$antalltreff['virksommeord'] = (int) $xmldata->attributes()->length;

	@$materialtreff['manuskripter'] += $antalltreff['virksommeord']; 

	// ... SÅ HVERT ENKELT TREFF
	$teller = 0;
	$virksomarray = '';
	foreach ($xmldata as $key => $entry) {
		if (isset($entry->Speech)) {
			foreach ($entry->Speech as $tale) {
				$virksomarray[] = $tale;
			}
		}
	}

	foreach ($virksomarray as $tale) {	
		if ($teller < $makstreff) {
			$virksommeordtreff[$teller]['id'] = (string) $tale->attributes()->id;
			$virksommeordtreff[$teller]['url'] = "http://virksommeord.uib.no/taler?id=" . $virksommeordtreff[$teller]['id'];
			$virksommeordtreff[$teller]['tittel'] = (string) $tale->Title;
			$virksommeordtreff[$teller]['ansvar'] = (string) $tale->Person->Name->Last . ", " . $tale->Person->Name->First;	
			// BILDE 
			$bildet = '';
			$straitname = (string) $tale->Person->Name->First . " " . (string) $tale->Person->Name->Last;
			$wikipedianame = str_replace (" " , "_" , $straitname);
			$bildeurl = str_replace ("<!QUERY!>" , $wikipedianame, "https://en.wikipedia.org/w/api.php?action=query&titles=<!QUERY!>&prop=pageimages&format=json&pithumbsize=400");
			$bildedataraw = get_content ($bildeurl);
			$bildedata = json_decode ($bildedataraw);
			$bilde = $bildedata->query->pages;
			foreach ($bilde as $key => $value) {
				$bildet = (string) $value->thumbnail->source;
			}
			if (trim($bildet) != '') {
				$virksommeordtreff[$teller]['bilde'] = $bildet;
			} else {
				$virksommeordtreff[$teller]['bilde'] = $talebilde;
			}

			// BESKRIVELSE
			$virksommeordtreff[$teller]['beskrivelse'] = '';

			if (isset($tale->Location) && (trim($tale->Location) != '')) {
				$virksommeordtreff[$teller]['beskrivelse'] .= "<b>Tale holdt: </b>" . $tale->Location . ". ";						
			}

			if (isset($tale->Event) && (trim($tale->Event) != '')) {
				$virksommeordtreff[$teller]['beskrivelse'] .= "<b>Anledning: </b>" . $tale->Event . ". ";						
			}

			// DATO
			unset ($thisday, $thismonth, $thisyear);
			if (isset($tale->Date)) {
				$virksommeordtreff[$teller]['beskrivelse'] .= "<b>Dato: </b>";
				if ($tale->Date->Day != '') {
					$thisday = sprintf("%02d", (int) $tale->Date->Day);
					$virksommeordtreff[$teller]['beskrivelse'] .= sprintf("%02d", (int) $tale->Date->Day) . ".";						
				}

				if ($tale->Date->Month != '') {
					$thismonth = sprintf("%02d", (int) $tale->Date->Month);
					$virksommeordtreff[$teller]['beskrivelse'] .= sprintf("%02d", (int) $tale->Date->Month) . ".";
				}
				$thisyear = $tale->Date->Year;
				$virksommeordtreff[$teller]['beskrivelse'] .= $tale->Date->Year . ". ";
			}
			
			if (isset($tale->{'Keyword-list'})) {
			$emneord = '';
				foreach ($tale->{'Keyword-list'}->Keyword as $keyword) {
					$emneord[] = $keyword;
				}
				if ($emneord != '') {
					$virksommeordtreff[$teller]['beskrivelse'] .= "<b>Emneord: </b>";
					$virksommeordtreff[$teller]['beskrivelse'] .= implode (", " , $emneord);
					$virksommeordtreff[$teller]['beskrivelse'] .= ". ";
				} 
			}

			$virksommeordtreff[$teller]['dato'] = $thisyear . $thismonth . $thisday;
			$virksommeordtreff[$teller]['digidato'] = '';
			$virksommeordtreff[$teller]['materialtype'] = 'manuskript';
			$virksommeordtreff[$teller]['kilde'] = "Virksomme Ord";
			$virksommeordtreff[$teller]['slug'] = "virksommeord";

			$teller++;
		}
	} // SLUTT PÅ HVERT ENKELT TREFF

} // Slutt på "vi fikk XML tilbake"

$treff = array_merge_recursive ((array) $virksommeordtreff , (array) $treff);

// SLUTT

