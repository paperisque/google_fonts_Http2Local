<?php error_reporting( E_ALL & ~E_NOTICE & ~E_STRICT & ~E_WARNING ); 

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;

require 'vendor/autoload.php';

try {

    (new class {

        public $client;
        public $fontdir;
        public $import;

        public function __construct(){

            $this->import = file_get_contents('php://input');
            $this->client = new Client();
            $this->fontdir = 'fonts/';
        }

        public function make() {

            $__res = $this->client->get($this->import, ['verify' => false]);

            if (($body = $__res->getBody()) && ($content = $body->getContents())) {

                foreach( glob($this->fontdir.'*.*') as $exist) unlink($exist);

                $content = preg_replace_callback("/{[^}]+}/", function( $face ) {

                    preg_match_all("/^(.*):\s*(.*)\s*;$/m", $face[0], $ms, PREG_SET_ORDER);
                    $fontname = preg_replace("/[^\w]+/", '', $ms[0][2].
                    ucfirst($ms[1][2]).ucfirst($ms[2][2]).ucfirst($ms[3][2]));

                    return preg_replace_callback("/(?<=url\()([^\)]+)(?=\))/", 

                        function( $match ) use ( $fontname ){

                            $realname = basename(parse_url($match[1], PHP_URL_PATH));
                            $fontname .= '.'.pathinfo($realname, PATHINFO_EXTENSION);

                            $__resfile = $this->client->get($match[1], [
                                'sink'   => $this->fontdir.$fontname,
                                'verify' => false
                            ]);

                            return $fontname;

                    }, $face[0]);

                }, $content);

                file_put_contents($this->fontdir.'fontface.css', $content);

                print sprintf("<pre>%s</pre>", $content);
            }       
        }

    })->make();


} catch ( \Throwable $e ) {

    print $e->getMessage();
}


?>
