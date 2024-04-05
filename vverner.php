<?php

global $VVerner;

use VVerner\Core\AutoLoader;

define('VVERNER_TOOLBOX', __DIR__);
define('VVERNER_TOOLBOX_VERSION', '1.0.0');

$VVerner = [
  'support' => [
    'email'     => 'comercial@vverner.com',
    'whatsapp'  => '(54) 9 8449-6064',
    'site'      => 'https://vverner.com'
  ]
];

require_once VVERNER_TOOLBOX . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'AutoLoader.php';

(new AutoLoader('VVerner', VVERNER_TOOLBOX . DIRECTORY_SEPARATOR . 'src'))->load();
