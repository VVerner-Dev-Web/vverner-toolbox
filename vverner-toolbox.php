<?php

/**
 * Plugin name: VVerner - Toolbox
 * Description: Este plugin é a caixa de ferramentas da equipe VVerner que otimiza e padroniza os projetos entregues pela empresa.
 * Author: VVerner
 * Author URI: https://vverner.com
 * Tested up to: 6.5.3
 * Requires PHP: 8.0
 * Requires at least: 6.4
 * Version: 1.0.0
 */

use VVerner\Core\AutoEnqueue;
use VVerner\Core\AutoLoader;

if (!defined('VVERNER_TOOLBOX_FILE')) :
  global $VVerner;

  define('VVERNER_TOOLBOX', __DIR__);
  define('VVERNER_TOOLBOX_FILE', __FILE__);
  define('VVERNER_TOOLBOX_VERSION', '1.0.0');

  $VVerner = [
    'support' => [
      'email'     => 'comercial@vverner.com',
      'whatsapp'  => '(54) 9 8449-6064',
      'site'      => 'https://vverner.com'
    ],
    'autoloader'  => [],
    'vjax'        => [],
  ];

  require_once VVERNER_TOOLBOX .  DIRECTORY_SEPARATOR . 'VVernerPluginBuilder.php';
  require_once VVERNER_TOOLBOX . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'AutoLoader.php';

  (new AutoLoader('VVerner', VVERNER_TOOLBOX . DIRECTORY_SEPARATOR . 'src'))->load();
  (new AutoEnqueue('vv-toolbox', get_pl() . 'assets'))->load();
endif;
