<?php

namespace VVerner\Core;

defined('ABSPATH') || exit('No direct script access allowed');

class AutoEnqueue
{
  private array $js = [];
  private array $css = [];
  const DS = DIRECTORY_SEPARATOR;


  public function __construct(private string $prefix = 'vverner', private string $initialPathURI)
  {
    global $VVerner;

    // $this->initialPath = $this->normalizeSlashesForDirectorySeparator($this->initialPath);

    // if (str_starts_with($this->initialPath, VVERNER_TOOLBOX)) :
    //   $this->loadAdapters();
    // endif;

    // $VVerner['autoloader'][$this->namespace] = $this->initialPath;
  }

  public function load(string $path = null): void
  {
    if (is_null($path)) :
      $path = $this->initialPathURI;
    endif;

    error_log(print_r($path, true));

    // if (is_file($path)) :
    //   $this->loadFile($path);
    //   return;
    // endif;

    $ignoredFiles = ['index.php', '..', '.'];
    $ignoredDir = ['imgs', 'image', 'images', 'icons', 'icon', 'fonts', 'font'];
    $dependencies = array_diff(scandir($path), $ignoredFiles, $ignoredDir);
    error_log(print_r($dependencies, true));

    $files = array_filter($dependencies, fn ($dependency): bool => is_file($path . self::DS . $dependency));

    error_log(print_r($files, true));
    $dependencies = array_diff($dependencies, $files);

    foreach ($files as $file) :
      $this->loadFile($path . self::DS . $file);
    endforeach;

    foreach ($dependencies as $dependency) :
      $this->load($path . self::DS . $dependency);
    endforeach;
  }

  private function enqueueFile(string $path): void
  {
    if (str_ends_with($path, '.css')) :
      $pieces = explode(self::DS,$path);
      wp_enqueue_style($this->prefix . '-' . '')
    endif;
  }
}
