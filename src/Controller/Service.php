<?php

namespace Application\Controller;

use Application\Common\Response;
use Application\Google;

class Service extends Response {

    public $zip;

    public function home(){
        $test = 0;
        ?>

        <html>
        <head>
        <title></title>
        <meta http-equiv="pragma" content="no-cache">
        <meta http-equiv="cache-control" content="no-cache">
        <script src="https://unpkg.com/petite-vue" defer init></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="/css/fa.svgicon.css">
        <link rel="stylesheet" href="/css/index.css">
        </head>
        <body>
            <div
                id="container"
                class="container load"
                v-scope="petite"
                @vue:mounted="mounted">
                <main>
                    <div class="row row g-3">
                        <?php $this->form(); ?>
                    </div>
                </main>
            </div>
        <script src="index.js"></script>
        </body>
        </html>
        <?php
    }

    public function temp(){
        ?><h3>...under construction</h3><?php
    }

    public function form(){
        ?>
        <p class="text-uppercase text-center">google fonts to local</p>
            <div class="col-12">
                <label for="url" class="form-label">Google fonts url:</label>
                <input type="text" id="url" class="form-control" v-model="url"
                       placeholder="https://fonts.googleapis.com/css2?family=Material+Symbols..."
                /><br>
            </div>
            <div class="col-12">
              <div class="form-check">
                <label class="form-check-label"><input name="action" type="radio"
                class="form-check-input" value="store" v-model="type">Public</label>
              </div>
              <div class="form-check">
                <label class="form-check-label"><input name="action" type="radio"
                class="form-check-input" value="download" v-model="type">Download</label>
              </div>
            </div>
            <div class="col-12" v-if="type=='download'">
                <label class="form-label">Ordner name:</label>
                <input type="text" v-model="folder" class="form-control">
            </div>
            <div class="col-12 download-box" v-show="response.length">
                <a class="stretched-link"
                   :href="response"
                download>Download: {{ response }}</a>
            </div>
            <div class="col-12">
                <button class="w-100 btn btn-primary btn-lg position-relative"
                @click="save"><span>Speichern</span>
                <img v-show="sandbox" class="sandbox"
                     src="css/sand.svg">
                </button>
            </div>
        <?php
    }

    private function host(){

        return 'https://' . $_SERVER['HTTP_HOST'];
    }

    public function addFolderName($src){

        $ordner = strlen($_POST['folder'] ?? '')
                       ? $_POST['folder'] . '/' : '';

        return $ordner . $src;
    }

    public function srcName($ordername, $realname){

        $src = $ordername . '/'.$realname;

        if ( $this->zip ) {

            $file = $this->app->config['fonts'] . $src;
            $this->zip->addFile( $file, $this->addFolderName($realname) );
            $src = $realname;

        } else return $this->host() .'/store/' . $src;

        return $src;
    }

    public function apply(){

        try {

            $return = [ 'error' => $_POST ];

            if ( strlen( $_POST['url'] ?? '') && strlen( $_POST['type'] ?? ''  ) ) {

                $google = new Google( $this );
                $__import = "/* original: {$_POST['url']} */\n\r" .
                $google->getImportHeader($_POST['url']);

                $filename = hash('xxh64', $_POST['url']);

                if ( $_POST['type'] == 'download' ) {

                    $this->zip = new \ZipArchive();
                    $file = $this->app->config['download'].$filename.'.zip';
                    if ( is_file( $file ) ) unlink( $file );

                    if ( $this->zip->open( $file, \ZipArchive::CREATE) !== TRUE )
                    throw new \Exception('no file');

                    $content = $google->parse($__import);

                    $this->zip->addFromString($this->addFolderName('import.css'), $content);
                    $this->zip->close();

                } else {

                    $file = $this->app->config['download'].$filename.'.css';
                    file_put_contents($file, $google->parse($__import));
                }

                $return = [ 'success' => 1, 'file' => $this->host() .
                            '/download/' . basename($file) ];
            }

        } catch (\Exception $e ){

            $return = ['error' => $e->getMessage()];

        } finally {

            $this->json($return);
        }


    }
}

?>
