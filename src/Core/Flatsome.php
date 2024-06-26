<?php

namespace VVerner\Core;

defined('ABSPATH') || exit;

class Flatsome
{
  private string $cacheKey = 'vverner/flatsome/theme-update';

  private function __construct()
  {
  }

  public static function attach(): void
  {
    $cls = new self();

    add_filter('site_transient_update_themes', $cls->update(...));
  }
  private function update($transient)
  {
    $flatsome   = wp_get_theme('flatsome');

    if (!$flatsome || !$flatsome->exists() || !$transient) :
      return $transient;
    endif;

    $version    = $flatsome->get('Version');
    $remote     = $this->request();

    if (!$remote) :
      return $transient;
    endif;

    $data = [
      'theme'       => $flatsome->get_stylesheet(),
      'url'         => $remote->details_url,
      'new_version' => $remote->version,
      'package'     => $remote->download_url,
    ];

    $remote && version_compare($version, $remote->version, '<') ?
      $transient->response[$flatsome->get_stylesheet()] = $data :
      $transient->no_update[$flatsome->get_stylesheet()] = $data;

    return $transient;
  }

  private function request()
  {
    $remote = get_transient($this->cacheKey);

    if (!$remote) :
      $remote = wp_remote_get('https://vverner.com/remote-updates/flatsome.json?key=' . uniqid());

      if (
        is_wp_error($remote)
        || 200 !== wp_remote_retrieve_response_code($remote)
        || empty(wp_remote_retrieve_body($remote))
      ) {
        return false;
      }

      $remote = json_decode((string) wp_remote_retrieve_body($remote));

      set_transient($this->cacheKey, $remote, DAY_IN_SECONDS);
    endif;

    return $remote;
  }
}
