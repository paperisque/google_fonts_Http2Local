<?php

include_once( __DIR__. '/../src/Common/bootstrap.php');
$config = require( __DIR__ .'/../config.php' );
dispatch( new Application( $config ) );

?>
