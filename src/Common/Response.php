<?php

namespace Application\Common;

use Application;
use GuzzleHttp\Psr7\Response as HttpResponce;

abstract class Response {

    public $app;

    public function __construct( Application $app ){
        $this->app = $app;
    }

    public static function instance() {
        //Application::debug();

        $arguments = func_get_args();
        $methods   = array_shift($arguments);
        $app       = array_shift($arguments);
        $app->isMethod = is_string( $methods ?? 0 );

        $instance = new static( $app, ...$arguments );

        $instance->jsonLog('log.json', [ date('d-m-Y H:i:s'),
        $methods, $instance->post()?:$_REQUEST, $_FILES]);

        if ( $app->isMethod ) $instance->{$methods}(...$arguments);
    }

    public function path($name, $make = false) {

        $return = realpath( $this->app->config[$name] );
        if ( !file_exists( $return ) && $make )
        mkdir($return, 0777, true);

        return $return;
    }

    public function post($name = '') {
        return strlen( $name )  ?
        $this->app->post[$name] :
        $this->app->post;
    }

    public function json($data) {

        header("Content-Type: application/json");
        print json_encode( $data );
    }

    public function download($file, $name = '') {

        if ( is_file( $file )) {

            if (strlen( $name ) == 0) $name = basename($file);
            $disposition = $this->app->isFireFox() ? "*=UTF-8\'\'" : '=';
            $contentType = mime_content_type($file);

            header("Content-Type: ".$contentType);
            header("Content-Disposition: attachment; filename".
            $disposition.rawurlencode($name) );
            readfile($file);

        } else {

            header("Content-Type: text/plain");
            print $name. ' file not found'; return;
        }
    }

    protected function jsonLog($name, $data){

        file_put_contents( $this->app->config['logs'].'/'.$name,
        json_encode( $data ) . "\n", FILE_APPEND );
    }

}

?>
