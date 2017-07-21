<?php

/* Søker i lokalhistoriske bildebaser til Deichman og legger til:
 - treff i $treffliste
 - antall treff i $antalltreff['slug']
 - antall nye i denne materialtypen i $materialtreff['materialtype']
*/

// http://gastonlibrary.org/omeka/items/browse

// Finne antall først

$rawurl = "http://bildebaser.deichman.no/items/browse?search=<!QUERY!>&output=omeka-xml";
$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm

$xml = get_content($rawurl);

if(substr($xml, 0, 5) == "<?xml") { // vi fikk en XML-fil tilbake
	$xmldata = simplexml_load_string($xml);
	$antalltreff['omekadeichman'] = (int) $xmldata->miscellaneousContainer->pagination->totalResults;
} else {
	$antalltreff['omekadeichman'] = 0;
}

@$materialtreff['bilde'] += $antalltreff['omekadeichman']; 

$omekadeichmantreff = '';

// LASTE TREFFLISTE SOM XML
$xml = get_content($rawurl);

if(substr($xml, 0, 5) == "<?xml") { // vi fikk en XML-fil tilbake

	$xmldata = simplexml_load_string($xml);
	$teller = 0;
	foreach ($xmldata->item as $entry) {
		if ($teller < $makstreff) {

			$omekadeichman[$teller]['slug'] = 'omekadeichman';
			$omekadeichman[$teller]['materialtype'] = 'bilde';
			$omekadeichman[$teller]['kilde'] = 'Lokalhistoriske bildebaser i Oslo (Deichman)';
			$omekadeichman[$teller]['id'] = (string) $entry->attributes()->itemId;
			$omekadeichman[$teller]['url'] = 'http://bildebaser.deichman.no/items/show/' . $entry->attributes()->itemId;

			$omekadeichman[$teller]['bilde'] = $entry->fileContainer->file->src;
			$omekadeichman[$teller]['bilde'] = str_replace ("/original/" , "/fullsize/" , $omekadeichman[$teller]['bilde']);
			$omekadeichman[$teller]['bilde'] = str_replace (".tif" , ".jpg" , $omekadeichman[$teller]['bilde']);
			$omekadeichman[$teller]['bilde'] = str_replace (".JPG" , ".jpg" , $omekadeichman[$teller]['bilde']); // doh...

			foreach ($entry->itemType->elementContainer->element as $elementcontainer) {
				// Dirty tilordne alle
				$fieldid = (string) $elementcontainer->attributes();
				$tempura[$fieldid] = (string) $elementcontainer->elementTextContainer->elementText->text;
			}

			if (isset($tempura['137'])) {
				$omekadeichman[$teller]['ansvar'] = $tempura['137'];
			}

			if (isset($tempura['152'])) {
				$omekadeichman[$teller]['digidato'] = date ("ymd" , strtotime ($tempura['152']) );
			}

				
			if (!isset($omekadeichman[$teller]['ansvar'])) {
				$omekadeichman[$teller]['ansvar'] = 'Ukjent opphavsperson';
			}

			foreach ($entry->elementSetContainer->elementSet->elementContainer->element as $elementcontainer) {

				// Dirty tilordne alle
				$fieldid = (string) $elementcontainer->attributes();
				$tempura[$fieldid] = (string) $elementcontainer->elementTextContainer->elementText->text;
			}

			foreach ($entry->elementSetContainer->elementSet->elementContainer->element as $elementcontainer) {

				// Dirty tilordne alle
				$fieldid = (string) $elementcontainer->attributes();
				$tempura[$fieldid] = (string) $elementcontainer->elementTextContainer->elementText->text;
			}

			$omekadeichman[$teller]['tittel'] = $tempura['50'];
			if (isset($tempura['40'])) {
				$omekadeichman[$teller]['dato'] = filter_var($tempura['40'], FILTER_SANITIZE_NUMBER_INT);
			}

			if (isset($tempura['156'])) {
				$tempsted[] = $tempura['156']; 
			}

			if (isset($tempura['154'])) {
				$tempsted[] = $tempura['154'];
			}

			if (isset($tempura['153'])) {
				$tempsted[] = $tempura['153'];
			}

			$sted = @implode (" / " , $tempsted);
			unset ($tempsted);

			@$omekadeichman[$teller]['beskrivelse'] = "<b>Beskrivelse: </b>" . $tempura['41'] . " " . $tempura['161'] . " <b>Sted:</b> " . $sted . ". ";
			if (isset($omekadeichman[$teller]['ansvar'])) {
				$omekadeichman[$teller]['beskrivelse'] .= "<b>Fotograf:</b> " . $omekadeichman[$teller]['ansvar'] . ". ";
			}

			if (isset($tempura['40'])) {
				$omekadeichman[$teller]['beskrivelse'] .= "<b>Datering:</b> " . $tempura['40'] . ". ";
			}

			if (isset($tempura['148'])) {
				$omekadeichman[$teller]['beskrivelse'] .= "<b>Rettigheter:</b> " . $tempura['148'] . ". ";
			}

			$teller++;
		}
	} // SLUTT PÅ HVERT ENKELT TREFF
} // slutt på "vi fikk XML-fil tilbake

$treff = array_merge_recursive ((array) $omekadeichman , (array) $treff);

// SLUTT

/*
137: Fotograf
156: Gårdsnavn
154: Sted, nærmere bestemt
153: Bydel
157: Farger eller S/H?
159: Navn på giver
160: Navn på kilde til informasjon om bildet
148: Rettighetseier til bildet
152: Dato bildet ble registrert (dd.mm.åååå)
161: Andre kommentarer

50: Tittel 
49: Emne
41: Beskrivelse
40: Dato (format: "mai 1984")
*/



