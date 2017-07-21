<?php

/* Søker i Nasjonalbibliotekets radioklipp og legger til:
 - treff i $treffliste
 - antall treff i $antalltreff['slug']
 - antall nye i denne materialtypen i $materialtreff['materialtype']
*/

$rawurl = "http://www.nb.no/services/search/v2/search?q=<!QUERY!>&fq=mediatype:Radio&fq=contentClasses:public&fq=digital:Ja&itemsPerPage=" . $makstreff . "&ft=true";

$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm
$antalltreff['nbradio'] = ''; // nullstiller i tilfelle søket feiler

$nbradiotreff = '';

// LASTE TREFFLISTE SOM XML
$xmlfile = get_content($rawurl);

if(substr($xmlfile, 0, 5) == "<?xml") { // vi fikk en XML-fil tilbake
	$xmldata = simplexml_load_string($xmlfile);

	// FINNE ANTALL TREFF
	$antalltreff['nbradio'] = (int) substr(stristr($xmldata->subtitle, " of ") , 4);
	@$materialtreff['radio'] += $antalltreff['nbradio']; 

	// ... SÅ HVERT ENKELT TREFF
	$teller = 0;
	foreach ($xmldata->entry as $entry) {
		if ($teller < $makstreff) {

			// METADATA SOM XML FOR DETTE TREFFET
			$childxml = ($entry->link[0]->attributes()->href); // Dette er XML med metadata
			$xmlfile = get_content($childxml);
			$childxmldata = simplexml_load_string($xmlfile);
			$namespaces = $entry->getNameSpaces(true);
			$nb = $entry->children($namespaces['nb']);

			// FINNE URN			
			if (stristr($nb->urn , ";")) {
				$tempura = explode (";" , $nb->urn);
				$urn = trim((string) $tempura[1]); // vi tar nummer 2 
			} else {
				$urn = (string) $nb->urn;
			}

			$nbradiotreff[$teller]['bilde'] = $gammelradio; // å ska'n gjørra da?

			$nbradiotreff[$teller]['url'] = "http://urn.nb.no/" . $urn;
			if ((isset($entry->title)) && ($entry->title != '')) {
				$nbradiotreff[$teller]['tittel'] = (string) $entry->title;
			} else {
				$nbradiotreff[$teller]['tittel'] = '(Uten tittel)';
			}

			$nbradiotreff[$teller]['ansvar'] = "Norsk Rikskringkasting";

			$nbradiotreff[$teller]['dato'] = str_replace ("-" , "" , $nb->date);	

			$nbradiotreff[$teller]['beskrivelse'] = "<b>Dato: </b>" . $nbradiotreff[$teller]['dato'] . ". ";
			$nbradiotreff[$teller]['beskrivelse'] .= $entry->summary;
			$nbradiotreff[$teller]['beskrivelse'] = str_replace ("<br>" , ". " , $nbradiotreff[$teller]['beskrivelse']);

			$nbradiotreff[$teller]['kilde'] = "Nasjonalbiblioteket - radio";
			$nbradiotreff[$teller]['slug'] = "nbradio";
			$nbradiotreff[$teller]['materialtype'] = "lyd";
			$nbradiotreff[$teller]['id'] = $urn;
			$nbradiotreff[$teller]['digidato'] = ""; // Vi får ikke digidato fra URN


			$teller++;
		}
	} // SLUTT PÅ HVERT ENKELT TREFF

} // Slutt på "vi fikk XML tilbake"

$treff = array_merge_recursive ((array) $nbradiotreff , (array) $treff);

// SLUTT
