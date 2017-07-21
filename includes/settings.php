<?php

$wl_bruk_cache = get_option('wl_bruk_cache' , '1');
$wl_uthev_term = get_option('wl_uthev_term' , '1');
$wl_keep_cache_for = get_option('wl_keep_cache_for' , '1209600');
$wl_bitly_key = get_option('wl_bitly_key' , '');
$wl_bitly_login = get_option('wl_bitly_login' , '');
$wl_flickr_api_key = get_option('wl_flickr_api_key' , '');
$wl_youtube_api_key = get_option('wl_youtube_api_key' , '');

?>
<div class="wrap">

    <form method="post" action="options.php">

    <h1>WL Kulturs&oslash;k - innstillinger</h1>

        <?php settings_fields('lokalhist_options'); ?>

		<h2>Mellomlagring (cache)</h2>
		<p>
         <label for="wl_bruk_cache">Mellomlagre trefflister til senere bruk (anbefales)?</label>&nbsp;
            <input name="wl_bruk_cache" type="checkbox" value="1" <?php if ($wl_bruk_cache == "1") { echo "checked";} ?> />

		<br>
         <label for="wl_keep_cache_for">N&aring;r skal lagrede trefflister g&aring; ut p&aring; dato og slettes (standard er etter 2 uker, som skal virke greit for nettsteder med normal trafikk)?</label>&nbsp;
            <select name="wl_keep_cache_for" />
				<option value="604800"<?php if ($wl_keep_cache_for == "604800") { echo " selected"; } ?>>Etter 1 uke</option>
				<option value="1209600"<?php if ($wl_keep_cache_for == "1209600") { echo " selected"; } ?>>Etter 2 uker</option>
				<option value="1814400"<?php if ($wl_keep_cache_for == "1814400") { echo " selected"; } ?>>Etter 3 uker</option>
				<option value="3628800"<?php if ($wl_keep_cache_for == "3628800") { echo " selected"; } ?>>Etter 6 uker</option>
			</select>
		</p>
	
		<h2>Utseende</h2>

		<p>
		<label for="wl_uthev_term">Utheve s&oslash;keterm i trefflista?</label>&nbsp;
            <input name="wl_uthev_term" type="checkbox" value="1" <?php if ($wl_uthev_term == "1") { echo "checked";} ?> />
		</p>
		<h2>Forkorte lange URL-er</h2>
		<p>Dersom du vil forkorte URL-er med Bit.ly, angi brukernavn og API-n&oslash;kkel under:<br>
		<label for="wl_bitly_login">Bit.ly brukernavn&nbsp;</label><input name="wl_bitly_login" type="text" value="<?= $wl_bitly_login; ?>" />
		<br>
		<label for="wl_bitly_key">Bit.ly API-n&oslash;kkel:&nbsp;</label><input name="wl_bitly_key" type="text" value="<?= $wl_bitly_key; ?>" />
		<br>		
		Har du ikke API-n&oslash;kkel kan du skaffe deg det <a target="_blank" href="https://bitly.com/a/your_api_key">her</a>. 

        </p>

		<h2>Tilgang til Flickr</h2>
		<p>Vil du bruke noen av Flickr-basene som er inkludert i Kulturs&oslash;k krever Flickr at du oppgir en API-n&oslash;kkel:<br>
		<label for="wl_flickr_api_key">Flickr API-n&oslash;kkel&nbsp;</label><input name="wl_flickr_api_key" type="text" value="<?= $wl_flickr_api_key; ?>" />
		<br><br>		
		Les mer om API-n&oslash;kkel <a target="_blank" href="https://www.flickr.com/services/api/misc.api_keys.html">her</a>. 

        </p>

		<h2>Tilgang til Youtube</h2>
		<p>Vil du bruke noen av Youtube-basene som er inkludert i Kulturs&oslash;k krever Youtube at du oppgir en API-n&oslash;kkel:<br>
		<label for="wl_youtube_api_key">Flickr API-n&oslash;kkel&nbsp;</label><input name="wl_youtube_api_key" type="text" value="<?= $wl_youtube_api_key; ?>" />
		<br><br>		
		Les mer om API-n&oslash;kkel <a target="_blank" href="https://en.wikipedia.org/wiki/YouTube_API">her</a>. 

        </p>


        <p class="submit">
            <input type="submit" class="button-primary" value="Oppdat&eacute;r" />
        </p>

    </form>
</div>

