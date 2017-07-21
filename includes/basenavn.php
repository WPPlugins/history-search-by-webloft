<?php

// Inneholder basenavn og funksjoner for å konvertere slug<=>navn
// slug |x| navn |x| URL til mer info |x| URL til websøk med <!QUERY!>

$basenavn = array();

// BILDEBASER BIBLIOFIL

$basenavn[] = "baerumkunst|x|Bærum biblioteks kunstbase|x|http://bibliotek.baerum.kommune.no/Nyheter/Kunstsamling|x|http://www.barum.folkebibl.no/cgi-bin/websok-kunst?mode=vt&st=p&publikumskjema=1&hpid=4311&embedded=0&pubsok_txt_0=<!QUERY!>";

$basenavn[] = "baerumbilder|x|Bærum biblioteks bildebase|x|http://bibliotek.baerum.kommune.no/lokalhistorie/Bilder-fra-Barum/Lokalhistoriske-bilder|x|http://www.barum.folkebibl.no/cgi-bin/websok-lokalsamling?mode=vt&st=p&publikumskjema=1&hpid=5496&embedded=0&pubsok_txt_0=<!QUERY!>";

$basenavn[] = "fusabilder|x|Fusa biblioteks bildesamling|x|https://www.fusa.kommune.no/snarvegar/bibliotek/|x|http://www.fusa.folkebibl.no/cgi-bin/websok-bilde?mode=vt&st=p&publikumskjema=1&hpid=8529&pubsok_txt_7=<!QUERY!>";

$basenavn[] = "levangerbilder|x|Levanger biblioteks bildesamling|x|http://www.levanger.kommune.no/Bibliotek/Tjenester/Lokalhistorisk-samling/|x|http://www.levangsbilder.no/cgi-bin/websok-bilde?mode=vt&st=p&publikumskjema=1&hpid=8529&pubsok_txt_7=<!QUERY!>";

$basenavn[] = "askerbilder|x|Asker biblioteks bildebase|x|http://www.askerbibliotek.no/askersamling/bilder|x|http://askbib.asker.folkebibl.no/cgi-bin/websok-bilde?mode=vt&st=p&publikumskjema=1&hpid=1061&embedded=0&pubsok_txt_0=<!QUERY!>";

$basenavn[] = "oppegaardlokal|x|Oppegård biblioteks lokalhistoriske samling|x|http://www.oppegard.folkebibl.no/lokalhistorie|x|http://www.oppegard.bib.no/cgi-bin/websok-bilde?mode=vt&st=p&publikumskjema=1&hpid=5496&embedded=0&pubsok_txt_0=<!QUERY!>";

$basenavn[] = "sandefjordlokal|x|Sandefjord biblioteks lokalhistoriske samling|x|https://www.sandefjord.folkebibl.no/samlinger/lokalhistorie.html|x|https://www.sandefjord.folkebibl.no/cgi-bin/websok-bilde?mode=vt&st=p&publikumskjema=1&hpid=5496&embedded=0&pubsok_txt_0=<!QUERY!>"; 

$basenavn[] = "askoybilder|x|Askøy biblioteks bildebase|x|http://www.bibvest.no/bibliotek/askoy-folkebibliotek|x|http://www.askoy.folkebibl.no/cgi-bin/websok-bilde?mode=vt&st=p&embedded=0&pubsok_txt_0=<!QUERY!>"; 

$basenavn[] = "ballangenbilder|x|Ballangen biblioteks bildebase|x|http://www.nordbib.no/index.php?option=com_content&view=category&layout=blog&id=57&Itemid=161|x|http://www.ballangen.folkebibl.no/cgi-bin/websok-bilde?mode=vt&st=p&embedded=0&pubsok_txt_0=<!QUERY!>"; 

$basenavn[] = "bomlobilder|x|Bømlo biblioteks bildebase|x|http://www.bomlobibliotek.no|x|http://www.bomlo.folkebibl.no/cgi-bin/websok-bilde?mode=vt&st=p&embedded=0&pubsok_txt_0=<!QUERY!>"; 

