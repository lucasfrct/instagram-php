<?php

set_time_limit ( 60 );                  // meio minuto
date_default_timezone_set ( 'UTC' );    // set regiao
ignore_user_abort ( true );             // rodar mesmo depois que acabar 

header ( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); 
header ( 'Cache-Control: no-store, no-cache, must-revalidate' ); 
header ( 'Cache-Control: post-check=0, pre-check=0', FALSE ); 
header ( 'Pragma: no-cache' );

require ( '../vendor/autoload.php' );
require_once ( 'live/Archive.php' );
require_once ( 'live/Alive.php' );

use InstagramAPI\Instagram;
use InstagramAPI\Request\Live;

$post = json_decode ( file_get_contents ( "php://input" ), true );

if ( !empty ( $post ) && $post [ "action" ] == "streamkey" && !empty ( $post [ "id" ] ) ) {
    
    $GLOBALS["KEY-URL"] = generateKeyUrl($post [ "id" ]);

    Archive::write($GLOBALS["KEY-URL"], json_encode($post));
    
    \InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
    $ig = new Instagram(false, false);

    Alive::$src = $GLOBALS["KEY-URL"];
    Alive::login($ig);
    Alive::StreamKey($ig);
}

function generateKeyUrl ( $id ) {
    return "keys/{$id}.log";
}