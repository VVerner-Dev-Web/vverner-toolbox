<?php

namespace VVerner\WordPress;

defined('ABSPATH') || exit;

class Admin
{
  private function __construct()
  {
  }

  public static function attach(): void
  {
    $cls = new self();

    add_action('admin_menu', $cls->addPages(...));
    add_action('admin_enqueue_scripts', $cls->enqueueScripts(...));

    add_filter('admin_footer_text', $cls->footerText(...));
  }

  private function footerText(): string
  {
    global $VVerner;
    return 'A <a href="' . $VVerner['support']['site'] . '" target="_blank" rel="noopener">VVerner</a> orgulhosamente trabalhou neste projeto <a href="https://wordpress.org/" target="_blank" rel="noopener">WordPress</a>.';
  }

  private function addPages(): void
  {
    add_menu_page(
      'VVerner',
      'VVerner',
      'manage_options',
      'vverner-main',
      $this->loadView(...),
      vvernerToolboxAssetUrl('imgs/icon.png'),
      5
    );
  }

  private function enqueueScripts(): void
  {
    wp_enqueue_style('vverner-admin', vvernerToolboxAssetUrl('css/admin.css'), [], VVERNER_TOOLBOX_VERSION);
    wp_enqueue_script('vverner-admin', vvernerToolboxAssetUrl('js/admin.js'), ['jquery'], VVERNER_TOOLBOX_VERSION, true);
  }

  private function loadView(): void
  {
    $view = filter_input(INPUT_GET, 'page') ?? '';
    $view = str_replace('vverner-', '', $view);
    $filename = VVERNER_TOOLBOX . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $view . '.php';

    if (vvernerToolboxFileSystem()->exists($filename)) :
      echo '<div class="wrap vverner-page">';
      require $filename;
      echo '</div>';
    endif;
  }
}
