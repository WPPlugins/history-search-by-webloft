<?php

/* Søker i Nore og Uvdal biblioteks bildebase og legger til:
 - treff i $treffliste
 - antall treff i $antalltreff['slug']
 - antall nye i denne materialtypen i $materialtreff['materialtype']
*/

// Kilder: Standard for fotokatalogisering, ABM-skrift #44
// http://www.kulturradet.no/documents/10157/d8681d12-c2f9-446d-88d3-5858f4fc9cfc

$rawurl = "http://asp.bibliotekservice.no/nuf_foto/nome.aspx?version=1.2&operation=searchRetrieve&maximumRecords=" . $makstreff . "&query=<!QUERY!>+and+ma=foto&recordSchema=marcxchange";

$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm
$antalltreff['nuvbilder'] = ''; // nullstiller i tilfelle søket feiler
$nuvbildertreff = '';
$srw = '';
$sru_datafil = get_content($rawurl);

if(substr($sru_datafil, 0, 5) == "<?xml") { // vi fikk en XML-fil tilbake
	$sru_data    = simplexml_load_string($sru_datafil);

	$namespaces = $sru_data->getNameSpaces(true);
	$srw        = $sru_data->children($namespaces['srw']); // alle som er srw:ditten og srw:datten
	$antalltreff['nuvbilder'] = (int) $srw->numberOfRecords;
	@$materialtreff['bilde'] += $antalltreff['nuvbilder']; 
}

// Så ta selve filen og plukke ut det vi skal ha

$hepphepp = str_replace("marc:", "", $sru_datafil);
$hepphepp = strip_tags($hepphepp, "<record><leader><controlfield><datafield><subfield>");
$hepphepp = stristr($hepphepp, "<record");

$newfile = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
$newfile .= "<collection>\n";
$newfile .= $hepphepp;
$newfile .= "</collection>";

// Retrieve a set of MARC records from a file

require_once 'File/MARCXML.php';

$journals = new File_MARCXML($newfile, File_MARC::SOURCE_STRING);

// Iterate through the retrieved records

$hitcounter = 0;

/*
*kilde
*ID
*slug
*url
*bilde
*tittel	
*ansvar
*beskrivelse
*digidato
*dato
*materialtype
*/

