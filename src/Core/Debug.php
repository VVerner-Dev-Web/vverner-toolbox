<?php

namespace VVerner\Core;

class Debug
{
  public function __construct()
  {
  }

  public function getConst(string $const)
  {
    $const = trim(strtoupper($const));
    return defined($const) ? constant($const) : null;
  }

  public function setConstAsTrue(string $const): void
  {
    $const = trim(strtoupper($const));
    if ($this->getConst($const)) :
      return;
    endif;

    $this->switchConstValue($const, 'true', 'false');
  }

  public function setConstAsFalse(string $const): void
  {
    $const = trim(strtoupper($const));
    if (!$this->getConst($const)) :
      return;
    endif;

    $this->switchConstValue($const, 'false', 'true');
  }

  public function getCurrentLogContents(): string
  {
    $limitSize = 500000;
    $filename  = $this->getConst('WP_DEBUG_LOG') && is_readable($this->getConst('WP_DEBUG_LOG')) ? $this->getConst('WP_DEBUG_LOG') : WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'debug.log';
    $validSize = vvernerToolboxFileSystem()->exists($filename) && filesize($filename) < $limitSize;
    $content   = $validSize ? vvernerToolboxFileSystem()->get_contents($filename) : 'Conteúdo indisponível ou muito grande para ser carregado no navegador.';

    return $content;
  }

  public function clearLogs(): void
  {
    $filename  = $this->getConst('WP_DEBUG_LOG') && is_readable($this->getConst('WP_DEBUG_LOG')) ? $this->getConst('WP_DEBUG_LOG') : WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'debug.log';
    if (vvernerToolboxFileSystem()->exists($filename)) :
      vvernerToolboxFileSystem()->put_contents($filename, '');
    endif;
  }

  private function switchConstValue(string $constName, string $switchTo, string $switchFrom): void
  {
    $code = "define('$constName', $switchTo)";

    $content = $this->getWpConfigContent();
    $content = str_replace(
      ['define("' . $constName . '", ' . $switchFrom . ')', "define('$constName', $switchFrom)"],
      [$code, $code],
      $content
    );

    if (strpos($content, $code) === false) :
      $content = preg_replace("/^([\r\n\t ]*)(\<\?)(php)?/i", "<?php " . PHP_EOL . "$code; " . PHP_EOL, $content);
    endif;

    $this->setWpConfigContent($content);
  }

  private function getWpConfigContent(): string
  {
    $filename = ABSPATH . 'wp-config.php';
    return vvernerToolboxFileSystem()->is_writable($filename) ? vvernerToolboxFileSystem()->get_contents($filename) : '';
  }

  private function setWpConfigContent(string $content): bool
  {
    $done = false;
    $filename = ABSPATH . 'wp-config.php';

    if (vvernerToolboxFileSystem()->is_writable($filename)) :
      $done = vvernerToolboxFileSystem()->put_contents($filename, $content);
    endif;

    return (bool) $done;
  }
}
