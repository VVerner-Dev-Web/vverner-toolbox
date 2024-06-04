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

function vvernerToolboxFileSystem(): WP_Filesystem_Base
{
  global $wp_filesystem;

  if (!$wp_filesystem instanceof WP_Filesystem_Base) :
    include_once(ABSPATH . 'wp-admin/includes/file.php');
    wp_filesystem(request_filesystem_credentials(site_url()));
  endif;

  return $wp_filesystem;
}
