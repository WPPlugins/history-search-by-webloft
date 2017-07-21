<?php

/* Søker i Oslo byarkivs Youtube-konto og legger til:
 - treff i $treffliste
 - antall treff i $antalltreff['slug']
 - antall nye i denne materialtypen i $materialtreff['materialtype']
*/

// LAG URL: https://developers.google.com/youtube/v3/docs/search/list

// Finn channelID: View source på Youtube-siden til kanalen og søk etter data-channel-external-id

// Oslo Byarkiv sin Channel ID:
$youtubechannelid = "UCuZ6miGdG1uEYfQmcDjIbvA";

if ($wl_youtube_api_key == '') { // skal være lastet i search.php
	die ("Du kan ikke s&oslash;ke i Youtube-baser uten &aring; angi en API-n&oslash;kkel i innstillingene!");
}

//SØKE-URL:
// https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=UCuZ6miGdG1uEYfQmcDjIbvA&key=AIzaSyCtvFixJoNOHzJ4Y0q5zbeteKCUSWxA3ek


$rawurl = "https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=" . $youtubechannelid . "&key=" . $wl_youtube_api_key . "&q=<!QUERY!>&maxResults=" . $makstreff . "&type=video";

$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn søketerm

$antalltreff['youtubeoslobyarkiv'] = ''; // nullstiller i tilfelle søket feiler
$youtubeoslobyarkiv = '';

// LASTE TREFFLISTE SOM JSON
$jsonfile = get_content($rawurl);

// Klargjøre for konvertering fra JSON
$videos = json_decode ($jsonfile);

// FINNE ANTALL TREFF
$antalltreff['youtubeoslobyarkiv'] = $videos->pageInfo->totalResults;
@$materialtreff['video'] += $antalltreff['youtubeoslobyarkiv']; 

// ... SÅ HVERT ENKELT TREFF
$teller = 0;

// kilde, slug, id, URL, bilde, tittel, ansvar, beskrivelse, digidato, dato, materialtype

foreach ($videos->items as $entry) {

	$youtubeoslobyarkivtreff[$teller]['kilde'] = "Oslo Byarkiv på Youtube";	
	$youtubeoslobyarkivtreff[$teller]['slug'] = "youtubeoslobyarkiv";
	$youtubeoslobyarkivtreff[$teller]['materialtype'] = 'video';
	$youtubeoslobyarkivtreff[$teller]['ansvar'] = 'Eier: Oslo byarkiv';
	$youtubeoslobyarkivtreff[$teller]['id'] = $entry->id->videoId;
	$youtubeoslobyarkivtreff[$teller]['bilde'] = $entry->snippet->thumbnails->high->url;
	$youtubeoslobyarkivtreff[$teller]['tittel'] = $entry->snippet->title;
	$youtubeoslobyarkivtreff[$teller]['beskrivelse'] = $entry->snippet->description;
	$youtubeoslobyarkivtreff[$teller]['url'] = 'https://www.youtube.com/watch?v=' . $youtubeoslobyarkivtreff[$teller]['id'];
	$youtubeoslobyarkivtreff[$teller]['digidato'] = date ("Ymd", strtotime($entry->snippet->publishedAt));

	$teller++;
} // SLUTT PÅ HVERT ENKELT TREFF

$treff = array_merge_recursive ((array) $youtubeoslobyarkivtreff , (array) $treff);

// SLUTT
