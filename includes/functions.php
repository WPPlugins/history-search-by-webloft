<?php

if (!function_exists('wl_slugtilnavn')) { // tar slug, gir navn
	function wl_slugtilnavn ($slug) {
		include_once ("basenavn.php");
		foreach ($basenavn as $enbase) {
			$allinfo = explode ("|x|" , $enbase);
			if ($allinfo[0] == $slug) {
				return ($allinfo[1]);
			}
		}
	}
}

if (!function_exists('multibyte_ucfirst')) { // gjør første bokstav stor, også æøå
	function multibyte_ucfirst($str) {
	    $fc = mb_strtoupper(mb_substr($str, 0, 1));
	    return $fc.mb_substr($str, 1);
	}
}

if (!function_exists('wl_make_bitly_url')) {
	function wl_make_bitly_url($url,$login,$appkey,$format = 'txt') {
	
		/* usage */
		//$kortlenke = make_bitly_url($ferdigurl,'sundaune','R_API_KEY','json');
		// returns:  http://bit.ly/11Owun
		
		//create the URL
		$bitly = 'http://api.bit.ly/v3/shorten?'.'longUrl='.urlencode($url).'&login='.$login.'&apiKey='.$appkey.'&format='.$format;
	
		//get the url
		//could also use cURL here
		$response = get_content($bitly);
		
		return trim($response);
	}
}



if (!function_exists('wlhighlight')) {
	function wlhighlight($text, $words, $case='1') { 
	 $words = trim($words); 
	 $wordsArray = explode(' ', $words); 
	 
	 foreach($wordsArray as $word) { 
	  if(strlen(trim($word)) != 0) 
	   if ($case) {
	    $text = eregi_replace($word, '<span class="wlsearchhighlight">\\0</span>', $text);
	     } else {
	    $text = ereg_replace($word, '<span class="wlsearchhighlight">\\0</span>', $text); 
	   }
	  } 
	 return $text; 
	} 
}


if (!function_exists('wlbrekkurl')) {
	function wlbrekkurl () {
		// Returnerer array[0] => hele URL fram til ? og array[1] => array med $parameter -> $value
		$url[0] = stristr ($_SERVER['REQUEST_URI'] , "?" , TRUE); // alt før ? er URL
		$queries = stristr ($_SERVER['REQUEST_URI'] , "?"); // alt etter ? er queries
		$queries = str_replace ("?" , "" , $queries); // men vi må ikke ha med ?
		$enogenquery = explode ("&" , $queries);
		foreach ($enogenquery as $ettogettquery) {
			$dollyparton = explode ("=" , $ettogettquery);
			if (!isset($dollyparton[1])) { // hvis bare 'parameter=' uten noen verdi
				$dollyparton[1] = '';
			}
			//$allequeries[$dollyparton[0]] = $dollyparton[1];
			$url[1][$dollyparton[0]] = $dollyparton[1];
			//unset ($allequeries);
		}
	return $url;
	}
}

if (!function_exists('wlsimplifyurl')) {
	function wlsimplifyurl ($url) {
		// tar $url[0] => hele URL fram til ? og $url[1] => array med $parameter -> $value
		// Fjerner alle duplikater i URL av "parameter=" slik at bare den siste gjelder
		$nyurl[0] = $url[0]; // ikke forandret
		foreach ($url[1] as $key => &$value) {
			$nyeparams[$key] = $value; // magic
		}
		$nyurl[1] = $nyeparams;
		return $nyurl;
	}
}

if (!function_exists('domp')) {
	function domp ($whattodomp) {
		echo "<pre>";
		print_r ($whattodomp);
		echo "</pre>";
	}
}

if (!function_exists('rop')) {
	function rop ($whattorop) {
		echo "<h1>*" . $whattorop . "*</h1>";
	}
}

if (!function_exists('get_content')) {
	function get_content($url) { 
	
		$ch = curl_init();  
	     
		curl_setopt ($ch, CURLOPT_URL, $url);  
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT , 5); 
		curl_setopt ($ch, CURLOPT_TIMEOUT, 10); 
		//curl_setopt ($ch, CURLOPT_FRESH_CONNECT, TRUE); // bruk frisk forbindelse

$string = curl_exec($ch);
return $string;

      
/*
		ob_start();  
	      
		curl_exec ($ch);  
		curl_close ($ch);  
		$string = ob_get_contents();  
	      
		ob_end_clean();  
	         
		return $string;
*/
	}  
}

if (!function_exists('simpletrunc')) {
	function simpletrunc($phrase, $max_words) {
	   $phrase_array = explode(' ',$phrase);
	   if(count($phrase_array) > $max_words && $max_words > 0)
	      $phrase = implode(' ',array_slice($phrase_array, 0, $max_words)).'...';
	   return $phrase;
	}
}

if (!function_exists('tilstreng')) {	
	function tilstreng(&$item){
	   $item = (string) $item;
	}
}

if (!function_exists('merbaseinfo')) {
	function merbaseinfo ($lenke) { // Viser lenke til mer info om en base
	 echo '[<a target="_blank" href="' . $lenke . '">mer info</a>]';
	}
}

