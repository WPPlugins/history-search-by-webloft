<?php

// Søker i Lurøy biblioteks bildebase og legger til treff i $treffliste
// Kilder: Standard for fotokatalogisering, ABM-skrift #44
// http://www.kulturradet.no/documents/10157/d8681d12-c2f9-446d-88d3-5858f4fc9cfc

$domain = "http://www.luroy.folkebibl.no";
$rawurl = "http://www.luroy.folkebibl.no/cgi-bin/sru-bilde?version=1.2&operation=searchRetrieve&maximumRecords=" . $makstreff . "&query=<!QUERY!>";
$antalltreff['luroybilder'] = ''; // nullstiller i tilfelle søket feiler
$luroybildertreff = ''; // nullstiller treff
$srw = '';

$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm

$sru_datafil = get_content($rawurl);

// Herfra kan det feile spektakulært med XML

if(substr($sru_datafil, 0, 5) == "<?xml") { // vi fikk en XML-fil tilbake

	$sru_data = simplexml_load_string($sru_datafil); 
	$namespaces = $sru_data->getNameSpaces(true);
	$srw = $sru_data->children($namespaces['SRU']); // alle som er srw:ditten og srw:datten
	
	$antalltreff['luroybilder'] = (int) $srw->numberOfRecords;
	@$materialtreff['bilde'] += $antalltreff['luroybilder']; 

} // for det som følger er ikke hva vi fikk tilbake så viktig - det vil bare gi 0 treff

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
- ID : unik ID
- URL : For å lenke til objektet i kontekst
- bilde (til illustrasjon, f.eks. omslagsbilde)
- tittel
- ansvar (forfatter, fotograf etc.)
- beskrivelse (kan settes sammen av ymse)
- digidato : Dato for digitalisering
- dato : Opphavsdato
*/


while ($record = $journals->next()) {

$beskrivelse = '';

	// Digitalisert dato
	if ($record->getField("008")) {
		$luroybildertreff[$hitcounter]['digidato'] = ($record->getField("008"));
		$luroybildertreff[$hitcounter]['digidato'] = trim(substr($luroybildertreff[$hitcounter]['digidato'] , 5, 9));
	}

	// ID
	if ($record->getField("001")) {
		$luroybildertreff[$hitcounter]['id'] = ($record->getField("001"));
		$luroybildertreff[$hitcounter]['id'] = trim(substr($luroybildertreff[$hitcounter]['id'] , 5));
	}

	// Diverse
	$luroybildertreff[$hitcounter]['slug'] = 'luroybilder';
	$luroybildertreff[$hitcounter]['kilde'] = "Lurøy biblioteks bildebase";
	$luroybildertreff[$hitcounter]['materialtype'] = "bilde";

	// URL og bilde er det samme
	if ($record->getField("856")) {
		if ($record->getField("856")->getSubfield("u")) {
			$luroybildertreff[$hitcounter]['bilde'] = $record->getField("856")->getSubfield("u");
			$luroybildertreff[$hitcounter]['bilde'] = $domain . substr($luroybildertreff[$hitcounter]['bilde'], 5); // fjerne feltkoden i 	starten
		}
	}

	@$luroybildertreff[$hitcounter]['url'] = $domain . "/cgi-bin/websok-bilde?tnr=" . trim($luroybildertreff[$hitcounter]['id']);


	// Tittel, ev. med årstall i 260
	if ($record->getField("245")) {
		if ($record->getField("245")->getSubfield("a")) {
			$luroybildertreff[$hitcounter]['tittel'] = $record->getField("245")->getSubfield("a");
			$luroybildertreff[$hitcounter]['tittel'] = substr($luroybildertreff[$hitcounter]['tittel'], 5); // fjerne feltkoden i starten
		}
	}

	if ($record->getField("260")) { // opphavsdato: sist i tittelen OG til eget felt
		if ($record->getField("260")->getSubfield("c")) {
			$luroybildertreff[$hitcounter]['tittel'] .= " (" . substr($record->getField("260")->getSubfield("c") , 5) . ")";
			$luroybildertreff[$hitcounter]['dato'] = trim(substr($record->getField("260")->getSubfield("c") , 5));
			$luroybildertreff[$hitcounter]['dato'] = trim(str_replace ("ca." , "" , $luroybildertreff[$hitcounter]['dato']));
		}
	}

	// Ansvar
	if ($record->getField("100")) {
		if ($record->getField("100")->getSubfield("a")) {
			$luroybildertreff[$hitcounter]['ansvar'] = $record->getField("100")->getSubfield("a");
			$luroybildertreff[$hitcounter]['ansvar'] = substr($luroybildertreff[$hitcounter]['ansvar'], 5); // fjerne feltkoden i starten
		}
		if ($record->getField("100")->getSubfield("d")) {
			$luroybildertreff[$hitcounter]['ansvar'] .= " (" . substr($record->getField("100")->getSubfield("d") , 5) . ")";
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
		$luroybildertreff[$hitcounter]['beskrivelse'] = "<b>Format: </b>" . implode (" / " , $beskrivelse) . ". ";
	}
	
	// Mer beskrivelse: Plassering (590)
	if ($record->getField("590")) {
		if ($record->getField("590")->getSubfield("a")) {
			$luroybildertreff[$hitcounter]['beskrivelse'] .= "<b>Plassering: </b>" . substr ($record->getField("590")->getSubfield("a") , 5);
		}
		if ($record->getField("590")->getSubfield("b")) {
			$luroybildertreff[$hitcounter]['beskrivelse'] .= " - " . substr ($record->getField("590")->getSubfield("b") , 5);
		}
		$luroybildertreff[$hitcounter]['beskrivelse'] .= ". ";
	}

	// REPETERBARE FELTER

	foreach ($record->getFields() as $tag => $subfields) {

		// Mer beskrivelse: Generell note (500)
		if ($tag == '500') {
			foreach ($subfields->getSubfields() as $code => $value) {
				$ettfelt[(string) $code] = substr((string) $value, 5);
			}
			$noter[] = $ettfelt['a'];
		}
	
		// Mer beskrivelse: Generell note (505)
		if ($tag == '505') {
			foreach ($subfields->getSubfields() as $code => $value) {
				$ettfelt[(string) $code] = substr((string) $value, 5);
			}
			$noter[] = $ettfelt['a'];
		}
			
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

	if (@is_array($emne)) {	
		$luroybildertreff[$hitcounter]['beskrivelse'] .= "<b>Emneord: </b>" . implode (" ; " , $emne) . ". ";
	}

	if (@is_array($person)) {
		$luroybildertreff[$hitcounter]['beskrivelse'] .= "<b>Personer: </b>" . implode (" ; " , $person) . ". ";
	}

	if (@is_array($noter)) {
		$luroybildertreff[$hitcounter]['beskrivelse'] .= "<b>Noter: </b>" . implode (" ; " , $noter) . ". ";
	}

	$emne = '';
	$person = '';
	$noter = '';	
	
	$hitcounter++;	
} // SLUTT PÅ HVERT ENKELT TREFF

$treff = array_merge_recursive ((array) $luroybildertreff , (array) $treff);

// SLUTT
