<?php

/* Søker i Kirjastokaista og legger til:
 - treff i $treffliste
 - antall treff i $antalltreff['slug']
 - antall nye i denne materialtypen i $materialtreff['materialtype']
*/

$rawurl = "http://www.kirjastokaista.fi/api/get_search_results/?search=<!QUERY!>&count=" . $makstreff;

$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm

$antalltreff['kirjastokaista'] = ''; // nullstiller i tilfelle søket feiler
$kirjastokaistatreff = '';

// LASTE TREFFLISTE SOM JSON
$jsonfile = get_content($rawurl);

$kirjastodata = json_decode ($jsonfile, TRUE);

// FINNE ANTALL TREFF

$antalltreff['kirjastokaista'] = $kirjastodata['count_total'];

// ... SÅ HVERT ENKELT TREFF
$teller = 0;

// kilde, slug, id, URL, bilde, tittel, ansvar, beskrivelse, digidato, dato, materialtype

foreach ($kirjastodata['posts'] as $entry) {

	$kirjastokaistatreff[$teller]['kilde'] = "Kirjastokaista";	
	$kirjastokaistatreff[$teller]['slug'] = "kirjastokaista";
	$kirjastokaistatreff[$teller]['tittel'] = $entry['title'];
	$kirjastokaistatreff[$teller]['url'] = $entry['url'];

	if (isset($entry['custom_fields']['duration'][0])) {
		$kirjastokaistatreff[$teller]['beskrivelse'] = "<b>Lengde: </b>" . gmdate("H:i:s", $entry['custom_fields']['duration'][0]) . ". ";
	} else {
		$kirjastokaistatreff[$teller]['beskrivelse'] = '';
	}

	$kirjastokaistatreff[$teller]['beskrivelse'] .= strip_tags($entry['excerpt']);
	if (isset($entry['attachments'][0]['images']['progression-slider']['url'])) {
		$kirjastokaistatreff[$teller]['bilde'] = $entry['attachments'][0]['images']['progression-slider']['url'];
	}

	if (trim($entry['author']['first_name']) != "") {
		$kirjastokaistatreff[$teller]['ansvar']	= $entry['author']['first_name'] . " " . $entry['author']['last_name'];
	} else {
		$kirjastokaistatreff[$teller]['ansvar'] = $entry['author']['nickname'];
	}
	$kirjastokaistatreff[$teller]['dato'] = substr ($entry['date'] , 0 , 10);
	$kirjastokaistatreff[$teller]['dato'] = str_replace ("-" , "" , $kirjastokaistatreff[$teller]['dato']);

	$teller++;
} // SLUTT PÅ HVERT ENKELT TREFF

$treff = array_merge_recursive ((array) $kirjastokaistatreff , (array) $treff);

// SLUTT
