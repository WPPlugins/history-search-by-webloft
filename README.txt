=== WL Kulturs&oslash;k ===
Contributors: sundaune
Tags: Webekspertene, Aviser, Bilder, lokalhistorie, slektsgransking, slektsgranskning, PDF, Nasjonalbiblioteket, Bøker, bok, bygdehistorie, bygdebok, bygdebøker, historie, norvegiana, kulturnett, webløft, webloft, bibvenn, Bibliotekarens beste venn, kultur, kultursøk, norvegiana
Requires at least: 4.0
Tested up to: 4.3.1
Stable tag: 3.0.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Find historical and cultural material from many Norwegian sources. Norwegian: Finn lokal- og kulturhistorisk materiale (bøker, bilder, tekster etc.) fra mange kilder.

== Description ==

This plugin searches for culture related objects from many freely available Norwegian sources: Books, digital stories, photos, video, audio and more. Upon activation the plugin installs a shortcode available for pages and posts. Using this shortcode will insert a search form - the maximum number of hits to retreieve from each source is specified as arguments to the shortcode. When searching, each search will get its own unique URL for sharing with others. 

NORWEGIAN:

Denne utvidelsen søker i kulturrelatert materiale fra mange fritt tilgjengelige norske kilder: Bøker, digitale fortellinger, bilder, video lyd og annet. Når utvidelsen aktiveres installerer den en kortkode (shortcode) som du kan sette inn i sider og innlegg for å vise en søkeboks. I kortkoden kan det angis hvor mange treff som maksimalt skal hentes fra hver kilde. Hvert søk vil få sin egen unike URL slik at det kan deles med andre. 

== Installation ==

= Uploading the plugin via the Wordpress control panel = 

Make sure you have downloaded the .zip file containing the plugin. Then:

1. Go to 'Add' on the plugin administration panel
2. Proceed to 'Upload'
3. Choose the .zip file on your local drive containing the plugin
4. Click 'Install now'
5. Activate the plugin from the control panel

= Upload the plugin via FTP =

Make sure you have downloaded the .zip file containing the plugin. Then:

1. Unzip the folder 'wl-kultursok' to your local drive
2. Upload the folder 'wl-kultursok' to the '/wp-content/plugins/' folder (or wherever you store your plugins)
3. Activate the plugin from the control panel

= Or install it via the Wordpress repository! =

To place the search form in your post/page, first visit the shortcode creator tool via the menu: "Tools -> WL-kultursøk". On this page you can specify which sources to use and set other various options. This will result in a shortcode being automatically generated at the bottom of the page - copy and paste it into your page or post, and you're ready to go! Please note that a huge number of sources combined with many hits from each source will slow down your search, so find your own balance. Also be aware that this plugin has an option to cache results - so every subsequent visitor to that precise search won't have to wait as long as you did!

Note that the search form and results can be styled to your liking by overruling the CSS included with the plugin. 

NORWEGIAN:

= Laste opp innstikket i kontrollpanelet for Wordpress =

Sørg for at du har lastet ned ZIP-filen som inneholder innstikket. Deretter:

1. Gå til 'Legg til' på administrasjonssiden for innstikk
2. Gå til 'Last opp'
3. Velg ZIP-filen som inneholder innstikket på harddisken din
4. Klikk 'Installer nå'
5. Aktiver innstikket fra kontrollpanelet

= Laste opp innstikket via FTP =

Sørg for at du har lastet ned ZIP-filen som inneholder innstikket. Deretter:

1. Pakk ut mappen 'wl-kultursok' til datamaskinen din
2. Last opp mappen 'wl-kultursok' til '/wp-content/plugins/'-katalogen under din Wordpress-installasjon
3. Aktiver innstikket fra kontrollpanelet

= Eller installér det via Wordpress-katalogen! =

For å sette inn et søkefelt på siden din må du først gå til verktøyet for å lage kortkoder via menyen: "Verktøy -> WL-kultursøk". På denne siden kan du angi hvilke baser du vil søke i samt forskjellige andre innstillinger. Dette resulterer i en automatisk generert kortkode på bunnen av siden. Kopier og lim denne inn et innlegg eller på en side, så er du klar! Merk at søk i et stort antall baser kombinert med mange treff fra hver base vil resultere i tregere søk, så prøv deg fram. Vær også oppmerksom på innstillingen for å mellomlagre treff - da vil de påfølgende besøkende til det samme søket slippe å vente slik som du måtte!

Du kan også bestemme i detalj hvordan søket skal se ut ved å redigere den medfølgende CSS-filen.

== Frequently Asked Questions ==

= My search is so slow? =

There can be any number of reasons, including the servers we contact to get your search results. Try narrowing down your search by excluding some of the bases, and use the cache option to speed up subsequent queries for the same search. 

NORWEGIAN:

= Hvorfor er søket så treigt? =

Dette kan skyldes flere ting - blant annet hastigheten på tjenerne vi kontakter for å hente resultatene dine. Prøv å ekskludere noen baser fra søket, og skru på mellomlagring av trefflister slik at påfølgende identiske spørringer går raskere. 

== Screenshots ==

1. This is what your query result could look like
2. Create your own shortcode to insert in your posts/pages with this tool

NORWEGIAN:

1. Slik kan trefflisten din se ut
2. Med dette verktøyet lager du dine egne kortkoder som du kan sette inn i innlegg og på sider

== Change log ==

= 3.0.2 =

* New source: Mørerom

= 3.0.1 =

* Bugfix: Incomplete handling of spaces before and after search query
* Plenty of new sources: National Library of Norway (Flickr Commons), Trondheim city archive (Flickr), public library photo collections from: Askøy, Ballangen, Bømlo, Dønna, Hammerfest, Vadsø, Kongsvinger, Kvinnherad, Lurøy, Notodden, Porsgrunn, Ringsaker, Steinkjer and Åmot 

