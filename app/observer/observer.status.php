<?php

require_once ( "Observer.php" );
require_once ( "../archive/Archive.php" );

$data = Archive::read ( "../log/stream.log" );

Observer::Event ( 'connection', $data );