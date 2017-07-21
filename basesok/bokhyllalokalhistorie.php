<?php

/* Søker i Bokhylla emne lokalhistorie og legger til:
 - treff i $treffliste
 - antall treff i $antalltreff['slug']
 - antall nye i denne materialtypen i $materialtreff['materialtype']
*/

$rawurl = "http://www.nb.no/services/search/v2/search?q=<!QUERY!>&fq=mediatype:(" . utf8_decode("Bøker") . ")&fq=contentClasses:(bokhylla%20OR%20public)&fq=subject:lokalhistorie&fq=digital:Ja&itemsPerPage=" . $makstreff . "&ft=false";

$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm
$antalltreff['bokhyllalokalhistorie'] = ''; // nullstiller i tilfelle søket feiler
$bokhyllalokalhistorietreff = '';

// LASTE TREFFLISTE SOM XML
$xml = get_content($rawurl);

if(substr($xml, 0, 5) == "<?xml") { // vi fikk en XML-fil tilbake

	$xmldata = simplexml_load_string($xml);
	
	// FINNE ANTALL TREFF
	$antalltreff['bokhyllalokalhistorie'] = (int) substr(stristr($xmldata->subtitle, " of ") , 4);
	@$materialtreff['bok'] += $antalltreff['bokhyllalokalhistorie']; 

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

			$bokhyllalokalhistorietreff[$teller]['tittel'] = $childxmldata->titleInfo->title;
			if (isset($childxmldata->titleInfo->subTitle)) {
				$bokhyllalokalhistorietreff[$teller]['tittel'] .= " : " . $childxmldata->titleInfo->subTitle;
			}
			if (isset($childxmldata->titleInfo->partNumber)) {
				$bokhyllalokalhistorietreff[$teller]['tittel'] .= " : " . $childxmldata->titleInfo->partNumber;
			}
			if (isset($childxmldata->titleInfo->partName)) {
				$bokhyllalokalhistorietreff[$teller]['tittel'] .= " : " . $childxmldata->titleInfo->partName;
			}

			$bokhyllalokalhistorietreff[$teller]['ansvar'] = $nb->namecreator;

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
			$bokhyllalokalhistorietreff[$teller]['utgitt'] = implode (" " , $utgitt);

			if (isset($childxmldata->physicalDescription->extent)) {
				$bokhyllalokalhistorietreff[$teller]['omfang'] = $childxmldata->physicalDescription->extent;
			}

			// BESKRIVELSE
			$bokhyllalokalhistorietreff[$teller]['beskrivelse'] = "<b>Utgitt: </b>" . $bokhyllalokalhistorietreff[$teller]['utgitt'] . ". ";
			$bokhyllalokalhistorietreff[$teller]['beskrivelse'] .= "<b>Omfang: </b>" . $bokhyllalokalhistorietreff[$teller]['omfang'] . ". ";
			

			if (isset($childxmldata->note)) {
				//$bokhyllalokalhistorietreff[$teller]['beskrivelse'] .= $childxmldata->note . ". ";
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
				$bokhyllalokalhistorietreff[$teller]['bilde'] = "http://bokforsider.webloft.no/urn/" . $delavurn . ".jpg";
			} else {
				$bokhyllalokalhistorietreff[$teller]['bilde'] = $generiskbokomslag; // DEFAULTOMSLAG
			}

			$bokhyllalokalhistorietreff[$teller]['digidato'] = substr ($urn , 22, 8);
			$bokhyllalokalhistorietreff[$teller]['dato'] = str_replace ("-" , "" , $nb->date);	
			$bokhyllalokalhistorietreff[$teller]['url'] = "http://urn.nb.no/" . $urn;
			$bokhyllalokalhistorietreff[$teller]['kilde'] = "Bokhylla - lokalhistorie";
			$bokhyllalokalhistorietreff[$teller]['slug'] = "bokhyllalokalhistorie";
			$bokhyllalokalhistorietreff[$teller]['materialtype'] = "bok";
			$bokhyllalokalhistorietreff[$teller]['id'] = $urn;
			$teller++;
		}
	} // SLUTT PÅ HVERT ENKELT TREFF

} // slutt på "vi fikk XML-fil tilbake

$treff = array_merge_recursive ((array) $bokhyllalokalhistorietreff , (array) $treff);

// SLUTT
