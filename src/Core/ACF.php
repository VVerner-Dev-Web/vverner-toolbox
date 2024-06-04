<?php

namespace VVerner\ACF;

defined('ABSPATH') || exit;

class ACF
{
  private string $jsonPath = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'acf-json';

  private function __construct()
  {
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
      vvernerToolboxFileSystem()->mkdir($this->jsonPath);
      vvernerToolboxFileSystem()->put_contents($this->jsonPath . DIRECTORY_SEPARATOR . 'index.php', '');
    endif;
  }
}