$basenavn[] = "donnabilder|x|Dønna biblioteks bildebase|x|http://www.nordbib.no/index.php?option=com_content&view=category&layout=blog&id=7&Itemid=182|x|http://www.donna.folkebibl.no/cgi-bin/websok-bilde?mode=vt&st=p&embedded=0&pubsok_txt_0=<!QUERY!>"; 

$basenavn[] = "hammerfestbilder|x|Hammerfest biblioteks bildebase|x|http://www.hammerfest.folkebibl.no/|x|http://www.hammerfest.folkebibl.no/cgi-bin/websok-bilde?mode=vt&st=p&embedded=0&pubsok_txt_0=<!QUERY!>"; 

$basenavn[] = "vadsobilder|x|Vadsø biblioteks bildebase|x|http://www.vadso.folkebibl.no|x|http://www.vadso.folkebibl.no/cgi-bin/websok-bilde?mode=vt&st=p&embedded=0&pubsok_txt_0=<!QUERY!>"; 

$basenavn[] = "kongsvingerbilder|x|Kongsvinger biblioteks bildebase|x|http://www.kongsvinger.kommune.no/no/Artikler/Bibliotek/|x|http://www.kongsvinger.folkebibl.no/cgi-bin/websok-bilde?mode=vt&st=p&embedded=0&pubsok_txt_0=<!QUERY!>"; 

$basenavn[] = "kvinnheradbilder|x|Kvinnherad biblioteks bildebase|x|http://www.sunnbib.no/bibliotek/kvinnherad-bibliotek|x|http://www.kvinnherad.folkebibl.no/cgi-bin/websok-bilde?mode=vt&st=p&embedded=0&pubsok_txt_0=<!QUERY!>"; 

$basenavn[] = "luroybilder|x|Lurøy biblioteks bildebase|x|http://www.luroy.folkebibl.no/Fotoprosjekt.htm|x|http://www.luroy.folkebibl.no/cgi-bin/websok-bilde?mode=vt&st=p&embedded=0&pubsok_txt_0=<!QUERY!>"; 

$basenavn[] = "notoddenbilder|x|Notodden biblioteks bildebase|x|http://www.notodden.kommune.no/Organisasjon/Kultur/Notodden-bibliotek1/Lokalhistorisk-samling|x|http://websok.notodden.folkebibl.no/cgi-bin/websok-bilde?mode=vt&st=p&embedded=0&pubsok_txt_0=<!QUERY!>"; 

$basenavn[] = "porsgrunnbilder|x|Porsgrunn biblioteks bildebase|x|http://www.porsgrunn.folkebibl.no|x|http://www.porsgrunn.folkebibl.no/cgi-bin/websok-bilde?mode=vt&st=p&embedded=0&pubsok_txt_0=<!QUERY!>"; 

//$basenavn[] = "raumabilder|x|Rauma biblioteks bildebase|x|http://www.rauma.folkebibl.no|x|http://www.rauma.folkebibl.no/cgi-bin/websok-bilde?mode=vt&st=p&embedded=0&pubsok_txt_0=<!QUERY!>"; 
// Denne lenken er oppført hos Bibliofil, men gir 404: http://www.rauma.folkebibl.no/cgi-bin/sru-bilde

$basenavn[] = "ringsakerbilder|x|Ringsaker biblioteks bildebase|x|http://www.ringsaker.kommune.no/bildebasen.199139.no.html|x|http://www.ringsaker.bib.no/cgi-bin/websok-bilde?mode=vt&st=p&embedded=0&pubsok_txt_0=<!QUERY!>"; 

$basenavn[] = "steinkjerbilder|x|Steinkjer biblioteks bildebase|x|http://steinkjer.folkebibl.no/bildesoek.85089.no.html|x|http://websok.steinkjer.folkebibl.no/cgi-bin/websok-bilde?mode=vt&st=p&embedded=0&pubsok_txt_0=<!QUERY!>"; 

