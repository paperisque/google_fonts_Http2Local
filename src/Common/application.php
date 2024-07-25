<?php

use FastRoute\RouteCollector;

class Application {

    public $post;
    public $config;
    public $userAgent;
    public $isMethod = false;

    public function __construct( $config ){

        $this->config = $config;
        $this->post = json_decode(file_get_contents("php://input"),true);
        $this->userAgent = strtolower($_SERVER['HTTP_USER_AGENT']??'');
    }

    public function isChrome() {

        return preg_match("/chrome/", $this->userAgent);
    }

    public function isFireFox() {

        return preg_match("/firefox/", $this->userAgent);
    }

    public function route(RouteCollector $r) {

        foreach( $this->config['routes'] as $route ) {

            if ( count( $invoke = preg_split("/\:+/", $route['class'] ) ) )

            $r->addRoute( $route['method']??'GET', $route['url'],

                function() use ( $route, $invoke ){

                $this->invoke( $invoke[0], $invoke[1] ?? null, array_merge(
                $route['arguments']??[], func_get_args() ));
            });
        }

        return $r;
    }

    public function invoke( $class,

        $method = null, $arguments = [] ) {

        array_unshift($arguments, $this);
        array_unshift($arguments, $method);

        $classexists = class_exists($__class = 'Application'.$class);
        call_user_func_array([$__class, 'instance'], $arguments );

        return $this;
    }

}

?>
