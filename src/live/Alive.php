<?php 
 /*
  * Autor: Lucas Costa
  * Data: Abril de 2020
  * Alive::Login ( $ig, $user, $password )
  */

require_once ( "Archive.php" );

set_time_limit(0);
date_default_timezone_set('UTC');

Class Alive
{
    public static $src = "";
    public static $status = [];
    
    # Login 
    public static function Login ( &$ig ): array 
    { 
        self::update ( function ( &$stream ) {
            $GLOBALS [ "user" ] = $stream [ "user" ];
            $GLOBALS [ "password" ] = $stream [ "password" ];
            return $stream;
        } );

        try {
            
            $loginResponse =  $ig->login ( $GLOBALS [ "user" ], $GLOBALS [ "password" ] );

            if ( $loginResponse !== null && $loginResponse->isTwoFactorRequired() ) {
                
                $twoFactorIdentifier = $loginResponse->getTwoFactorInfo()->getTwoFactorIdentifier();

                $verificationCode = self::verificationCode ( self::$src, 60 );

                $ig->finishTwoFactorLogin ( $GLOBALS [ "user" ], $GLOBALS [ "password" ], $twoFactorIdentifier, $verificationCode );

            }

            array_push ( self::$status, array ( "LoginSuccess"=> "User: ".$GLOBALS [ "user" ] ) );

        } catch ( \Exception $e ) {
            $ig = null;
            self::CleanSession($ig);
            array_push ( self::$status, array ( "LoginError"=> "User: ".$GLOBALS [ "user" ]." | ".$e->getMessage ( ) ) );
            echo $e->getMessage();

        }

        self::update ( function ( &$stream ) {
            $stream [ "status" ] = self::$status;
            return $stream;
        } );

        return self::$status;

    }

    # habilita verificação de dois fatores
    public static function verificationCode ( string $src = "", $wait = 10 )
    {
        array_push ( self::$status, array ( "Code"=> "Require Verification: ".$GLOBALS [ "user" ] ) );

        self::update ( function ( &$stream ) { 
            $stream [ "required" ] = true;
            $stream [ "status" ] = self::$status; 
            return $stream;
        } );


        ob_end_clean ( );
        ob_start ( );

        $code = "";
        
        $time = 0;
        
        $timeStart = microtime ( true );

        do {

            $timeCurrent = microtime ( true );

            $time  = ( $timeCurrent - $timeStart );

            $stream =  json_decode ( Archive::read ( $src ), true );
            
            if ( !empty ( $stream ) && true == $stream [ "required" ] && !empty ( $stream [ "code" ] ) ) {
                
                $code = base64_decode($stream [ "code" ]);
                
                array_push ( self::$status , array ( "Code"=> "OK. Código obtido: {$code}" ) );

                self::update( function ( &$stream ) { 
                    $stream [ "status" ] = self::$status;
                    $stream [ "required" ] = false;
                    $stream [ "code" ] = "";
                    return $stream;
                } );


                break;
            }

            ob_flush ( );
            flush ( );
            sleep ( 1 );

        } while ( $time < $wait );

        ob_end_flush ( );

        return $code;
    }

    # obtem a URL com seridor e chave para acesso ao braodcast
    public static function StreamKey ( &$ig ) {
        
        try {
            if ( null != $ig ) {
                $stream = $ig->live->create();
            
                $broadcastId = $stream->getBroadcastId();
                
                $ig->live->start($broadcastId);
                
                $streamUploadUrl = $stream->getUploadUrl();
                
                $split = preg_split("[".$broadcastId."]", $streamUploadUrl);

                $GLOBALS [ "streamUrl" ] = $split [0];
                $GLOBALS [ "streamKey" ] = $broadcastId.$split[1];
                $GLOBALS [ "broadcast" ] = $streamUploadUrl;

                array_push ( self::$status, array ( "Stream"=> "start broadcast and Get server and key ") );
                
                self::update ( function ( &$stream ) {
                    $stream [ "server" ] = $GLOBALS [ "streamUrl" ];
                    $stream [ "key" ] = $GLOBALS [ "streamKey" ];
                    $stream [ "broadcast" ] = $GLOBALS [ "broadcast" ];
                    $stream [ "status" ] = self::$status;

                    return $stream;
                } );

            }

            self::CleanSession($ig);

        } catch ( \Exception $e) {
            self::CleanSession($ig);
            array_push ( self::$status, array ( "StreamError"=> $e->getMessage()) );
            echo $e->getMessage();
        }

        self::update ( function ( &$stream ) {
            $stream [ "status" ] = self::$status;
            return $stream;
        } );
    }

    public static function update ( $fn ) 
    {
        $stream = json_decode ( Archive::read ( self::$src ), true );

        $stream = $fn ( $stream ); 

        Archive::write ( self::$src, json_encode ( $stream ) );
    }

    public static function CleanSession ( &$ig ) 
    {   
        $ig = null;
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time()-1000);
                setcookie($name, '', time()-1000, '/');
            }
        }

        session_unset ( );
    }
}


