<?php

/* Søker i norvegiana (Industrimuseum) og legger til:
 - treff i $treffliste
 - antall treff i $antalltreff['slug']
 - antall nye i denne materialtypen i $materialtreff['materialtype']
*/

$rawurl = "http://kulturnett2.delving.org/api/search?rows=" . $makstreff . "&query=delving_description:<!QUERY!>%20delving_hasDigitalObject%3Atrue%20delving_spec%3AIndustrimuseum";

// Gjøre noe med søkeord?

// LEGGE TIL ?start=N for result page N
// LEGGE TIL ?rows=N for antall treff per side

$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm
$antalltreff['norvegianaindmu'] = ''; // nullstiller i tilfelle søket feiler

$norvegianaindmutreff = '';

// LASTE TREFFLISTE SOM XML

$xmlfile = get_content($rawurl);

if(substr($xmlfile, 0, 5) == "<?xml") { // vi fikk en XML-fil tilbake

	$xmldata = simplexml_load_string($xmlfile);
	
	// FINNE ANTALL TREFF
	$antalltreff['norvegianaindmu'] = (int) $xmldata->query->attributes()->numFound;
	@$materialtreff['artikkel'] += $antalltreff['norvegianaindmu']; 

	// ... SÅ HVERT ENKELT TREFF
	$teller = 0;
	foreach ($xmldata->items->item as $entry) {
		$ferdigdato = '';
		$stedsinfo = '';

		if ($teller < $makstreff) {
			$delving = $entry->fields->children('delving', true);
			$dc = $entry->fields->children('dc', true);
			$dcterms = $entry->fields->children('dcterms', true);
			$abm = $entry->fields->children('abm', true);

			// DATO
			if (isset($dc->date)) {
				$norvegianaindmutreff[$teller]['dato'] = trim(str_replace("-", "" , $dc->date));
			} else { 
				$norvegianaindmutreff[$teller]['dato'] = "";
		 	}
			$norvegianaindmutreff[$teller]['digidato'] = ""; // finnes ikke 

			// BESKRIVELSE
			$norvegianaindmutreff[$teller]['beskrivelse'] = "";
			if (isset($delving->description)) {
				$norvegianaindmutreff[$teller]['beskrivelse'] .= "<b>Beskrivelse: </b>" . htmlspecialchars(strip_tags($delving->description));
			}

			if (isset($abm->address)) {
				$norvegianaindmutreff[$teller]['beskrivelse'] .= "<b>Sted: </b>" . $abm->address . ". ";
			}

			if (isset($dc->date)) {
				$norvegianaindmutreff[$teller]['beskrivelse'] .= "<b>Datering: </b>" . $dc->date . ". ";
			}


			$norvegianaindmutreff[$teller]['url'] = (string) $delving->landingPage;

			// Hvis vi finner ting som &amp;oslash er det en html-encoding for mye...
			
			if ((stristr($norvegianaindmutreff[$teller]['beskrivelse'] , "&amp;oslash")) || (stristr($norvegianaindmutreff[$teller]['beskrivelse'] , "&amp;aring"))) { // rart med tegnene her...
				$norvegianaindmutreff[$teller]['beskrivelse'] = html_entity_decode($norvegianaindmutreff[$teller]['beskrivelse']);
			}
			$norvegianaindmutreff[$teller]['beskrivelse'] = str_replace ("&amp;nbsp;" , " " , $norvegianaindmutreff[$teller]['beskrivelse']); // mer kål med tegn


			if (isset($dc->title)) {
				$norvegianaindmutreff[$teller]['tittel'] = htmlspecialchars($dc->title);
			}

			if (isset($dc->creator)) {
				$norvegianaindmutreff[$teller]['ansvar'] = htmlspecialchars($dc->creator);
			} else {
				$norvegianaindmutreff[$teller]['ansvar'] = "N.N.";
			}

			$norvegianaindmutreff[$teller]['bilde'] = $delving->thumbnail;
			$norvegianaindmutreff[$teller]['kilde'] = "Industrimuseum.no";
			$norvegianaindmutreff[$teller]['slug'] = 'norvegianaindmu';
			$norvegianaindmutreff[$teller]['id'] = (string) $dc->identifier;
			$norvegianaindmutreff[$teller]['materialtype'] = 'artikkel';

			$teller++;
			
		}
	} // SLUTT PÅ HVERT ENKELT TREFF
} // Slutt på "Vi fikk XML tilbake"

$treff = @array_merge_recursive ((array) $norvegianaindmutreff , (array) $treff);

// slutt

