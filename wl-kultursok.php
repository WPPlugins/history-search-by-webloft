<?php

/**
 *
 * @package   WL Kultursøk
 * @author    H&aring;kon Sundaune / Bibliotekarens beste venn <haakon@bibliotekarensbestevenn.no>
 * @license   GPL-3.0+
 * @link      http://www.bibvenn.no
 * @copyright 2015 Håkon M. E. Sundaune
 *
 * @wordpress-plugin
 * Plugin Name:       WL Kulturs&oslash;k
 * Plugin URI:        http://www.bibvenn.no/
 * Description:       S&oslash;ker etter historisk materiale / search for historical material (books, images, video, audio...)
 * Version:           3.0.2
 * Author:            H&aring;kon Sundaune / Bibliotekarens beste venn
 * Author URI:        http://www.bibvenn.no/
 * Text Domain:       wl-kultursok-locale
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path:       /languages
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// INCLUDE NECESSARY  
    
    add_action( 'wp_enqueue_scripts', 'finnlokalhistorie_safely_add_stylesheets_and_scripts' );
    add_action( 'admin_enqueue_scripts', 'finnlokalhistorie_admin_styles_and_scripts' );

	require_once("includes/functions.php"); // funksjoner vi har bruk for

    /**
     * Add stylesheets and script to the page
     */

	function finnlokalhistorie_admin_styles_and_scripts() {
		wp_enqueue_script('finnlokalhist-public', plugins_url( 'js/admin.js', __FILE__ ), array('jquery') );
	    wp_enqueue_style ('finnlokalhistorie-admin-style', plugins_url('/css/admin.css', __FILE__) );
	}

    function finnlokalhistorie_safely_add_stylesheets_and_scripts() {
	    wp_enqueue_style ('finnlokalhistorie-shortcode-style', plugins_url('/css/public.css', __FILE__) );
	    wp_enqueue_style ('finnlokalhistorie-responsive-style', plugins_url('/css/media.css', __FILE__) );
	    wp_enqueue_style ('finnlokalhistorie-slick-style', plugins_url('/css/slick.css', __FILE__) );
	    wp_enqueue_style ('finnlokalhistorie-slick-theme-style', plugins_url('/css/slick-theme.css', __FILE__) );
	    wp_enqueue_style ('finnlokalhistorie-paginate-style', plugins_url('/css/paginate.css', __FILE__) );
		
		wp_enqueue_script('finnlokalhist-public', plugins_url( 'js/public.js', __FILE__ ), array('jquery') );
		wp_enqueue_script('finnlokalhist-accordion', plugins_url( 'js/accordion.js', __FILE__ ), array('jquery') );
		wp_enqueue_script('finnlokalhist-accordion-init', plugins_url( 'js/accordion_init.js', __FILE__ ), array('jquery') );
		wp_enqueue_script('finnlokalhist-masonry', plugins_url( 'js/masonry.pkgd.min.js', __FILE__ ), array('jquery') );
		wp_enqueue_script('finnlokalhist-masonry-imagesload', plugins_url( 'js/imagesloaded.pkgd.min.js', __FILE__ ), array('jquery') );
		wp_enqueue_script('finnlokalhist-masonry-init', plugins_url( 'js/masonry-init.js', __FILE__ ), array('jquery') );
		wp_enqueue_script('finnlokalhist-slick', plugins_url( 'js/slick.min.js', __FILE__ ), array('jquery') );
		wp_enqueue_script('finnlokalhist-slick-init', plugins_url( 'js/slick.js', __FILE__ ), array('jquery') );
    }

// FIRST COMES THE SHORTCODE... EH, CODE!

