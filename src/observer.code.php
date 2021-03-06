<?php

require_once ( "live/Observer.php" );
require_once ( "live/Archive.php" );

$id = $_GET [ "id" ] ?? "";
$data = "";

if ( !empty ( $id ) ) {
    $data = Archive::read ( "keys/".$id.".log" );
    Observer::EventCreate ( 'code', $data );
}
