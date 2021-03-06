<?php

// Søker i Telemarksbilder og legger til treff i $treffliste
// Kilder: Standard for fotokatalogisering, ABM-skrift #44
// http://www.kulturradet.no/documents/10157/d8681d12-c2f9-446d-88d3-5858f4fc9cfc

$domain = "http://telebib.tm.fylkesbibl.no";
$rawurl = "http://telebib.tm.fylkesbibl.no/cgi-bin/sru-bilde?version=1.2&operation=searchRetrieve&maximumRecords=" . $makstreff . "&query=<!QUERY!>";
$antalltreff['telemarksbilder'] = ''; // nullstiller i tilfelle søket feiler
$telemarksbildertreff = ''; // nullstiller treff
$srw = '';

$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm
rop ($rawurl);
$sru_datafil = get_content($rawurl);

// Herfra kan det feile spektakulært med XML

if(substr($sru_datafil, 0, 5) == "<?xml") { // vi fikk en XML-fil tilbake

	$sru_data = simplexml_load_string($sru_datafil); 
	$namespaces = $sru_data->getNameSpaces(true);
	$srw = $sru_data->children($namespaces['SRU']); // alle som er srw:ditten og srw:datten
	
	$antalltreff['telemarksbilder'] = (int) $srw->numberOfRecords;
	@$materialtreff['bilde'] += $antalltreff['telemarksbilder']; 

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
		$telemarksbildertreff[$hitcounter]['digidato'] = ($record->getField("008"));
		$telemarksbildertreff[$hitcounter]['digidato'] = trim(substr($telemarksbildertreff[$hitcounter]['digidato'] , 5));
	}

	// ID
	if ($record->getField("001")) {
		$telemarksbildertreff[$hitcounter]['id'] = ($record->getField("001"));
		$telemarksbildertreff[$hitcounter]['id'] = trim(substr($telemarksbildertreff[$hitcounter]['id'] , 5));
	}

	// Diverse
	$telemarksbildertreff[$hitcounter]['slug'] = 'telemarksbilder';
	$telemarksbildertreff[$hitcounter]['kilde'] = "Telemarksbilder";
	$telemarksbildertreff[$hitcounter]['materialtype'] = "bilde";

	// URL og bilde er det samme
	if ($record->getField("856")) {
		if ($record->getField("856")->getSubfield("u")) {
			$telemarksbildertreff[$hitcounter]['bilde'] = $record->getField("856")->getSubfield("u");
			$telemarksbildertreff[$hitcounter]['bilde'] = $domain . substr($telemarksbildertreff[$hitcounter]['bilde'], 5); // fjerne feltkoden i 	starten
		}
	}

	@$telemarksbildertreff[$hitcounter]['url'] = $domain . "/cgi-bin/websok-bilder?tnr=" . trim($telemarksbildertreff[$hitcounter]['id']);


	// Tittel, ev. med årstall i 260
	if ($record->getField("245")) {
		if ($record->getField("245")->getSubfield("a")) {
			$telemarksbildertreff[$hitcounter]['tittel'] = $record->getField("245")->getSubfield("a");
			$telemarksbildertreff[$hitcounter]['tittel'] = substr($telemarksbildertreff[$hitcounter]['tittel'], 5); // fjerne feltkoden i starten
		}
	}

	if ($record->getField("260")) { // opphavsdato: sist i tittelen OG til eget felt
		if ($record->getField("260")->getSubfield("c")) {
			$telemarksbildertreff[$hitcounter]['tittel'] .= " (" . substr($record->getField("260")->getSubfield("c") , 5) . ")";
			$telemarksbildertreff[$hitcounter]['dato'] = trim(substr($record->getField("260")->getSubfield("c") , 5));
			$telemarksbildertreff[$hitcounter]['dato'] = trim(str_replace ("ca." , "" , $telemarksbildertreff[$hitcounter]['dato']));
		}
	}

	// Ansvar
	if ($record->getField("100")) {
		if ($record->getField("100")->getSubfield("a")) {
			$telemarksbildertreff[$hitcounter]['ansvar'] = $record->getField("100")->getSubfield("a");
			$telemarksbildertreff[$hitcounter]['ansvar'] = substr($telemarksbildertreff[$hitcounter]['ansvar'], 5); // fjerne feltkoden i starten
		}
		if ($record->getField("100")->getSubfield("d")) {
			$telemarksbildertreff[$hitcounter]['ansvar'] .= " (" . substr($record->getField("100")->getSubfield("d") , 5) . ")";
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
		$telemarksbildertreff[$hitcounter]['beskrivelse'] = "<b>Format: </b>" . implode (" / " , $beskrivelse) . ". ";
	}
	
	// Mer beskrivelse: Generell note (500)
	if ($record->getField("500")) {
		if ($record->getField("500")->getSubfield("a")) {
			$telemarksbildertreff[$hitcounter]['beskrivelse'] .= substr ($record->getField("500")->getSubfield("a") , 5) . ". ";
		}
	}

	// Mer beskrivelse: Generell note (505)
	if ($record->getField("505")) {
		if ($record->getField("505")->getSubfield("a")) {
			$telemarksbildertreff[$hitcounter]['beskrivelse'] .= "<b>Motiv: </b>" . substr ($record->getField("505")->getSubfield("a") , 5) . ". ";
		}
	}

	// Mer beskrivelse: Plassering (590)
	if ($record->getField("590")) {
		if ($record->getField("590")->getSubfield("a")) {
			$telemarksbildertreff[$hitcounter]['beskrivelse'] .= "<b>Plassering: </b>" . substr ($record->getField("590")->getSubfield("a") , 5);
		}
		if ($record->getField("590")->getSubfield("b")) {
			$telemarksbildertreff[$hitcounter]['beskrivelse'] .= " - " . substr ($record->getField("590")->getSubfield("b") , 5);
		}
		$telemarksbildertreff[$hitcounter]['beskrivelse'] .= ". ";
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
		$telemarksbildertreff[$hitcounter]['beskrivelse'] .= "<b>Emneord: </b>" . implode (" ; " , $emne) . ". ";
	}

	if (@is_array($person)) {
		$telemarksbildertreff[$hitcounter]['beskrivelse'] .= "<b>Personer: </b>" . implode (" ; " , $person) . ". ";
	}

	$emne = '';
	$person = '';
	
	$hitcounter++;	
} // SLUTT PÅ HVERT ENKELT TREFF

$treff = array_merge_recursive ((array) $telemarksbildertreff , (array) $treff);
domp ($telemarksbildertreff);
// SLUTT