//$basenavn[] = "telemarksbilder|x|Telemarksbilder|x|http://www.tm.fylkesbibl.no|x|http://telebib.tm.fylkesbibl.no/cgi-bin/websok-bilde?mode=vt&st=p&embedded=0&pubsok_txt_0=<!QUERY!>"; 
//Det er noe tull med denne, får opp bøker i stedet for bilder

$basenavn[] = "amotbilder|x|Åmot biblioteks bildebase|x|http://www.amot.kommune.no/tema/kultur-og-fritid/bibliotek/Sider/side.aspx|x|http://www.amot.folkebibl.no/cgi-bin/websok-bilde?mode=vt&st=p&embedded=0&pubsok_txt_0=<!QUERY!>"; 


// NASJONALBIBLIOTEKET

$basenavn[] = "bokhylla|x|Bokhylla - alt|x|http://www.nb.no/Tilbud/Samlingen/Samlingen/Boeker/Bokhylla.no|x|http://www.nb.no/nbsok/search?action=search&mediatype=bøker&format=Digitalt tilgjengelig&CustomDateFrom=&CustomDateTo=&pageSize=50&sortBy=ranking&searchString=<!QUERY!>%26ft=false";

$basenavn[] = "bokhyllalokalhistorie|x|Bokhylla - emne lokalhistorie|x|http://www.nb.no/Tilbud/Samlingen/Samlingen/Boeker/Bokhylla.no|x|http://www.nb.no/nbsok/search?action=search&mediatype=bøker&format=Digitalt tilgjengelig&CustomDateFrom=&CustomDateTo=&pageSize=50&sortBy=ranking&searchString=subjecttopic:lokalhistorie AND <!QUERY!> %26ft=false";

$basenavn[] = "nbbilder|x|Bilder fra Nasjonalbiblioteket|x|http://www.nb.no/Tilbud/Samlingen/Samlingen/Bilder|x|http://www.nb.no/nbsok/search?action=search&mediatype=bilder&format=Digitalt tilgjengelig&CustomDateFrom=&CustomDateTo=&pageSize=50&sortBy=ranking&searchString=<!QUERY!>";

$basenavn[] = "nbradio|x|Radio fra NRK/Nasjonalbiblioteket|x|http://www.nb.no/Tilbud/Samlingen/Samlingen/Radio-og-TV|x|http://www.nb.no/nbsok/search?action=search&mediatype=radio&format=Digitalt tilgjengelig&CustomDateFrom=&CustomDateTo=&pageSize=50&sortBy=ranking&searchString=<!QUERY!>";

$basenavn[] = "nbprivatarkiv|x|Privatarkivmateriale fra Nasjonalbiblioteket|x|http://www.nb.no/Tilbud/Samlingen/Samlingen/Privatarkiv|x|http://www.nb.no/nbsok/search?action=search&mediatype=privatarkivmateriale&format=Digitalt tilgjengelig&CustomDateFrom=&CustomDateTo=&pageSize=50&sortBy=ranking&searchString=<!QUERY!>";


// FLICKR


$basenavn[] = "flickraurland|x|Lokalhistorisk senter i Aurland (Flickr)|x|https://www.flickr.com/people/aurland/|x|https://www.flickr.com/search/?w=7425351@N02&q=<!QUERY!>";

$basenavn[] = "flickrtrondheim|x|Trondheim byarkiv (Flickr)|x|https://www.flickr.com/people/trondheim_byarkiv/|x|https://www.flickr.com/search/?w=29160242@N08&q=<!QUERY!>";
 
$basenavn[] = "flickrnb|x|Nasjonalbibliotekets (Flickr Commons)|x|https://www.flickr.com/people/national_library_of_norway/|x|https://www.flickr.com/search/?w=48220291@N04&q=<!QUERY!>";

// YOUTUBE

$basenavn[] = "youtubeoslobyarkiv|x|Oslo Byarkivs Youtube-kanal|x|https://www.youtube.com/user/Oslobyarkiv|x|https://www.youtube.com/user/Oslobyarkiv/search?query=<!QUERY!>";


// ANNET


$basenavn[] = "nuvbilder|x|Nore og Uvdal biblioteks bildebase|x|http://noreuvdal.dbib.no/|x|http://asp.bibliotekservice.no/nuf_foto/doclist.aspx?fquery=fr%3d<!QUERY!>";

