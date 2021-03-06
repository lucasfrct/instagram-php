<?php 

/*
 * Autor: Laucas Costa
 * Data: Maio de 2020
 */

require_once ( 'Path.php' );

 $post = json_decode ( file_get_contents ( "php://input" ), true );

 if ( !empty ( $post ) && $post [ "action" ] == "access" && !empty ( $post [ "path" ] ) ) {

    echo json_encode ( Path::access ( $post [ "path" ], "*.{avi,AVI,mov,MOV,wmv,WMV,mp4, MP4,flv,FLV,mkv,MKV,rm,RM}" ) );

}
