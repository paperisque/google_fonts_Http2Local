<?php

$config = [

    'force'  => false,
    'logs'   => __DIR__ . '/logs',
    'fonts'  => __DIR__ . '/fonts/',
    'download' => __DIR__ . '/public/download/',

    'routes' => [

        [ 'url' => '/',  'class' => '\Controller\Service:home' ],
        [ 'url' => '/apply',  'class' => '\Controller\Service:apply', 'method' => 'POST' ],
    ],
];

return $config;

?>