$basenavn[] = "lokalhistoriewiki|x|Lokalhistoriewiki|x|https://lokalhistoriewiki.no/index.php/Hjelp:Om_wikien|x|http://lokalhistoriewiki.no/index.php?search=<!QUERY!>&title=Spesial%3AS%C3%B8k&go=G%C3%A5";

$basenavn[] = "norvegianadifo|x|Digitalt fortalt|x|http://digitaltfortalt.no/info/about|x|http://digitaltfortalt.no/search?state_id=&query=<!QUERY!>&js=1";

$basenavn[] = "norvegianadimu|x|Digitalt Museum|x|https://digitaltmuseum.no/info/digitaltmuseum|x|https://digitaltmuseum.no/search?query=<!QUERY!>";

$basenavn[] = "omekadeichman|x|Lokalhistoriske bildebaser i Oslo (Deichman)|x|http://bildebaser.deichman.no/collections/browse|x|http://bildebaser.deichman.no/search?query=<!QUERY!>&query_type=exact_match";

$basenavn[] = "kulturminnebilder|x|Kulturminnebilder fra Riksantikvaren|x|http://www.riksantikvaren.no/Om-oss/Arkiver/Kulturminnebilder|x|http://kulturminnebilder.ra.no/fotoweb/default.fwx?search=<!QUERY!>";

$basenavn[] = "unimusark|x|Universitetsmuseene - arkeologi|x|http://www.unimus.no/arkeologi/|x|http://www.unimus.no/arkeologi/#/listView?search=<!QUERY!>&sortBy=artifact";

$basenavn[] = "unimusmynt|x|Universitetsmuseene - mynt og medaljer|x|http://www.unimus.no/numismatikk|x|http://www.unimus.no/numismatikk/#/L=no_BO/P=search/S=<!QUERY!>/I=0";

$basenavn[] = "unimusfoto|x|Universitetsmuseene - foto|x|http://www.unimus.no/foto|x|http://www.unimus.no/foto/#/search?q=<!QUERY!>";

$basenavn[] = "norvegianaindmu|x|Industrimuseum.no|x|http://www.industrimuseum.no/seksjoner/omindustrimuseum|x|http://www.industrimuseum.no/site_search?query%3Austring%3Autf8=<!QUERY!>";

$basenavn[] = "virksommeord|x|Virksomme Ord|x|http://virksommeord.uib.no/|x|http://virksommeord.uib.no/sok?search_term=<!QUERY!>&search_type=alle";

$basenavn[] = "kirjastokaista|x|Kirjastokaista|x|http://www.kirjastokaista.fi/en/|x|http://www.kirjastokaista.fi/en/?s=<!QUERY!>&submit=Search&lang=en";

$basenavn[] = "morerom|x|MøreRom|x|http://mrfylke.no/Tenesteomraade/Kultur/Fylkesbiblioteket/Framsida|x|http://websok.mr.fylkesbibl.no/cgi-bin/websok-morerom?mode=vt&st=p&embedded=0&pubsok_txt_0=<!QUERY!>";


// SÅ NOEN FUNKSJONER


if (!function_exists('wlbasesort')) {
	function wlbasesort($a, $b) {
		return @strcmp(stristr($a, "|x|") , stristr($b , "|x|")); // sorterer fra første |x| = baseNAVN
	}
}

usort ($basenavn , 'wlbasesort');

$GLOBALS['basenavn'] = $basenavn;

function basetilslug ($base) {
	foreach ($GLOBALS['basenavn'] as $enbase) {
		$splitt = explode ("|x|" , $enbase);
		if ($base == $splitt[1]) {
			return ($splitt[0]);
		}
	}
	return FALSE;
}

function slugtilbase ($slug) {

	foreach ($GLOBALS['basenavn'] as $enbase) {
		$splitt = explode ("|x|" , $enbase);
		if ($slug == $splitt[0]) {
			return ($splitt[1]);
		}
	}
	return FALSE;
}