while ($record = $journals->next()) {

$beskrivelse = '';

	// ID
	if ($record->getField("001")) {
		$nuvbildertreff[$hitcounter]['id'] = ($record->getField("001"));
		$nuvbildertreff[$hitcounter]['id'] = trim(substr($nuvbildertreff[$hitcounter]['id'] , 5));
	}

	// Digitalisert dato
	if ($record->getField("008")) {
		$nuvbildertreff[$hitcounter]['digidato'] = ($record->getField("008"));
		$nuvbildertreff[$hitcounter]['digidato'] = trim(substr($nuvbildertreff[$hitcounter]['digidato'] , 5));
	}

	// Diverse
	$nuvbildertreff[$hitcounter]['slug'] = 'nuvbilder';
	$nuvbildertreff[$hitcounter]['kilde'] = "Nore og Uvdal biblioteks bildebase";
	$nuvbildertreff[$hitcounter]['materialtype'] = "bilde";

	// URL og bilde er det samme
	if ($record->getField("856")) {
		if ($record->getField("856")->getSubfield("u")) {
			$nuvbildertreff[$hitcounter]['bilde'] = $record->getField("856")->getSubfield("u");
			$nuvbildertreff[$hitcounter]['bilde'] = substr($nuvbildertreff[$hitcounter]['bilde'], 5); // fjerne feltkoden i starten
		}
	}
	$nuvbildertreff[$hitcounter]['url'] = trim($nuvbildertreff[$hitcounter]['bilde']);

	// Tittel, ev. med årstall i 260
	if ($record->getField("245")) {
		if ($record->getField("245")->getSubfield("a")) {
			$nuvbildertreff[$hitcounter]['tittel'] = $record->getField("245")->getSubfield("a");
			$nuvbildertreff[$hitcounter]['tittel'] = substr($nuvbildertreff[$hitcounter]['tittel'], 5); // fjerne feltkoden i starten
		}
		if ($record->getField("245")->getSubfield("c")) {
			$nuvbildertreff[$hitcounter]['tittel'] .= ", " . substr($record->getField("245")->getSubfield("c"), 5);
		}
	}

	// Dato kan være 2.12.1900, 10.6.1900, 10.06.1900, 1900, 1900 - 1901, 1920-åra, 1920 åra, ca. 1900, ca 1900 ... flott katalogiseringspraksis.
	if ($record->getField("260")) {
		if ($record->getField("260")->getSubfield("c")) {
			$nuvbildertreff[$hitcounter]['tittel'] .= " (" . substr($record->getField("260")->getSubfield("c") , 5) . ")";
			$dato = trim(substr($record->getField("260")->getSubfield("c") , 5));

		// Prøve å klemme ut en dato vi faktisk kan bruke til noe
		// Hvis andre eller tredje tegn er et punkt er det sannsynligvis d.mm.åååå eller dd.mm.åååå (eller d.m.åååå / dd.m.åååå)
			if ((strpos($dato , ".") == "1") || (strpos($dato , ".") == "2")) {
				$fleskesuppe = explode ("." , $dato);
				$nydato = $fleskesuppe[2] . str_pad($fleskesuppe[1],2,"0",STR_PAD_LEFT) . str_pad($fleskesuppe[0],2,"0",STR_PAD_LEFT);
			} else { // I give up
				$nydato = $dato;
			}
			$nydato = str_replace ("ca" , "" , $nydato); // mehehehe
			$nuvbildertreff[$hitcounter]['dato'] = trim($nydato);
			$nydato = '';
		}
	}

	// Ansvar
	if ($record->getField("260")) {
		if ($record->getField("260")->getSubfield("b")) {
			$nuvbildertreff[$hitcounter]['ansvar'] = $record->getField("260")->getSubfield("b");
			$nuvbildertreff[$hitcounter]['ansvar'] = substr($nuvbildertreff[$hitcounter]['ansvar'], 5); // fjerne feltkoden i starten
		}
	}

	// beskrivelse
	$nuvbildertreff[$hitcounter]['beskrivelse'] = '';

	// REPETERBARE FELTER

	foreach ($record->getFields() as $tag => $subfields) {

		// Emneord - Repeterbart!! (650, 651 og 653)

		if ($tag == '650') {
			foreach ($subfields->getSubfields() as $code => $value) {
				$ettfelt[(string) $code] = substr((string) $value, 5);
			}
			$emne[] = $ettfelt['a'];
		}

		if ($tag == '651') {
			foreach ($subfields->getSubfields() as $code => $value) {
				$ettfelt[(string) $code] = substr((string) $value, 5);
			}
			$emne[] = $ettfelt['a'];
		}

		if ($tag == '653') {
			foreach ($subfields->getSubfields() as $code => $value) {
				$ettfelt[(string) $code] = substr((string) $value, 5);
			}
			$emne[] = $ettfelt['a'];
		}

	}

	if (is_array($emne)) {	
		$nuvbildertreff[$hitcounter]['beskrivelse'] .= "<b>Emneord: </b>" . implode (" ; " , $emne) . ". ";
	}

	$emne = '';

	// Siste på beskrivelse: Datering hvis dato finnes
	if (trim($nuvbildertreff[$hitcounter]['dato']) != '') {
		$nuvbildertreff[$hitcounter]['beskrivelse'] .= "<b>Datering: </b>" . $nuvbildertreff[$hitcounter]['dato'] . ". ";
	} else {
		$nuvbildertreff[$hitcounter]['beskrivelse'] .= "<b>Datering: </b>Ukjent. ";
	}

	// Det finnes et eget "Bilde mangler"-bilde. How convenient. 
	if (trim($nuvbildertreff[$hitcounter]['bilde'] == '')) { 
		$nuvbildertreff[$hitcounter]['bilde'] = "http://asp.bibliotekservice.no/foto/nuf/ARK01614%20A.JPG";
	}

	$hitcounter++;	
} // SLUTT PÅ HVERT ENKELT TREFF

$treff = array_merge_recursive ((array) $nuvbildertreff , (array) $treff);

// SLUTT
