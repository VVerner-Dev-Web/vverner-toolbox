<?php

namespace VVerner\Core;

defined('ABSPATH') || exit('No direct script access allowed');

class AutoLoader
{
  public function __construct(private string $namespace, private string $initialPath)
  {
    global $VVerner;

    $this->initialPath = $this->normalizeSlashesForDirectorySeparator($this->initialPath);

    if (str_starts_with($this->initialPath, VVERNER_TOOLBOX)) :
      $this->loadAdapters();
    endif;

    $VVerner['autoloader'][$this->namespace] = $this->initialPath;
  }

  private function loadAdapters(): void
  {
    $path = VVERNER_TOOLBOX . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Adapter';
    $this->load($path);
  }

  public function load(string $path = null): void
  {
    if (is_null($path)) :
      $path = $this->initialPath;
    endif;

    $path = $this->normalizeSlashesForDirectorySeparator($path);

    if (is_file($path)) :
      $this->loadFile($path);
      return;
    endif;

    $ignoredFiles = ['index.php', '..', '.'];
    $dependencies = array_diff(scandir($path), $ignoredFiles);

    $files = array_filter($dependencies, fn ($dependency): bool => is_file($path . DIRECTORY_SEPARATOR . $dependency));
    $dependencies = array_diff($dependencies, $files);

    foreach ($files as $file) :
      $this->loadFile($path . DIRECTORY_SEPARATOR . $file);
    endforeach;

    foreach ($dependencies as $dependency) :
      $this->load($path . DIRECTORY_SEPARATOR . $dependency);
    endforeach;
  }

  private function loadFile(string $path): void
  {
    if (str_ends_with($path, '.php') && !str_ends_with($path, 'AutoLoader.php')) :
      require_once $path;
      $this->attachClass($path);
    endif;
  }

  private function attachClass(string $path): void
  {
    $className = $this->getDirectoryNamespace($path);

    if (!class_exists($className) || !method_exists($className, 'attach') || str_contains($className, '\Adapter\\')) {
      return;
    }

    call_user_func([$className, 'attach']);
  }

  private function normalizeSlashesForDirectorySeparator(string $path): string
  {
    return str_replace(
      ['/', '\\'],
      [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR],
      $path
    );
  }

  private function getDirectoryNamespace(string $path): string
  {
    $className = str_replace(
      [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR],
      ["\\", "\\"],
      $path
    );

    $className = str_replace($this->initialPath, '', $className);
    $className = str_replace('.php', '', $className);
    return $this->namespace . $className;
  }
}
