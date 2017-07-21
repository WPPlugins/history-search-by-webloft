<?php

/* Søker i Nasjonalbibliotekets digitaliserte privatarkivmateriale og legger til:
 - treff i $treffliste
 - antall treff i $antalltreff['slug']
 - antall nye i denne materialtypen i $materialtreff['materialtype']
*/

$rawurl = "http://www.nb.no/services/search/v2/search?q=<!QUERY!>&fq=mediatype:Privatarkivmateriale&fq=contentClasses:public&fq=digital:Ja&itemsPerPage=" . $makstreff . "&ft=true";

$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm
$antalltreff['nbprivatarkiv'] = ''; // nullstiller i tilfelle søket feiler

$nbprivatarkivtreff = '';

// LASTE TREFFLISTE SOM XML
$xmlfile = get_content($rawurl);

if(substr($xmlfile, 0, 5) == "<?xml") { // vi fikk en XML-fil tilbake
	$xmldata = simplexml_load_string($xmlfile);

	// FINNE ANTALL TREFF
	$antalltreff['nbprivatarkiv'] = (int) substr(stristr($xmldata->subtitle, " of ") , 4);
	@$materialtreff['manuskript'] += $antalltreff['nbprivatarkiv']; 

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

			$nbprivatarkivtreff[$teller]['bilde'] = "http://www.nb.no/services/image/resolver?url_ver=geneza&urn=" . $urn . "_0001&maxLevel=5&level=4&col=0&row=0&resX=9000&resY=9000&tileWidth=1024&tileHeight=1024";
			
			$nbprivatarkivtreff[$teller]['url'] = "http://urn.nb.no/" . $urn;
			if ((isset($entry->title)) && ($entry->title != '')) {
				$nbprivatarkivtreff[$teller]['tittel'] = (string) $entry->title;
			} else {
				$nbprivatarkivtreff[$teller]['tittel'] = '(Uten tittel)';
			}

			if ((isset($nb->mainentry)) && ($nb->mainentry != '')) {
				$nbprivatarkivtreff[$teller]['ansvar'] = (string) $nb->mainentry;
			} else {
				$nbprivatarkivtreff[$teller]['ansvar'] = 'N.N.';
			}

			$nbprivatarkivtreff[$teller]['beskrivelse'] = ''; // ikke beskrivelse for disse 

			$nbprivatarkivtreff[$teller]['kilde'] = "Nasjonalbiblioteket - privatarkivmateriale";
			$nbprivatarkivtreff[$teller]['slug'] = "nbprivatarkiv";
			$nbprivatarkivtreff[$teller]['materialtype'] = "manuskript";
			$nbprivatarkivtreff[$teller]['id'] = $urn;
			$nbprivatarkivtreff[$teller]['dato'] = '';
			$nbprivatarkivtreff[$teller]['digidato'] = '';

			$teller++;
		}
	} // SLUTT PÅ HVERT ENKELT TREFF

} // Slutt på "vi fikk XML tilbake"

$treff = array_merge_recursive ((array) $nbprivatarkivtreff , (array) $treff);

// SLUTT
