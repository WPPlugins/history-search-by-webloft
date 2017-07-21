<?php

/* Søker i Lokalhistorisk senter i Aurland sine Flickr-bilder og legger til:
 - treff i $treffliste
 - antall treff i $antalltreff['slug']
 - antall nye i denne materialtypen i $materialtreff['materialtype']
*/

if ($wl_flickr_api_key == '') { // skal være lastet i search.php
	die ("Du kan ikke s&oslash;ke i Flickr-baser uten &aring; angi en API-n&oslash;kkel i innstillingene!");
}

$rawurl = "https://api.flickr.com/services/rest/?method=flickr.photos.search&user_id=aurland&api_key=" . $wl_flickr_api_key . "&text=<!QUERY!>&per_page=" . $makstreff . "&format=json";
$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm

$antalltreff['flickraurland'] = ''; // nullstiller i tilfelle søket feiler
$flickraurlandtreff = '';

// LASTE TREFFLISTE SOM JSON
$jsonfile = get_content($rawurl);

// Klargjøre for konvertering fra JSON
$json = substr($jsonfile, strlen("jsonFlickrApi("), strlen($jsonfile) - strlen("jsonFlickrApi(") - 1);
$photos = json_decode ($json, TRUE);

// FINNE ANTALL TREFF
$antalltreff['flickraurland'] = $photos['photos']['total'];
@$materialtreff['bilde'] += $antalltreff['flickraurland']; 


// ... SÅ HVERT ENKELT TREFF
$teller = 0;

// kilde, slug, id, URL, bilde, tittel, ansvar, beskrivelse, digidato, dato, materialtype

foreach ($photos['photos']['photo'] as $entry) {

	$flickraurlandtreff[$teller]['kilde'] = "Flickr: Lokalhistorisk senter i Aurland";	
	$flickraurlandtreff[$teller]['slug'] = "flickraurland";
	$flickraurlandtreff[$teller]['materialtype'] = 'bilde';
	$flickraurlandtreff[$teller]['ansvar'] = 'Lokalhistorisk senter i Aurland';
	$flickraurlandtreff[$teller]['id'] = $entry['id'];

	// Uvidet info om bilde
	$bildeinfourl = "https://api.flickr.com/services/rest/?method=flickr.photos.getinfo&api_key=" . $wl_flickr_api_key . "&photo_id=" . $flickraurlandtreff[$teller]['id'] . "&format=json";
	$bildeinfofil = get_content($bildeinfourl);
	$json = substr($bildeinfofil, strlen("jsonFlickrApi("), strlen($bildeinfofil) - strlen("jsonFlickrApi(") - 1);
	$dettebilde = json_decode ($json, TRUE);	
	$flickraurlandtreff[$teller]['url'] = $dettebilde['photo']['urls']['url'][0]['_content'];
	$flickraurlandtreff[$teller]['tittel'] = $dettebilde['photo']['title']['_content'];
	$flickraurlandtreff[$teller]['bilde'] = "http://farm" . $dettebilde['photo']['farm'] . ".static.flickr.com/" . $dettebilde['photo']['server'] . '/' . $dettebilde['photo']['id'] . '_' . $dettebilde['photo']['secret'] . '_z.jpg';
	$flickraurlandtreff[$teller]['digidato'] = date ("Ymd", $dettebilde['photo']['dateuploaded']);

	
	// BESKRIVELSE
	$flickraurlandtreff[$teller]['beskrivelse'] = $dettebilde['photo']['description']['_content'] . ". ";
	if (isset($dettebilde['photo']['tags']['tag'])) {
		$tagger = '';
		foreach ($dettebilde['photo']['tags']['tag'] as $tagg) {
			$tagger[] = $tagg['_content'];
		}	
		$flickraurlandtreff[$teller]['beskrivelse'] .= "<b>Emneord: </b>" . implode (", " , $tagger) . ". ";
	}		


	$teller++;
} // SLUTT PÅ HVERT ENKELT TREFF

$treff = array_merge_recursive ((array) $flickraurlandtreff , (array) $treff);

// SLUTT
