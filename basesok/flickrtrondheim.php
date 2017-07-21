<?php

/* Søker i Trondheim byarkiv sine Flickr-bilder og legger til:
 - treff i $treffliste
 - antall treff i $antalltreff['slug']
 - antall nye i denne materialtypen i $materialtreff['materialtype']
*/

if ($wl_flickr_api_key == '') { // skal være lastet i search.php
	die ("Du kan ikke s&oslash;ke i Flickr-baser uten &aring; angi en API-n&oslash;kkel i innstillingene!");
}

$rawurl = "https://api.flickr.com/services/rest/?method=flickr.photos.search&user_id=trondheim_byarkiv&api_key=" . $wl_flickr_api_key . "&text=<!QUERY!>&per_page=" . $makstreff . "&format=json";
$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm

$antalltreff['flickrtrondheim'] = ''; // nullstiller i tilfelle søket feiler
$flickrtrondheimtreff = '';

// LASTE TREFFLISTE SOM JSON
$jsonfile = get_content($rawurl);

// Klargjøre for konvertering fra JSON
$json = substr($jsonfile, strlen("jsonFlickrApi("), strlen($jsonfile) - strlen("jsonFlickrApi(") - 1);
$photos = json_decode ($json, TRUE);

// FINNE ANTALL TREFF
$antalltreff['flickrtrondheim'] = $photos['photos']['total'];
@$materialtreff['bilde'] += $antalltreff['flickrtrondheim']; 


// ... SÅ HVERT ENKELT TREFF
$teller = 0;

// kilde, slug, id, URL, bilde, tittel, ansvar, beskrivelse, digidato, dato, materialtype

foreach ($photos['photos']['photo'] as $entry) {

	$flickrtrondheimtreff[$teller]['kilde'] = "Flickr: Trondheim byarkiv";	
	$flickrtrondheimtreff[$teller]['slug'] = "flickrtrondheim";
	$flickrtrondheimtreff[$teller]['materialtype'] = 'bilde';
	$flickrtrondheimtreff[$teller]['ansvar'] = 'Trondheim byarkiv';
	$flickrtrondheimtreff[$teller]['id'] = $entry['id'];

	// Uvidet info om bilde
	$bildeinfourl = "https://api.flickr.com/services/rest/?method=flickr.photos.getinfo&api_key=" . $wl_flickr_api_key . "&photo_id=" . $flickrtrondheimtreff[$teller]['id'] . "&format=json";
	$bildeinfofil = get_content($bildeinfourl);
	$json = substr($bildeinfofil, strlen("jsonFlickrApi("), strlen($bildeinfofil) - strlen("jsonFlickrApi(") - 1);
	$dettebilde = json_decode ($json, TRUE);	
	$flickrtrondheimtreff[$teller]['url'] = $dettebilde['photo']['urls']['url'][0]['_content'];
	$flickrtrondheimtreff[$teller]['tittel'] = $dettebilde['photo']['title']['_content'];
	$flickrtrondheimtreff[$teller]['bilde'] = "http://farm" . $dettebilde['photo']['farm'] . ".static.flickr.com/" . $dettebilde['photo']['server'] . '/' . $dettebilde['photo']['id'] . '_' . $dettebilde['photo']['secret'] . '_z.jpg';
	$flickrtrondheimtreff[$teller]['digidato'] = date ("Ymd", $dettebilde['photo']['dateuploaded']);

	$dato = stristr($dettebilde['photo']['description']['_content'] , "Date:");
	$dato = stristr($dato , "Fotograf" , TRUE);
	$dato = strip_tags($dato);
	$dato = preg_replace("/[^0-9-]/","",$dato); // Fjerner alt unntatt tall og -
	$dato = trim($dato);
	$flickrtrondheimtreff[$teller]['dato'] = $dato;
	$dato = '';

	// BESKRIVELSE
	$flickrtrondheimtreff[$teller]['beskrivelse'] = strip_tags($dettebilde['photo']['description']['_content']) . ". ";
	if (isset($dettebilde['photo']['tags']['tag'])) {
		$tagger = '';
		foreach ($dettebilde['photo']['tags']['tag'] as $tagg) {
			$tagger[] = $tagg['_content'];
		}	
		$flickrtrondheimtreff[$teller]['beskrivelse'] .= "<b>Emneord: </b>" . implode (", " , $tagger) . ". ";
	}		


	$teller++;
} // SLUTT PÅ HVERT ENKELT TREFF

$treff = array_merge_recursive ((array) $flickrtrondheimtreff , (array) $treff);

// SLUTT
