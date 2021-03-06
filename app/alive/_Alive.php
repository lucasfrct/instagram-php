<?php 
 /*
  * Autor: Lucas Costa
  * Data: Abril de 2020
  * Alive::Login ( $ig, $user, $password )
  */

require_once ( "../archive/Archive.php" );

set_time_limit(0);
date_default_timezone_set('UTC');

Class Alive
{
    public static $streamSrc = "../log/stream.log";
    public static $status = [];
    
    
    public static $logSrc = "../log/stream.log";
    public static $response = [ 
        "status"=> [ ],
        "error"=> [ ]
    ];




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

            if ( $loginResponse !== null && $loginResponse->isTwoFactorRequired ( ) ) {

                sleep(1);
                
                $twoFactorIdentifier = $loginResponse->getTwoFactorInfo ( )->getTwoFactorIdentifier ( );

                $verificationCode = self::verificationCode ( self::$streamSrc, 60 );

                $ig->finishTwoFactorLogin ( $user, $password, $twoFactorIdentifier, $verificationCode );

            }

            array_push ( self::$status, array ( "Login"=> "Success user: ".$GLOBALS [ "user" ] ) );

        } catch ( \Exception $e ) {

            array_push ( self::$status, array ( "Login Error"=> "Error user: ".$GLOBALS [ "user" ]." | ".$e->getMessage ( ) ) );

        }

        self::update ( function ( &$stream ) {
            $stream [ "status" ] = self::$status;
            return $stream;
        } );

        return self::$response;

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
                
                $code = $stream [ "code" ];
                
                array_push ( self::$status , array ( "Code"=> "Codigo capturado com sucesso: ".$GLOBALS [ "user" ] ) );

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
        sleep ( 1 );

        return $code;
    }

    # @param: transpose = 1 (lanscape) | transpose = 4 (portrait)
    public static function StreamVideo ( &$ig, string $videoFilename = "", string $transpose = "portrait" ) 
    {  
        $ffmpegPath = realpath ( "../../assets/ffmpeg/ffmpeg.exe" );

        $filter = '';

        if ( $transpose == "portrait" ) {
            $filter = '-filter:v "crop=(iw/2.9):ih"';            
        }

        $transpose = ( $transpose == "landscape" ) ? "1" : "4";

        array_push ( self::$status , array ( "Stream"=> "Start Stream file: ".$videoFilename ) );


        try {

            $ffmpeg = \InstagramAPI\Media\Video\FFmpeg::factory($ffmpegPath);
            
            $stream = $ig->live->create();
            
            $broadcastId = $stream->getBroadcastId();
            
            $ig->live->start($broadcastId);
            
            $streamUploadUrl = $stream->getUploadUrl();
            
            $split = preg_split("[".$broadcastId."]", $streamUploadUrl);

            $GLOBALS [ "streamUrl" ] = $split [0];
            $GLOBALS [ "streamKey" ] = $broadcastId.$split[1];

            array_push ( self::$status, array ( "Stream"=> "start broadcast and Get server and key ") );
            
            self::update ( function ( &$stream ) {
                $stream [ "server" ] = $GLOBALS [ "streamUrl" ];
                $stream [ "key" ] = $GLOBALS [ "streamKey" ];
                $stream [ "status" ] = self::$status;

                return $stream;
            } );
        
            /*
            $broadcastProcess = $ffmpeg->runAsync(sprintf(
                '-rtbufsize 256M -re -y -i %s -acodec libmp3lame -ar 44100 -b:a 2500k -pix_fmt yuv420p -profile:v baseline -s 720x1280 -bufsize 4000k -vb 3000k -maxrate 8000k -minrate 2500k -b:v 4000k -deinterlace -vcodec libx264 -preset veryfast -g 30 -r 30 -vf "transpose='.$transpose.'" '.$filter.' -preset ultrafast -tune zerolatency -f flv %s',
                \Winbox\Args::escape($videoFilename),
                \Winbox\Args::escape($streamUploadUrl)
            ));
        
            $lastCommentTs = 0;
            $lastLikeTs = 0;

            do {
                
                $streamlog = json_decode ( Archive::read ( self::$streamSrc ), true );

                if ( !empty ( $streamlog ) && $streamlog [ "action" ] == "stop" ) {

                    array_push ( self::$status, array ( "Stream"=> "stop broadcast") );

                    self::update ( function ( &$stream ) {
                
                        $stream [ "play" ] = false;
                        $stream [ "connection" ] = false;
                        $stream [ "server" ] = "";
                        $stream [ "key" ] = "";
                        $stream [ "time" ] = 0;
                        $stream [ "status" ] = self::$status;
        
                        return $stream;
                    } );

                    break;
                }

                if ( !empty ( $streamlog ) && $streamlog [ "action" ] == "play" ) {

                    self::updateLog ( function ( &$stream ) {
                
                        $stream [ "play" ] = true;
                        $stream [ "connection" ] = true;
                        $stream [ "status" ] = self::$status;
        
                        return $stream;
                    } );

                }

  
                $commentsResponse = $ig->live->getComments($broadcastId, $lastCommentTs);
                $systemComments = $commentsResponse->getSystemComments();
                $comments = $commentsResponse->getComments();

                if (!empty($systemComments)) {
                    $lastCommentTs = $systemComments[0]->getCreatedAt();
                }

                if (!empty($comments) && $comments[0]->getCreatedAt() > $lastCommentTs) {
                    $lastCommentTs = $comments[0]->getCreatedAt();
                }

            
                // Get broadcast heartbeat and viewer count.
                $heartbeatResponse = $ig->live->getHeartbeatAndViewerCount($broadcastId);

        
                // Check to see if the livestream has been flagged for a policy violation.
                if ($heartbeatResponse->isIsPolicyViolation() && (int) $heartbeatResponse->getIsPolicyViolation() === 1) {
                    if (true) {
                        #$ig->live->getFinalViewerList($broadcastId);
                        #$ig->live->end($broadcastId, true);
                        #exit(0);
                    }
                    #$ig->live->resumeBroadcastAfterContentMatch($broadcastId);
                }
        
                $likeCountResponse = $ig->live->getLikeCount($broadcastId, $lastLikeTs);
                $lastLikeTs = $likeCountResponse->getLikeTs();


                $GLOBALS [ "comments" ] = $comments;
                $GLOBALS [ "lastCommentTs" ] = $lastCommentTs;
                $GLOBALS [ "likes" ] = $lastLikeTs;

                self::update ( function ( &$stream ) {

                    $stream [ "comments" ] = $GLOBALS [ "comments" ];
                    $stream [ "lastCommentTs" ] = $GLOBALS [ "lastCommentTs" ];
                    $stream [ "likes" ] = $GLOBALS [ "likes" ];
                    $stream [ "status" ] = self::$status;
                    
                    return $stream;
                } );
        
                $ig->live->getJoinRequestCounts($broadcastId);
        
                sleep(2);

            } while ($broadcastProcess->isRunning());


        
            // Get the final viewer list of the broadcast.
            // NOTE: You should only use this after the broadcast has stopped uploading.
            $ig->live->getFinalViewerList($broadcastId);
        
            // End the broadcast stream.
            // NOTE: Instagram will ALSO end the stream if your broadcasting software
            // itself sends a RTMP signal to end the stream. FFmpeg doesn't do that
            // (without patching), but OBS sends such a packet. So be aware of that.
            $ig->live->end($broadcastId);
        
            // Once the broadcast has ended, you can optionally add the finished
            // broadcast to your post-live feed (saved replay).
            $ig->live->addToPostLive($broadcastId);

            array_push ( self::$status, array ( "Stream"=> "Live finalizada com sucesso" ) );

            self::update ( function ( &$stream ) {
                
                $stream [ "play" ] = false;
                $stream [ "connection" ] = false;
                $stream [ "server" ] = "";
                $stream [ "key" ] = "";
                $stream [ "time" ] = 0;
                $stream [ "status" ] = self::$status;

                return $stream;
            } );
            */


        } catch ( \Exception $e ) { 
            array_push ( self::$status, array ( "Stream Error"=> $e->getMessage ( ) ) );
        }

        self::update ( function ( &$stream ) {
                
            $stream [ "play" ] = false;
            $stream [ "connection" ] = false;
            $stream [ "user" ] = "";
            $stream [ "password" ] = "";
            $stream [ "server" ] = "";
            $stream [ "key" ] = "";
            $stream [ "time" ] = 0;
            $stream [ "status" ] = self::$status;

            return $stream;
        } );

    }

    public static function updateLog ( $fn ) 
    {
        $stream = json_decode ( Archive::read ( self::$streamSrc ), true );

        $stream = $fn ( $stream ); 

        Archive::write ( self::$streamSrc, json_encode ( $stream ) );
    }

    public static function update ( $fn ) 
    {
        $stream = json_decode ( Archive::read ( self::$streamSrc ), true );

        $stream = $fn ( $stream ); 

        Archive::write ( self::$streamSrc, json_encode ( $stream ) );
    }
}