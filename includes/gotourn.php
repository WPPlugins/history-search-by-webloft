<?php

// skal motta lenke, bilde, tittel
// dynamisk lage og-data
// så videresende til lenke
// params: 0: Tittel 1: Beskrivelse (IKKE FOR LANG!) 2: url 3: bilde 4: ansvar

$getthem = strip_tags(stripslashes($_SERVER["QUERY_STRING"]));
parse_str ($getthem);
$params = unserialize(base64_decode($params));

$tittel = html_entity_decode($params['tittel']);
$tittel = str_replace ("<br>" , ". " , $tittel);

$beskrivelse = html_entity_decode($params['beskrivelse']);
$beskrivelse = str_replace ("<br>" , ". " , $beskrivelse);
$beskrivelse = strip_tags($beskrivelse);
$beskrivelse = str_replace ("<" , "" , $beskrivelse);
$beskrivelse = str_replace (">" , "" , $beskrivelse);
$beskrivelse = str_replace ("&lt;" , "" , $beskrivelse);
$beskrivelse = str_replace ("&gt;" , "" , $beskrivelse);
$beskrivelse = str_replace ("\"" , "'" , $beskrivelse);

$url = $params['url'];
$bilde = $params['bilde'];
$ansvar = html_entity_decode($params['ansvar']);

@list($width, $height, $type, $attr) = getimagesize($bilde);

header('Content-type: text/html; charset=utf-8');

?>
<html xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://ogp.me/ns#">
    <head>
        <meta charset="utf-8">

        <title><?= $tittel ?> (<?= $ansvar ?>)</title>

		<meta property="fb:app_id" content="977481908941789" /> 

        <meta name="twitter:card" content="photo">
        <meta name="twitter:site" content="@bibvenn">
        <meta name="twitter:title" content="<?= $tittel ?> (<?= $ansvar ?>)">
        <meta name="twitter:image" content="<?= $bilde ?>">
        <meta name="twitter:url" content="<?= $url ?>">

        <meta property="og:description" content="<?= $beskrivelse ?>">
        <meta property="og:title" content="<?= $tittel ?>">
        <meta property="og:image" content="<?= $bilde ?>">
        <meta property="og:image:type" content="image/jpeg">
        <meta property="og:image:height" content="<?= $height ?>">
        <meta property="og:image:width" content="<?= $width ?>">

        <meta http-equiv="refresh" content="2;<?= $url ?>">
    </head>
    <body>Venter på omdirigering...</body>
</html>