if (!function_exists('trunc')) {
	function trunc($text, $length, $ending = '...', $exact = false, $considerHtml = true) {

// Hack: Funksjonen vi brukte tidligere tok antall ord som argument, denne tar antall tegn. Vi antar at ett ord er fem tegn...
$length = 5 * $length;

	    if ($considerHtml) {
    	    // if the plain text is shorter than the maximum length, return the whole text
        	if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
	            return $text;
	        }
        	// splits all html-tags to scanable lines
	        preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
	        $total_length = strlen($ending);
	        $open_tags = array();
	        $truncate = '';
	        foreach ($lines as $line_matchings) {
	            // if there is any html-tag in this line, handle it and add it (uncounted) to the output
	            if (!empty($line_matchings[1])) {
	                // if it's an "empty element" with or without xhtml-conform closing slash
	                if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
	                    // do nothing
	                // if tag is a closing tag
	                } else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
	                    // delete tag from $open_tags list
	                    $pos = array_search($tag_matchings[1], $open_tags);
	                    if ($pos !== false) {
		                    unset($open_tags[$pos]);
	                    }
	                // if tag is an opening tag
	                } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
	                    // add tag to the beginning of $open_tags list
	                    array_unshift($open_tags, strtolower($tag_matchings[1]));
	                }
	                // add html-tag to $truncate'd text
	                $truncate .= $line_matchings[1];
	            }
	            // calculate the length of the plain text part of the line; handle entities as one character
	            $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
	            if ($total_length+$content_length> $length) {
	                // the number of characters which are left
	                $left = $length - $total_length;
	                $entities_length = 0;
	                // search for html entities
	                if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
	                    // calculate the real length of all entities in the legal range
	                    foreach ($entities[0] as $entity) {
	                        if ($entity[1]+1-$entities_length <= $left) {
	                            $left--;
	                            $entities_length += strlen($entity[0]);
	                        } else {
	                            // no more characters left
	                            break;
	                        }
	                    }
	                }
	                $truncate .= substr($line_matchings[2], 0, $left+$entities_length);
	                // maximum lenght is reached, so get off the loop
	                break;
	            } else {
	                $truncate .= $line_matchings[2];
	                $total_length += $content_length;
	            }
	            // if the maximum length is reached, get off the loop
	            if($total_length>= $length) {
	                break;
	            }
	        }
	    } else {
	        if (strlen($text) <= $length) {
	            return $text;
	        } else {
	            $truncate = substr($text, 0, $length - strlen($ending));
	        }
	    }
	    // if the words shouldn't be cut in the middle...
	    if (!$exact) {
	        // ...search the last occurance of a space...
	        $spacepos = strrpos($truncate, ' ');
	        if (isset($spacepos)) {
	            // ...and cut the text in this position
	            $truncate = substr($truncate, 0, $spacepos);
	        }
	    }
	    // add the defined ending to the text
	    $truncate .= $ending;
	    if($considerHtml) {
	        // close all unclosed html-tags
	        foreach ($open_tags as $tag) {
	            $truncate .= '</' . $tag . '>';
	        }
	    }
	    return $truncate;
	}
}


/*********************************************
Sorteringsfunksjoner for å sortere treffliste
/*********************************************/
if (!function_exists('wltittelasc')) {
	function wltittelasc($a, $b) {
		return @strcmp(wl_sort_strip($a['tittel']), wl_sort_strip($b['tittel']));
	}
}

if (!function_exists('wltitteldesc')) {
	function wltitteldesc($a, $b) {
		return @strcmp(wl_sort_strip($b['tittel']), wl_sort_strip($a['tittel']));
	}
}

if (!function_exists('wldigidatoasc')) {
	function wldigidatoasc($a, $b) {
		return @strcmp($a['digidato'], $b['digidato']);
	}
}

if (!function_exists('wldigidatodesc')) {
	function wldigidatodesc($a, $b) {
		return @strcmp($b['digidato'], $a['digidato']);
	}
}

if (!function_exists('wldatoasc')) {
	function wldatoasc($a, $b) {
		return @strcmp($a['dato'], $b['dato']);
	}
}

if (!function_exists('wldatodesc')) {
	function wldatodesc($a, $b) {
		return @strcmp($b['dato'], $a['dato']);
	}
}

if (!function_exists('wlansvarasc')) {
	function wlansvarasc($a, $b) {
		return @strcmp($a['ansvar'], $b['ansvar']);
	}
}

if (!function_exists('wlansvardesc')) {
	function wlansvardesc($a, $b) {
		return @strcmp($b['ansvar'], $a['ansvar']);
	}
}


// Stripper bort "() for sorteringsformål

if (!function_exists('wl_sort_strip')) {
	function wl_sort_strip ($streng) {
		$strung = str_replace ("\"" , "" , $streng);
		$strung= str_replace ("(" , "" , $strung);
		$strung= str_replace (")" , "" , $strung);
		return ($strung);
	}
}

