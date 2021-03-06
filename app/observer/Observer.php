<?php
/*
 * Autor: Lucas Costa
 * Data: Abril de 2020
 * Observador PHP em Server-Sent para eventos javascript 
 * 
 * CODE JS
 *      var source = new EventSource('obseerver.event.php');
 * 
 */
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

session_start();

class Observer 
{
    public static function Event ( string $name = "", string $data = "", $retry = "1000" ) {
        echo "event: {$name}\n";
        echo "retry: {$retry}\n";
        echo "data: {$data}\n\n";
    }

    public static function Counter ( string $name, $count = "" ) {

        if ( $count !== "" && $count == 0) {
            $_SESSION [ $name ] = 0;
        }

        if ( $count !== "" && $count > 0 ) {
            $_SESSION [ $name ] = ( $_SESSION [ $name ] + $count );
        }

        return $_SESSION [ $name ]; 
    }
}