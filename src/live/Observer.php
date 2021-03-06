<?php

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

session_start();

require_once ( "Archive.php" );

class Observer 
{
    public static $urlLog = "log/";

    public static function EventCreate ( string $name, string $data, $retry = 1000 ) {
        echo "event: {$name}\n";
        echo "retry: {$retry}\n";
        echo "data: {$data}\n\n";
    }

    public static function EventCount ( string $name, $count = "" ) {

        if ( $count !== "" && $count == 0) {
            $_SESSION [ $name ] = 0;
        }

        if ( $count !== "" && $count > 0 ) {
            $_SESSION [ $name ] = ( $_SESSION [ $name ] + $count );
        }

        return $_SESSION [ $name ]; 
    }

    public static function LogCreate ( $name, $data ) {
        return Archive::write ( self::$urlLog."{$name}.log", $data );
    }

    public static function LogRead( $name ) {
        return Archive::read ( self::$urlLog."{$name}.log" );
    }

    public static function LogEqual ( string $cache, string $cached ) {
        return ( strcmp ( $cache, $cached ) == 0 );
    }

}