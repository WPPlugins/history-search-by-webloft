<?php

/* Søker i hele Bokhylla og legger til:
 - treff i $treffliste
 - antall treff i $antalltreff['slug']
 - antall nye i denne materialtypen i $materialtreff['materialtype']
*/

$rawurl = "http://www.nb.no/services/search/v2/search?q=<!QUERY!>&fq=mediatype:(" . utf8_decode("Bøker") . ")&fq=contentClasses:(bokhylla%20OR%20public)&fq=digital:Ja&itemsPerPage=" . $makstreff . "&ft=false";

$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm
$antalltreff['bokhylla'] = ''; // nullstiller i tilfelle søket feiler

@$materialtreff['bok'] += $antalltreff['bokhylla']; 

$bokhyllatreff = '';

// LASTE TREFFLISTE SOM XML
$xml = get_content($rawurl);

if(substr($xml, 0, 5) == "<?xml") { // vi fikk en XML-fil tilbake

	$xmldata = simplexml_load_string($xml);
	
	// FINNE ANTALL TREFF
	$antalltreff['bokhylla'] = (int) substr(stristr($xmldata->subtitle, " of ") , 4);
	@$materialtreff['bok'] += $antalltreff['bokhylla']; 	

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

			$bokhyllatreff[$teller]['tittel'] = (string) $entry->title;
			$bokhyllatreff[$teller]['ansvar'] = (string) $nb->namecreator;

			// UTGITT

			unset ($utgitt);
			if (isset($childxmldata->originInfo->place[1])) {
				$utgitt[] = $childxmldata->originInfo->place[1];
			}

			if (isset($childxmldata->originInfo->publisher)) {
				$utgitt[] = $childxmldata->originInfo->publisher;
			}

			if (isset($childxmldata->originInfo->dateIssued[0])) {
				$utgitt[] = $childxmldata->originInfo->dateIssued[0];
			}
			$bokhyllatreff[$teller]['utgitt'] = implode (" " , $utgitt);

			if (isset($childxmldata->physicalDescription->extent)) {
				$bokhyllatreff[$teller]['omfang'] = (string) $childxmldata->physicalDescription->extent;
			}

			// BESKRIVELSE
			$bokhyllatreff[$teller]['beskrivelse'] = "<b>Utgitt: </b>" . $bokhyllatreff[$teller]['utgitt'] . ". ";
			$bokhyllatreff[$teller]['beskrivelse'] .= "<b>Omfang: </b>" . $bokhyllatreff[$teller]['omfang'] . ". ";
			

			if (isset($childxmldata->note)) {
				//$bokhyllatreff[$teller]['beskrivelse'] .= $childxmldata->note . ". ";
			}

			// BOKOMSLAG, SE http://www-sul.stanford.edu/iiif/image-api/1.1/#parameters
			if (stristr($nb->urn , ";")) {
				$tempura = explode (";" , $nb->urn);
				$urn = trim($tempura[1]); // vi tar nummer 2 
			} else {
				$urn = $nb->urn[0];
			}
			if ($urn != "") {
				$delavurn = substr($urn , 8);
				$bokhyllatreff[$teller]['bilde'] = "http://bokforsider.webloft.no/urn/" . $delavurn . ".jpg";
			} else {
				$bokhyllatreff[$teller]['bilde'] = $generiskbokomslag; // DEFAULTOMSLAG
			}

			$bokhyllatreff[$teller]['digidato'] = substr ($urn , 22, 8);
			$bokhyllatreff[$teller]['dato'] = str_replace ("-" , "" , $nb->date);	
			$bokhyllatreff[$teller]['url'] = "http://urn.nb.no/" . $urn;
			$bokhyllatreff[$teller]['kilde'] = "Bokhylla - alt";
			$bokhyllatreff[$teller]['slug'] = "bokhylla";
			$bokhyllatreff[$teller]['materialtype'] = "bok";
			$bokhyllatreff[$teller]['id'] = $urn;
			$teller++;
		}
	} // SLUTT PÅ HVERT ENKELT TREFF

} // slutt på "vi fikk XML-fil tilbake

$treff = array_merge_recursive ((array) $bokhyllatreff , (array) $treff);

// SLUTT
