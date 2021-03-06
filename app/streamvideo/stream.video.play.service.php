<?php 

/*
 * Autor: Lucas Costa
 * Data: Abril de 2020
 * 
 */

require_once ( "../alive/Alive.php" );
require_once ( "../../vendor/autoload.php" );

#$stream = json_decode ( file_get_contents ( "php://input" ), true );
#Archive::write ( "../log/stream.log", json_encode ( $stream ) );

#\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
#$ig = new \InstagramAPI\Instagram ( false, false );

#Alive::Login ( $ig );
#Alive::StreamVideo ( $ig, $stream [ "path" ], $stream [ "position" ]  );