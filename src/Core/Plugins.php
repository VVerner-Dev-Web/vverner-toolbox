<?php

namespace VVerner\Core;

use WP_REST_Request;

defined('ABSPATH') || exit('No direct script access allowed');

class Plugins
{
  private static $recommendPlugins = [
    'contact-form-7'          => 'Contact Form 7',
    'contact-form-cfdb7'      => 'Contact Form 7 Database Addon – CFDB7',
    'akismet'                 => 'Akismet',
    'cookie-law-info'         => 'CookieYes',
    'advanced-cron-manager'   => 'Advanced Cron Manager – debug & control',
    'updraftplus'             => 'UpdraftPlus: WordPress Backup & Migration Plugin',
    'wp-sweep'                => 'WP-Sweep',
    'rewrite-rules-inspector' => 'Rewrite Rules Inspector',
    'woocommerce'             => 'WooCommerce',
    'full-customer'           => 'FULL.',
    'advanced-custom-fields'  => 'Advanced Custom Fields (ACF)',
    'woocommerce-correios'    => 'Correios for WooCommerce',
    'woocommerce-extra-checkout-fields-for-brazil' => 'Brazilian Market on WooCommerce',
  ];

  public static function recommendPlugins(): array
  {
    asort(self::$recommendPlugins);
    return self::$recommendPlugins;
  }

  public static function install(string $slug): void
  {
    global $wp_filesystem;
    if (!is_a($wp_filesystem, 'WP_Filesystem_Base')) :
      include_once(ABSPATH . 'wp-admin/includes/file.php');
      wp_filesystem(request_filesystem_credentials(site_url()));
    endif;

    $installedPlugins = $wp_filesystem->dirlist(WP_PLUGIN_DIR);
    $activatedPlugins = get_option('active_plugins');

    $alreadyInstalled = array_key_exists($slug, $installedPlugins);
    $alreadyActive    = array_filter($activatedPlugins, fn ($plugin) => strpos($plugin, $slug) !== false) ? true : false;

    if ($alreadyActive) :
      return;
    endif;

    if ($alreadyInstalled) :
      $wp_filesystem->delete(WP_CONTENT_DIR . '/plugins/' . $slug, true, 'd');
    endif;

    $request = new WP_REST_Request('POST', '/wp/v2/plugins');
    $request->set_param('slug', $slug);
    $request->set_param('status', 'active');

    rest_do_request($request);
  }
}
