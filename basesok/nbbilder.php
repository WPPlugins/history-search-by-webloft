<?php

/* Søker i Nasjonalbibliotekets bilder og legger til:
 - treff i $treffliste
 - antall treff i $antalltreff['slug']
 - antall nye i denne materialtypen i $materialtreff['materialtype']
*/

$rawurl = "http://www.nb.no/services/search/v2/search?q=<!QUERY!>&fq=mediatype:Bilder&fq=contentClasses:public&fq=digital:Ja&itemsPerPage=" . $makstreff . "&ft=true";

$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm
$antalltreff['nbbilder'] = ''; // nullstiller i tilfelle søket feiler

$nbbildertreff = '';

// LASTE TREFFLISTE SOM XML
$xmlfile = get_content($rawurl);

if(substr($xmlfile, 0, 5) == "<?xml") { // vi fikk en XML-fil tilbake
	$xmldata = simplexml_load_string($xmlfile);

	// FINNE ANTALL TREFF
	$antalltreff['nbbilder'] = (int) substr(stristr($xmldata->subtitle, " of ") , 4);
	@$materialtreff['bilde'] += $antalltreff['nbbilder']; 

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

			$nbbildertreff[$teller]['bilde'] = $childxmldata->location->url;
	
			if (stristr($nb->contentclasses , "jp2")) {
				$nbbildertreff[$teller]['bilde'] = "http://www.nb.no/services/image/resolver?url_ver=geneza&urn=" . $urn . "&maxLevel=6&level=2&col=0&row=0&resX=6000&resY=6000&tileWidth=2048&tileHeight=2048";
			}
			
			$nbbildertreff[$teller]['url'] = "http://urn.nb.no/" . $urn;
			if ((isset($nb->subjectname)) && ($nb->subjectname != '')) {
				$nbbildertreff[$teller]['tittel'] = (string) $nb->subjectname;
			} else {
				$nbbildertreff[$teller]['tittel'] = '(Uten tittel)';
			}

			if ((isset($nb->mainentry)) && ($nb->mainentry != '')) {
				$nbbildertreff[$teller]['ansvar'] = $nb->mainentry;
			} else {
				$nbbildertreff[$teller]['ansvar'] = 'N.N.';
			}


			if ((isset($nb->year)) && (trim($nb->year) != '')) {
				$nbbildertreff[$teller]['utgitt'] = (string) $nb->year;
			}

			$nbbildertreff[$teller]['beskrivelse'] = str_ireplace ("prot: ", "<b>Protokoll: </b>" , $entry->summary); // Fjerne "Prot: " i starten
			$nbbildertreff[$teller]['beskrivelse'] = str_replace ("<br>" , ". " , $nbbildertreff[$teller]['beskrivelse']);

			if (isset($nb->subjectname)) {
				$nbbildertreff[$teller]['beskrivelse'] .= "<b>Motiv: </b>" . $nb->subjectname . ". ";
			}

			if (isset($nb->subjecttopic)) {
				$nbbildertreff[$teller]['beskrivelse'] .= "<b>Emneord: </b>" . $nb->subjecttopic . ". ";
			}

			if (isset($nb->places)) {
				$nbbildertreff[$teller]['beskrivelse'] .= "<b>Steder: </b>" . $nb->places . ". ";
			}

			$nbbildertreff[$teller]['kilde'] = "Nasjonalbiblioteket - bilder";
			$nbbildertreff[$teller]['slug'] = "nbbilder";
			$nbbildertreff[$teller]['materialtype'] = "bilde";
			$nbbildertreff[$teller]['id'] = $urn;
			if (!stristr($urn , "URN:NBN:no-nb_foto")) { // bogus URN som vi ikke kan få dato fra
				$nbbildertreff[$teller]['digidato'] = substr ($urn , 24, 6);
			}
			$nbbildertreff[$teller]['dato'] = str_replace ("-" , "" , $nb->date);	

			$teller++;
		}
	} // SLUTT PÅ HVERT ENKELT TREFF

} // Slutt på "vi fikk XML tilbake"

$treff = array_merge_recursive ((array) $nbbildertreff , (array) $treff);

// SLUTT
