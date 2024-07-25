<?php

namespace Application;

use GuzzleHttp\Client;

class Google {

    public $app;
    public $client;

    public function __construct($app){

        $this->client = new Client();
        $this->app = $app;
    }

    public function getImportHeader($url){

        $__res = $this->client->get($url, [
            'verify' => false,
            'allow_redirects' => false,
            'decode_content' => false,
            'http_errors' => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 Gecko/20100101 Firefox/128.0',
                'Accept'     => 'text/css; charset=utf-8',
            ]
        ]);

        if (( $body = $__res->getBody()) &&
            ( $content = $body->getContents())) {

            return $content;
        }
    }

    public function parse($content) {

        return preg_replace_callback("/{[^}]+}/", function( $face ) {

            preg_match_all("/^(.*):\s*(.*)\s*;$/m", $face[0], $ms, PREG_SET_ORDER);
            $fontname = preg_replace("/[^\w]+/", '', $ms[0][2].
                ucfirst($ms[1][2]).ucfirst($ms[2][2]).ucfirst($ms[3][2]));

            return preg_replace_callback("/(?<=url\()([^\)]+)(?=\))/",

                function( $match ) use ( $fontname ){

                    $realname = basename(parse_url($match[1], PHP_URL_PATH));
                    $fontname .= '.'.pathinfo($realname, PATHINFO_EXTENSION);

                    $__resfile = $this->client->get($match[1], [
                        'sink'   => $this->app->config['fonts'].$realname,
                        'verify' => false
                    ]);

                    //return $fontname;
                    return $realname;

            }, $face[0]);

        }, $content);
    }
}


?>
