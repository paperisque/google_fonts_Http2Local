<?php

$config = [

    'force'  => false,
    'logs'   => __DIR__ . '/logs',
    'fonts'  => __DIR__ . '/fonts/',

    'routes' => [

        [ 'url' => '/',  'class' => '\Controller\Service:home' ],
        [ 'url' => '/download',  'class' => '\Controller\Service:download' ],
        [ 'url' => '/cdn',  'class' => '\Controller\Service:cdn' ],
    ],
];

return $config;

?>