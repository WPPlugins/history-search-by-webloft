<?php

/* Søker i Sandefjord biblioteks bildebase og legger til:
 - treff i $treffliste
 - antall treff i $antalltreff['slug']
 - antall nye i denne materialtypen i $materialtreff['materialtype']
*/

// Kilder: Standard for fotokatalogisering, ABM-skrift #44
// http://www.kulturradet.no/documents/10157/d8681d12-c2f9-446d-88d3-5858f4fc9cfc

$domain = "https://www.sandefjord.folkebibl.no";
$rawurl = "https://www.sandefjord.folkebibl.no/cgi-bin/sru-lokalsamling?version=1.2&operation=searchRetrieve&maximumRecords=" . $makstreff . "&query=<!QUERY!>";

$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm
$antalltreff['sandefjordlokal'] = ''; // nullstiller i tilfelle søket feiler
$sandefjordlokaltreff = '';
$srw = '';

$sru_datafil = get_content($rawurl);

if(substr($sru_datafil, 0, 5) == "<?xml") { // vi fikk en XML-fil tilbake
	$sru_data    = simplexml_load_string($sru_datafil);

	$namespaces = $sru_data->getNameSpaces(true);
	$srw        = $sru_data->children($namespaces['SRU']); // alle som er srw:ditten og srw:datten
	$antalltreff['sandefjordlokal'] = (int) $srw->numberOfRecords;

	@$materialtreff['bilde'] += $antalltreff['sandefjordlokal']; 
}

// Så ta selve filen og plukke ut det vi skal ha

$hepphepp = str_replace("marcxchange:", "", $sru_datafil);
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
ID
slug
url
bilde
tittel
ansvar
beskrivelse
*/

