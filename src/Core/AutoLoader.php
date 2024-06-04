<?php

namespace VVerner\Core;

defined('ABSPATH') || exit('No direct script access allowed');

class AutoLoader
{
  const DS = DIRECTORY_SEPARATOR;
  const CS = '\\';

  private array $excludedFiles = [];

  public function except(array $paths): self
  {
    $this->excludedFiles = array_map(fn ($path): string => $this->normalizeSlashesForDirectorySeparator($path), $paths);
    return $this;
  }

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
    $path = VVERNER_TOOLBOX . self::DS . 'src' . self::DS . 'Adapter';
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

    $files = array_filter($dependencies, fn ($dependency): bool => is_file($path . self::DS . $dependency));
    $dependencies = array_diff($dependencies, $files);

    foreach ($files as $file) :
      $this->loadFile($path . self::DS . $file);
    endforeach;

    foreach ($dependencies as $dependency) :
      $this->load($path . self::DS . $dependency);
    endforeach;
  }

  private function loadFile(string $path): void
  {
    if (
      str_ends_with($path, '.php') &&
      !str_ends_with($path, 'AutoLoader.php') &&
      !in_array($path, $this->excludedFiles)
    ) :
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
      ['/', self::CS],
      [self::DS, self::DS],
      $path
    );
  }

  private function getDirectoryNamespace(string $path): string
  {
    return $this->namespace . str_replace(
      [$this->initialPath, '.php', self::DS],
      ['', '', self::CS],
      $path
    );
  }
}