= 3.0 =  (Soft-release, 1. november 2015)

* Many new sources: Oppegård library image collection, Fusa library image collection, Sandefjord library local historical collection, Levanger library local historical collection, Local historical centre of Aurland (Flickr), Virksomme Ord (political speeches), Private manuscripts and letters from the National Library, Radio clips from NRK and the National Library, Bokhylla - everything (not limited to local history), Cultural heritage photos (Directorate for cultural heritage), Local photo collections (Deichman)
* Paginate search results - specify number of hits per page in shortcode
* New source: industrimuseum.no
* New sources: University museal collections: Archeology, coins, photos
* Now possible to shorten search results URL via bit.ly
* Caching of search results - great speed improvement! Also option to set cache TTL (time to live)
* New toolbar shown above search results (can be turned off in the shortcode): Sort and limit your results
* New toolbar shown under search results (can be turned off in the shortcode): Share results, broaden search in the original source databases
* Richer info extracted from lokalhistoriewiki.no
* Bugfix: Didn't display anything when no hits were found
* New display modes for search results: Tiles, simple list and carousel view
* Can now sort results by source, title or random order

= 2.0.1 = 

* Bugfix: Gracefully handles the case where we haven't selected any sources to search

= 2.0 =

Major rewrite.

* Code cleanup to avoid functions conflicting with other plugins
* Now fetches book covers from Webloft's own server
* No longer search-as-you-type
* Module-based system for adding new sources
* Added several new sources: Bærumkunst, Askerbilder, Bærumbilder, Digitalt fortalt, Digitalt Museum 
* Removed NB newspapers as a source
* Added error handling for cases where we don't get a decent result (broken XML, timeouts...)
* Each search now gets its own permalink (URL)

= 1.0.5 =

* Hitting Enter no longer opens a new window - you have to click the button
* Removed superfluous widget code

= 1.0.4 =

* Corrected errors in readme.txt
* Uploaded new screenshot
 
= 1.0.3 = 

* Bugfix: header text not respecting specified width
* Various cosmetic improvements

= 1.0.2 =

* Improved and more readable readme.txt

= 1.0.1 =

* Fixed various errors

= 1.0 =

* First version

NORWEGIAN:

= 3.0.2 =

* Ny base: Mørerom

= 3.0.1 =

* Bugfix: Taklet ikke mellomrom før og etter søketerm
* Nye baser: Nasjonalbibliotekets bilder på Flickr Commons, Trondheim byarkiv på Flickr, Bibliofil bildebaser fra Askøy, Ballangen, Bømlo, Dønna, Hammerfest, Vadsø, Kongsvinger, Kvinnherad, Lurøy, Notodden, Porsgrunn, Ringsaker, Steinkjer og Åmot 

= 3.0 = (Soft-release, 1. november 2015)

* Mange nye baser: Oppegård biblioteks bildebase, Fusa biblioteks bildebase, Sandefjord biblioteks lokalhistoriebase, Levanger biblioteks lokalhistoriebase, Lokalhistorisk senter i Aurland (Flickr), Virksomme Ord (politiske taler), Privatarkivmateriale fra Nasjonalbiblioteket, Radioklipp fra NRK / Nasjonalbiblioteket, industrimuseum.no, Universitetsmuseene - arkeologi, mynter/medaljer, bilder, Bokhylla - alt (ikke avgrenset til lokalhistorie), Lokalhistoriske bildebaser i Oslo (Deichman), Kulturminnebilder fra Riksantikvaren
* Paginering av treffliste - angi i shortcode hvor mange treff du vil ha per side
* Kan nå forkorte URL til trefflister vha. bit.ly
* Mellomlagring av trefflister - stor hastighetsgevinst! Også mulighet til å angi hvor lenge en treffliste skal lagres
* Ny verktøylinje over trefflisten (kan slås av i kortkoden): Sorter og avgrens treffene dine
* Ny verktøylinje under trefflisten (kan slås av i kortkoden): Del trefflisten, vis flere treff for ditt søk i de originale basene
* Rikere treff fra lokalhistoriewiki.no
* Bugfix: Ga ingen beskjed ved ingen treff
* Nye visninger av treffliste: Flislagt, enkel liste og karusell
* Mulighet for å sortere treffliste etter base, etter tittel eller tilfeldig rekkefølge

= 2.0.1 = 

* Bugfix: Tar hånd om tilfeller der vi ikke har valgt noen baser å søke i

= 2.0 =

Stor overhaling

* Rydding i kode for å unngå at funksjoner kommer i konflikt med andre utvidelser
* Henter nå omslagsbilder fra Webløfts egen server
* Søker ikke lenger automatisk mens du skriver
* Modulbasert innlegging av nye søkekilder
* Lagt til mange nye søkekilder: Bærumkunst, Askerbilder, Bærumbilder, Digitalt fortalt, Digitalt Museum 
* Fjernet aviser fra Nasjonalbiblioteket som søkekilde
* Mulighet for å lenke direkte til søk

= 1.0.5 = 

* Trykk på Enter åpner ikke lenger nytt vindu, du må klikke på knappen
* Fjernet overflødig widgetkode

= 1.0.4 =

* Rettet feil i readme.txt
* Tok nytt skjermskudd

= 1.0.3 =

* Bugfix: Angitt bredde ble ikke respektert i overskriftstekst
* Forskjellige kosmetiske forbedringer

= 1.0.2 =

* Bedre og mer forståelig readme.txt

= 1.0.1 =

* Fikset forskjellig småtteri

= 1.0 =

* Første versjon

== Upgrade Notice ==

No notice at this point

NORWEGIAN:

Ingen beskjeder akkurat nå