while ($record = $journals->next()) {

$beskrivelse = '';

	// ID
	if ($record->getField("001")) {
		$sandefjordlokaltreff[$hitcounter]['id'] = ($record->getField("001"));
		$sandefjordlokaltreff[$hitcounter]['id'] = trim(substr($sandefjordlokaltreff[$hitcounter]['id'] , 5));
	}

	// Digitalisert dato
	if ($record->getField("008")) {
		$sandefjordlokaltreff[$hitcounter]['digidato'] = ($record->getField("008"));
		$sandefjordlokaltreff[$hitcounter]['digidato'] = trim(substr($sandefjordlokaltreff[$hitcounter]['digidato'] , 5));
	}

	// Diverse
	$sandefjordlokaltreff[$hitcounter]['slug'] = 'sandefjordlokal';
	$sandefjordlokaltreff[$hitcounter]['kilde'] = "Sandefjord biblioteks lokalhistoriebase";
	$sandefjordlokaltreff[$hitcounter]['materialtype'] = "bilde";

	// URL og bilde er det samme
	if ($record->getField("856")) {
		if ($record->getField("856")->getSubfield("u")) {
			$sandefjordlokaltreff[$hitcounter]['bilde'] = $record->getField("856")->getSubfield("u");
			$sandefjordlokaltreff[$hitcounter]['bilde'] = $domain . substr($sandefjordlokaltreff[$hitcounter]['bilde'], 5); // fjerne feltkoden i starten
		}
	}

	$sandefjordlokaltreff[$hitcounter]['url'] = $sandefjordlokaltreff[$hitcounter]['bilde']; // Har ikke egne poster, så bilde = url

	// Tittel, ev. med årstall i 260
	if ($record->getField("245")) {
		if ($record->getField("245")->getSubfield("a")) {
			$sandefjordlokaltreff[$hitcounter]['tittel'] = $record->getField("245")->getSubfield("a");
			$sandefjordlokaltreff[$hitcounter]['tittel'] = substr($sandefjordlokaltreff[$hitcounter]['tittel'], 5); // fjerne feltkoden i starten
		}
	}
	if ($record->getField("260")) {
		if ($record->getField("260")->getSubfield("c")) {
			$sandefjordlokaltreff[$hitcounter]['tittel'] .= " (" . substr($record->getField("260")->getSubfield("c") , 5) . ")";
			$sandefjordlokaltreff[$hitcounter]['dato'] = trim(substr($record->getField("260")->getSubfield("c") , 5));
			$sandefjordlokaltreff[$hitcounter]['dato'] = trim(str_replace (":" , "-" , $sandefjordlokaltreff[$hitcounter]['dato']));

		}
	}

	// Ansvar
	if ($record->getField("100")) {
		if ($record->getField("100")->getSubfield("a")) {
			$sandefjordlokaltreff[$hitcounter]['ansvar'] = $record->getField("100")->getSubfield("a");
			$sandefjordlokaltreff[$hitcounter]['ansvar'] = substr($sandefjordlokaltreff[$hitcounter]['ansvar'], 5); // fjerne feltkoden i starten
		}
		if ($record->getField("100")->getSubfield("d")) {
			$sandefjordlokaltreff[$hitcounter]['ansvar'] .= " (" . substr($record->getField("100")->getSubfield("d") , 5) . ")";
		}
	}

	// Beskrivelse
	if ($record->getField("300")) {
		if ($record->getField("300")->getSubfield("a")) {
			$beskrivelse[] = substr($record->getField("300")->getSubfield("a") , 5);
		}
		if ($record->getField("300")->getSubfield("b")) {
			$beskrivelse[] = str_replace ("- " , "" , substr($record->getField("300")->getSubfield("b") , 5));
		}
		if ($record->getField("300")->getSubfield("c")) {
			$beskrivelse[] = substr($record->getField("300")->getSubfield("c") , 5);
		}
		$sandefjordlokaltreff[$hitcounter]['beskrivelse'] = "<b>Format: </b>" . implode (" / " , $beskrivelse) . ". ";
	}
	
	// Mer beskrivelse: Generell note (500)
	if ($record->getField("500")) {
		if ($record->getField("500")->getSubfield("a")) {
			$sandefjordlokaltreff[$hitcounter]['beskrivelse'] .= substr ($record->getField("500")->getSubfield("a") , 5) . ". ";
		}
	}

	// Mer beskrivelse: Generell note (505)
	if ($record->getField("505")) {
		if ($record->getField("505")->getSubfield("a")) {
			@$sandefjordlokaltreff[$hitcounter]['beskrivelse'] .= "<b>Motiv: </b>" . substr ($record->getField("505")->getSubfield("a") , 5) . ". ";
		}
	}

	// Mer beskrivelse: Plassering (590)
	if ($record->getField("590")) {
		if ($record->getField("590")->getSubfield("a")) {
			$sandefjordlokaltreff[$hitcounter]['beskrivelse'] .= "<b>Plassering: </b>" . substr ($record->getField("590")->getSubfield("a") , 5);
		}
		if ($record->getField("590")->getSubfield("b")) {
			$sandefjordlokaltreff[$hitcounter]['beskrivelse'] .= " - " . substr ($record->getField("590")->getSubfield("b") , 5);
		}
		$sandefjordlokaltreff[$hitcounter]['beskrivelse'] .= ". ";
	}

	// REPETERBARE FELTER

	foreach ($record->getFields() as $tag => $subfields) {

		// Personer - repeterbart (600)

		if ($tag == '600') {
			foreach ($subfields->getSubfields() as $code => $value) {
				$ettfelt[(string) $code] = substr((string) $value, 5);
			}
			$person[] = $ettfelt['a'];
		}

		// Emneord - Repeterbart!! (650)

		if ($tag == '650') {
			foreach ($subfields->getSubfields() as $code => $value) {
				$ettfelt[(string) $code] = substr((string) $value, 5);
			}
			$emne[] = $ettfelt['a'];
		}

	}

	if (is_array($emne)) {	
		$sandefjordlokaltreff[$hitcounter]['beskrivelse'] .= "<b>Emneord: </b>" . implode (" ; " , $emne) . ". ";
	}

	if (@is_array($person)) {
		$sandefjordlokaltreff[$hitcounter]['beskrivelse'] .= "<b>Personer: </b>" . implode (" ; " , $person) . ". ";
	}

	$emne = '';
	$person = '';


	$hitcounter++;	
} // SLUTT PÅ HVERT ENKELT TREFF

$treff = array_merge_recursive ((array) $sandefjordlokaltreff , (array) $treff);

// SLUTT
