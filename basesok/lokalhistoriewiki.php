<?php

/* Søker i lokalhistoriewiki og legger til:
 - treff i $treffliste
 - antall treff i $antalltreff['slug']
 - antall nye i denne materialtypen i $materialtreff['materialtype']
*/

$rawurl = "http://lokalhistoriewiki.no/api.php?action=opensearch&search=<!QUERY!>&limit=" . $makstreff;
$rawurl = "http://lokalhistoriewiki.no/api.php?action=query&list=search&srwhat=title&srsearch=<!QUERY!>&format=json&srlimit=" . $makstreff;
$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm
$antalltreff['lokalhistoriewiki'] = ''; // nullstiller i tilfelle søket feiler
$lokalhistoriewikitreff = '';

// TREFFLISTE KOMMER SOM JSON

$jsonfile = get_content($rawurl);
$jsondata = json_decode($jsonfile);

$treffliste = $jsondata->query->search; // her ligger treffene

// ESTIMERE ANTALL TREFF
$antalltreff['lokalhistoriewiki'] = count ($treffliste);

// ... SÅ HVERT ENKELT TREFF
	$teller = 0;
	if ($antalltreff['lokalhistoriewiki'] > 0) { // dette gjør vi bare hvis vi har treff

		foreach ($treffliste as $enkelttreff) {

			$wordcount = (int) $enkelttreff->wordcount;		
			// HVIS SKITKORT TAR VI DEN IKKE MED - sikkert omdirigert eller i hvert fall uinteressant
			if ($wordcount > 10) {
				$lokalhistoriewikitreff[$teller]['tittel'] = strip_tags($enkelttreff->title);
			
				$treffurl = str_replace (" " , "_" , $lokalhistoriewikitreff[$teller]['tittel']);	
				$wordcount = (int) $enkelttreff->wordcount;		
				$lokalhistoriewikitreff[$teller]['url'] = "http://lokalhistoriewiki.no/index.php/" . $treffurl;
	
	
				// FINNE BILDE
				$bildeinfourl = "http://lokalhistoriewiki.no/api.php?action=query&prop=images|revisions&rvlimit=1&rvprop=content&format=json&titles=" . urlencode(trim(str_replace (" " , "_" , $lokalhistoriewikitreff[$teller]['tittel'])));
				$bildeinfourldata = get_content ($bildeinfourl);
				$bildeinfourldata = json_decode ($bildeinfourldata);
	
				foreach ($bildeinfourldata->query->pages as $hitme) {
					if (isset($hitme->images[0]->title)) {
						$bilde = str_replace("Fil:" , "" , $hitme->images[0]->title);
						$bilde = str_replace(" " , "_" , $bilde);
						$lokalhistoriewikitreff[$teller]['bilde'] =	"http://lokalhistoriewiki.no/images/" . $bilde;		
					} else { // bilde finnes ikke
						$lokalhistoriewikitreff[$teller]['bilde'] =	$lokalhistoriewikiomslag;
					}
					if ($bilde == "CC_some_rights_reserved.svg") { // da er det bedre å ha logo
						$lokalhistoriewikitreff[$teller]['bilde'] =	$lokalhistoriewikiomslag;
					}
				}
				unset ($bildeinfourl, $bildeinfourldata);

				// LAGE UTDRAG
				$teksturl = "http://lokalhistoriewiki.no/api.php?action=parse&format=json&page=" . urlencode(trim(str_replace (" " , "_" , $lokalhistoriewikitreff[$teller]['tittel'])));
				$teksturldata = get_content ($teksturl);
				$teksturldata = json_decode ($teksturldata);
				$tekst = $teksturldata->parse->text->{'*'};
				if (stristr ($tekst , '<strong class="selflink">')) {
					$tekster = explode ('<strong class="selflink">' , $tekst); // magisk
					$tekst = trunc(strip_tags(end($tekster)), 80);
				} else {
					$tekst = trunc(strip_tags($tekst), 80);
				}
				unset ($teksturl, $teksturldata);
			
				$lokalhistoriewikitreff[$teller]['kilde'] = "lokalhistoriewiki.no";
				$lokalhistoriewikitreff[$teller]['slug'] = "lokalhistoriewiki";
				$lokalhistoriewikitreff[$teller]['digidato'] = str_replace ("-" , "" , substr((string) $enkelttreff->timestamp , 0, 10));
				$lokalhistoriewikitreff[$teller]['dato'] = $lokalhistoriewikitreff[$teller]['digidato'];
				$lokalhistoriewikitreff[$teller]['materialtype'] = "artikkel";
				$lokalhistoriewikitreff[$teller]['beskrivelse'] = $tekst . " (" . $wordcount . " ord, sist endret " . substr((string) $enkelttreff->timestamp , 0, 10) . ")";
	
				unset ($treffurl , $wordcount , $bilde, $tekst);
				$teller++;
			} // Slutt på hvis omdirigert
		} // SLUTT PÅ HVERT ENKELT TREFF
	}

// Fjerne duplikater (som kan ha oppstått ved treff på omdirigerte artikler
$lokalhistoriewikitreff = @array_map("unserialize", array_unique(array_map("serialize", $lokalhistoriewikitreff)));

// FINNE EKSAKT ANTALL TREFF
$antalltreff['lokalhistoriewiki'] = count ($lokalhistoriewikitreff);

@$materialtreff['artikkel'] += $antalltreff['lokalhistoriewiki']; 

$treff = @array_merge_recursive ((array) $lokalhistoriewikitreff , (array) $treff);

// SLUTT
