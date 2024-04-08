<?php

namespace VVerner\ACF;

defined('ABSPATH') || exit;

class ACF
{
  private string $jsonPath = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'acf-json';

  private string $cacheKey = 'vverner/acf/plugin-update';
  private string $slug = WP_PLUGIN_DIR . '/advanced-custom-fields-pro/acf.php';
  private string $version;


  private function __construct()
  {
  }

  public static function attach(): void
  {
    $cls = new self();

    $cls->load();
    $cls->createJsonDir();

    add_filter('site_transient_update_plugins', $cls->update(...));
    add_action('upgrader_process_complete', $cls->purge(...), 10, 2);

    add_filter('acf/settings/save_json', $cls->saveJson(...));
    add_filter('acf/settings/load_json', $cls->loadPath(...));
  }

  private function saveJson(): string
  {
    return $this->jsonPath;
  }

  private function loadPath(array $paths): array
  {
    $paths[] = $this->jsonPath;
    return $paths;
  }

  private function createJsonDir(): void
  {
    if (!is_dir($this->jsonPath)) :
      mkdir($this->jsonPath);
      file_put_contents($this->jsonPath . DIRECTORY_SEPARATOR . 'index.php', '');
    endif;
  }

  private function update($transient)
  {
    if (!$transient || empty($transient->checked)) {
      return $transient;
    }

    $remote = $this->request();

    if ($remote && version_compare($this->version, $remote->version, '<')) :
      $update = (object) [
        'slug'        => 'advanced-custom-fields-pro',
        'plugin'      => 'advanced-custom-fields-pro/acf.php',
        'new_version' => $remote->version,
        'package'     => $remote->download_url,
      ];

      $transient->response[$update->plugin] = $update;
    endif;

    return $transient;
  }

  private function purge($upgrader, array $options): void
  {
    if ('update' !== $options['action'] || 'plugin' !== $options['type']) {
      return;
    }
    delete_transient($this->cacheKey);
  }

  private function load(): void
  {
    if (!function_exists('get_plugin_data')) :
      require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    endif;

    $data = get_plugin_data($this->slug, false, false);

    $this->version = $data['Version'];
  }

  private function request()
  {
    $remote = get_transient($this->cacheKey);

    if (!$remote) :
      $remote = wp_remote_get('https://vverner.com/remote-updates/acf.json?key=' . uniqid());

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