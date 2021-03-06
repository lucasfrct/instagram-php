<?php 

require_once ( "live/Archive.php" );

$post = json_decode ( file_get_contents ( "php://input" ), true );

if ( !empty($post) && $post [ "action" ] == "code" ) {
    $data = json_encode ( $post );
    Archive::write ( "keys/".$post [ "id" ].".json", $data );
    echo $data;
}


