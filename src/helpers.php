<?php defined('ABSPATH') || exit;

function vvernerToolboxInDev(): bool
{
  return 'DEV' === vvernerToolboxEnv();
}

function vvernerToolboxEnv(): string
{
  $url        = home_url();
  $knownPaths = ['.dev', '-sandbox.com', 'kinsta.cloud'];

  foreach ($knownPaths as $path) :
    if (strpos((string) $url, $path)) :
      return 'DEV';
    endif;
  endforeach;

  return 'PRD';
}

function vvernerToolboxAssetUrl(string $filename): string
{
  return  plugin_dir_url(VVERNER_TOOLBOX_FILE) . 'assets/' . $filename;
}
