<?php 

/*
 * Autor: Lucas Costa
 * data: Abril de 2020
 */

 require_once ( "../archive/Archive.php" );

$stream = json_decode ( file_get_contents ( "php://input" ), true );

if ( !empty ( $stream ) && $stream [ "action" ] == "stop" ) {
    Archive::Write ( "../log/stream.log", json_encode ( $stream ) );
}