function finnlokalhistorie_func ($atts){

extract(shortcode_atts(array(
	'makstreff' => "20",
	'visning' => "utvidet",
	'baser' => "bokhylla",
	'sortering' => "base",
	'wlgjemverktoyover' => "0",
	'wlgjemverktoyunder' => "0",
	'wltreffperside' => "25"
   ), $atts));

if ($makstreff > 100) { // 100 er maks for noen av basene
$makstreff = 100;
}

// DEFINE HTML TO OUTPUT WHEN SHORTCODE IS FOUND 
$htmlout = '';

$lokalhistmakstreff = (int) $makstreff;

if ((isset($_REQUEST['lokalhistquery'])) && (trim($_REQUEST['lokalhistquery']) != '')) {
	$querystring = trim(strip_tags(stripslashes($_REQUEST['lokalhistquery'])));
	// Og da vise spinner
	$spinnerdisplay = "block";
} else {
	$querystring = '';
	$spinnerdisplay = "none";
}

@$oldargs = stristr (get_permalink ($post->id) , "?"); // alt etter ? er query vars
$oldargs = str_replace ("?" , "" , $oldargs);
$oldargs = wp_parse_args ($oldargs);

@$formaction = stristr (get_permalink ($post->id) , "?" , TRUE); // alt FØR ? er basis
$formaction = str_replace ("?" , "" , $formaction);

$htmlout .= '<div class="lokalhistorie_skjema">';
$htmlout .= '<form method="GET" action="' . $formaction . '" onsubmit="wlshowspinner();">';

$htmlout .= '<input type="text" name="lokalhistquery" value="' . str_replace("\"" , "&quot;" , $querystring) . '" placeholder="S&oslash;k etter..." />';

$htmlout .= '<div id="wlspinner" class="wlspinner" style="display:' . $spinnerdisplay . ';">' . "\n";
$htmlout .= '<img id="wlimg-spinner" src="' . plugins_url( 'g/wlspinner.gif', __FILE__ ) . '" alt="Laster..."/>' . "\n";
$htmlout .= '</div>' . "\n";

$htmlout .= '<input id="wlbutton-upload" type="submit" value="S&oslash;k">';
$htmlout .= '<input type="hidden" name="baser" value="' . esc_attr($baser) . '">' . "\n";
$htmlout .= '<input type="hidden" name="makstreff" value="' . (int) ($makstreff) . '">' . "\n";
$htmlout .= '<input type="hidden" name="visning" value="' . esc_attr($visning) . '">' . "\n";
$htmlout .= '<input type="hidden" name="sortering" value="' . esc_attr($sortering) . '">' . "\n";
$htmlout .= '<input type="hidden" name="wlgjemverktoyover" value="' . (int) $wlgjemverktoyover . '">' . "\n";
$htmlout .= '<input type="hidden" name="wlgjemverktoyunder" value="' . (int) $wlgjemverktoyunder . '">' . "\n";
$htmlout .= '<input type="hidden" name="wltreffperside" value="' . (int) $wltreffperside . '">' . "\n";

// Legge til de som finnes fra før
foreach ($oldargs as $key => $value) {
	$htmlout .= '<input type="hidden" name="' . $key . '" value="' . $value . '">' . "\n";
}

$htmlout .= '<br style="clear: both;">';
$htmlout .= '</form>';
$htmlout .= '</div>';

if ($querystring != '') {
	ob_start();
	include "search.php";
	$result = ob_get_contents();
	$htmlout .= $result;
	ob_end_clean();


} else {
	// Ingen treff, ingen kake
	$htmlout .= '';
}

return $htmlout;

}; // end function
 
add_shortcode("wl-kultursok", "finnlokalhistorie_func");

// Settings page

add_action('admin_menu', 'lokalhist_toolspage'); // shortcode generator
add_action('admin_menu', 'lokalhist_settingspage'); // plugin settings
add_action('admin_init', 'lokalhist_registersettings');

function lokalhist_settingspage() {
    add_options_page("WL Kulturs&oslash;k innstillinger", "WL Kulturs&oslash;k", "manage_options", "lokalhist_options", "lokalhist_settings_page");
}

function lokalhist_toolspage() {
    add_submenu_page("tools.php", "WL Kulturs&oslash;k", "WL Kulturs&oslash;k", "edit_posts", "lokalhist_options", "lokalhist_tools_page");
}

function lokalhist_registersettings() {
	// Add options to database if they don't already exist
	add_option("wl_bruk_cache", "1", "", "yes");
	add_option("wl_uthev_term", "1", "", "yes");
	add_option("wl_keep_cache_for", "1209600", "" , "yes"); // default = 7776000 sekunder = 3 mnd.
	add_option("wl_bitly_key", "", "", "yes");
	add_option("wl_bitly_login", "", "", "yes");
	add_option("wl_flickr_api_key", "", "", "yes");
	add_option("wl_youtube_api_key", "", "", "yes");

	// Register settings that this form is allowed to update
	register_setting('lokalhist_options', 'wl_bruk_cache');
	register_setting('lokalhist_options', 'wl_uthev_term');
	register_setting('lokalhist_options', 'wl_keep_cache_for');
	register_setting('lokalhist_options', 'wl_bitly_key');
	register_setting('lokalhist_options', 'wl_bitly_login');
	register_setting('lokalhist_options', 'wl_flickr_api_key');
	register_setting('lokalhist_options', 'wl_youtube_api_key');
}

function lokalhist_settings_page () {
	require_once dirname(__FILE__) . '/includes/settings.php';
}

function lokalhist_tools_page() {
    if (!current_user_can('edit_posts'))
        wp_die(__("Du har ikke tilgang til denne siden"));
	require_once dirname(__FILE__) . '/includes/shortcode-generator.php';
}


