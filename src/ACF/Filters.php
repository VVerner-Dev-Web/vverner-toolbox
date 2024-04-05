<?php

namespace VVerner\ACF;

defined('ABSPATH') || exit('No direct script access allowed');

class Filters
{
  private string $dir;

  private function __construct()
  {
    $this->dir = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'acf-json';
  }

  public static function attach(): void
  {
    $cls = new self();

    $cls->createJsonDir();

    add_filter('acf/settings/save_json', $cls->saveJson(...));
    add_filter('acf/settings/load_json', $cls->loadPath(...));
  }

  private function saveJson(): string
  {
    return $this->dir;
  }

  private function loadPath(array $paths): array
  {
    $paths[] = $this->dir;
    return $paths;
  }

  private function createJsonDir(): void
  {
    if (!is_dir($this->dir)) :
      mkdir($this->dir);
      file_put_contents($this->dir . DIRECTORY_SEPARATOR . 'index.php', '');
    endif;
  }
}
