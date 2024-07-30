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
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="/css/fa.svgicon.css">
        <style>
            @import url("webfonts/content/import.css");
            body {
                margin: 0;
            }
            div {
            }
        </style>
        </head>
        <body>
            <div class="container">
                <main>
                    <div class="row row g-3">
                        <?php $this->form(); ?>
                    </div>
                </main>
            </div>
        </body>
        </html>
        <?php
    }

    public function temp(){
        ?><h3>...under construction</h3><?php
    }

    public function form(){
        ?>
        <p>save or get remote google fonts in de local</p>
        <form method="POST" action="/apply" >
            <div class="col-12">
                <label for="url" class="form-label">Google fonts url:</label>
                <input type="text" id="url" class="form-control" name="fontsUrl"
                       placeholder="https://fonts.googleapis.com/css2?family=Material+Symbols..."
                /><br>
            </div>
            <div class="col-12">
              <div class="form-check">
                <label class="form-check-label"><input name="action" type="radio"
                class="form-check-input" value="store" checked="" required="">Public</label>
              </div>
              <div class="form-check">
                <label class="form-check-label"><input name="action" type="radio"
                class="form-check-input" value="download" required="">Download</label>
              </div>
            </div>
            <div class="col-12">
                <label class="form-label">Download Order name:</label>
                <input type="text" name="download" class="form-control">
            </div>
            <br><br>
            <?php if ( isset( $this->file )) { ?>
            <div class="col-12">
                <a class="stretched-link" href="<?=$this->file;?>"
                download>Download: <?=$this->file;?></a>
            </div>
            <?php } ?>
            <div class="col-12">
                <button class="w-100 btn btn-primary btn-lg"
                type="submit">Speichern</button>
            </div>
        </form>
        <?php
    }

    private function host(){

        return 'https://' . $_SERVER['HTTP_HOST'];
    }

    public function addOrderName($src){

        $ordner = strlen($_POST['download'] ?? '')
                       ? $_POST['download'] . '/' : '';

        return $ordner . $src;
    }

    public function srcName($ordername, $realname){

        $src = $ordername . '/'.$realname;

        if ( $this->zip ) {

            $file = $this->app->config['fonts'].$src;
            $this->zip->addFile( $file, $this->addOrderName( $src ));

        } else return $this->host() .'/store/' . $src;

        return $src;
    }

    public function apply(){

        if ( isset( $_POST['fontsUrl'], $_POST['action'] )) {

            $google = new Google( $this );
            $__import = $google->getImportHeader($_POST['fontsUrl']);
            $filename = hash('xxh64', $_POST['fontsUrl']);

            if ( $_POST['action'] == 'download' ) {

                $this->zip = new \ZipArchive();
                $file = $this->app->config['download'].$filename.'.zip';

                if ( $this->zip->open( $file, \ZipArchive::CREATE) !== TRUE )
                throw new \Exception('no file');

                $content = $google->parse($__import);

                $this->zip->addFromString(
                $this->addOrderName('import.css'), $content);
                $this->zip->close();

            } else {

                $file = $this->app->config['download'].$filename.'.css';
                file_put_contents($file, $google->parse($__import));
            }

            $this->file = $this->host() . '/download/' . basename($file);

            $this->home();
        }
    }
}

?>
