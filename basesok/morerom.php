<?php

// Søker i MøreRom lokalhistoriske artikler og legger til treff i $treffliste

$domain = "http://websok.mr.fylkesbibl.no";
$rawurl = "https://alebib.bib.no/cgi-bin/sru-morerom?version=1.2&operation=searchRetrieve&maximumRecords=" . $makstreff . "&query=<!QUERY!>";
$antalltreff['morerom'] = ''; // nullstiller i tilfelle søket feiler
$moreromtreff = ''; // nullstiller treff
$srw = '';

$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm

$sru_datafil = get_content($rawurl);

// Herfra kan det feile spektakulært med XML

if(substr($sru_datafil, 0, 5) == "<?xml") { // vi fikk en XML-fil tilbake

	$sru_data = simplexml_load_string($sru_datafil); 
	$namespaces = $sru_data->getNameSpaces(true);
	$srw = $sru_data->children($namespaces['SRU']); // alle som er srw:ditten og srw:datten
	
	$antalltreff['morerom'] = (int) $srw->numberOfRecords;
	@$materialtreff['artikkel'] += $antalltreff['morerom']; 

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
		$moreromtreff[$hitcounter]['digidato'] = ($record->getField("008"));
		$moreromtreff[$hitcounter]['digidato'] = trim(substr($moreromtreff[$hitcounter]['digidato'] , 5, 9));
	}

	// ID
	if ($record->getField("001")) {
		$moreromtreff[$hitcounter]['id'] = ($record->getField("001"));
		$moreromtreff[$hitcounter]['id'] = trim(substr($moreromtreff[$hitcounter]['id'] , 5));
	}

	// Diverse
	$moreromtreff[$hitcounter]['slug'] = 'morerom';
	$moreromtreff[$hitcounter]['kilde'] = "MøreRom";
	$moreromtreff[$hitcounter]['materialtype'] = "artikkel";
	$moreromtreff[$hitcounter]['bilde'] = $artikkelbilde;


	@$moreromtreff[$hitcounter]['url'] = $domain . "/cgi-bin/websok-morerom?tnr=" . trim($moreromtreff[$hitcounter]['id']);


	// Tittel, ev. med årstall i 260
	if ($record->getField("245")) {
		if ($record->getField("245")->getSubfield("a")) {
			$moreromtreff[$hitcounter]['tittel'] = $record->getField("245")->getSubfield("a");
			$moreromtreff[$hitcounter]['tittel'] = substr($moreromtreff[$hitcounter]['tittel'], 5); // fjerne feltkoden i starten
		}
	}

	if ($record->getField("260")) { // opphavsdato: sist i tittelen OG til eget felt
		if ($record->getField("260")->getSubfield("c")) {
			$moreromtreff[$hitcounter]['tittel'] .= " (" . substr($record->getField("260")->getSubfield("c") , 5) . ")";
			$moreromtreff[$hitcounter]['dato'] = trim(substr($record->getField("260")->getSubfield("c") , 5));
			$moreromtreff[$hitcounter]['dato'] = trim(str_replace ("ca." , "" , $moreromtreff[$hitcounter]['dato']));
			$moreromtreff[$hitcounter]['dato'] = trim(str_replace (">" , "" , $moreromtreff[$hitcounter]['dato']));
			$moreromtreff[$hitcounter]['dato'] = trim(str_replace ("<" , "" , $moreromtreff[$hitcounter]['dato']));
		}
	}

	// Ansvar
	if ($record->getField("100")) {
		if ($record->getField("100")->getSubfield("a")) {
			$moreromtreff[$hitcounter]['ansvar'] = $record->getField("100")->getSubfield("a");
			$moreromtreff[$hitcounter]['ansvar'] = substr($moreromtreff[$hitcounter]['ansvar'], 5); // fjerne feltkoden i starten
		}
		if ($record->getField("100")->getSubfield("d")) {
			$moreromtreff[$hitcounter]['ansvar'] .= " (" . substr($record->getField("100")->getSubfield("d") , 5) . ")";
		}
	}

	// Beskrivelse
	if ($record->getField("773")) { // Vertsdokument
	$moreromtreff[$hitcounter]['beskrivelse'] = "<strong>I: </strong>" . substr($record->getField("773")->getSubfield("t") , 5) . " (" . substr($record->getField("773")->getSubfield("i") , 5) . "). ";
	}



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
		$moreromtreff[$hitcounter]['beskrivelse'] .= "<b>Omfang: </b>" . implode (" / " , $beskrivelse) . ". ";
	}
	
	// Mer beskrivelse: Generell note (500)
	if ($record->getField("500")) {
		if ($record->getField("500")->getSubfield("a")) {
			$moreromtreff[$hitcounter]['beskrivelse'] .= substr ($record->getField("500")->getSubfield("a") , 5) . ". ";
		}
	}

	// Mer beskrivelse: Generell note (505)
	if ($record->getField("505")) {
		if ($record->getField("505")->getSubfield("a")) {
			$moreromtreff[$hitcounter]['beskrivelse'] .= "<b>Motiv: </b>" . substr ($record->getField("505")->getSubfield("a") , 5) . ". ";
		}
	}

	// Mer beskrivelse: Plassering (590)
	if ($record->getField("590")) {
		if ($record->getField("590")->getSubfield("a")) {
			$moreromtreff[$hitcounter]['beskrivelse'] .= "<b>Plassering: </b>" . substr ($record->getField("590")->getSubfield("a") , 5);
		}
		if ($record->getField("590")->getSubfield("b")) {
			$moreromtreff[$hitcounter]['beskrivelse'] .= " - " . substr ($record->getField("590")->getSubfield("b") , 5);
		}
		$moreromtreff[$hitcounter]['beskrivelse'] .= ". ";
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

		if ($tag == '856') {
			foreach ($subfields->getSubfields() as $code => $value) {
				$ettfelt[(string) $code] = substr((string) $value, 5);
			}
			$merinnhold[] = "<button style=\"float: right; display: inline; margin: 15px 0;\" onclick=\"location.href='" . $ettfelt['u'] . "'\">" . $ettfelt['z'] . '</button>' . "\n";
		}

	}

	if (is_array($emne)) {	
		$moreromtreff[$hitcounter]['beskrivelse'] .= "<b>Emneord: </b>" . implode (" ; " , $emne) . ". ";
	}

	if (@is_array($person)) {
		$moreromtreff[$hitcounter]['beskrivelse'] .= "<b>Personer: </b>" . implode (" ; " , $person) . ". ";
	}

	if (@is_array($merinnhold)) {
		$moreromtreff[$hitcounter]['beskrivelse'] .= "<br>" . implode ("&nbsp;&nbsp;" , $merinnhold);
	}

	unset ($emne, $person, $merinnhold);
	$moreromtreff[$hitcounter]['beskrivelse'] .= "</b>"; 	
	
	$hitcounter++;	
} // SLUTT PÅ HVERT ENKELT TREFF

$treff = array_merge_recursive ((array) $moreromtreff , (array) $treff);

// SLUTT
