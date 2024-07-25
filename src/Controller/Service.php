<?php

namespace Application\Controller;

use Application\Common\Response;
use Application\Google;

class Service extends Response {

    public function home(){
        $test = 0;
        ?>
        <html>
        <head>
        <title></title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="/css/fa.svgicon.css">
        <style>
            @import url("webfonts/content/import.css");
            body {
                text-align: center;
                margin: 0;
            }
            div {
            }
        </style>
        </head>
        <body>
            <div class="container">
                <main class="text-center">
                    <div class="row row g-3">
                        <?php $this->temp(); ?>
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
        <form>
            <div class="col-sm-6">
                <label for="url" class="form-label"></label>
                <input type="text" class="form-control" id="url">
            </div>
        </form>
        <?php
    }

    public function store($name){

        $url  = file_get_contents('php://input');
    }
}

?>
