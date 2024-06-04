<?php defined('ABSPATH') || exit;

class VernerPluginBuilder
{
  private function __construct()
  {
  }

  public static function build(): void
  {
    register_activation_hook(VVERNER_TOOLBOX_FILE, [__CLASS__, 'activation']);
    register_deactivation_hook(VVERNER_TOOLBOX_FILE, [__CLASS__, 'deactivation']);
    register_uninstall_hook(VVERNER_TOOLBOX_FILE, [__CLASS__, 'uninstall']);
  }

  public static function activation(): void
  {
    self::deletePreviousLoader();

    if (!vvernerToolboxFileSystem()->exists(self::filename())) :
      vvernerToolboxFileSystem()->put_contents(self::filename(), self::fileContent());
    endif;
  }

  public static function deactivation(): void
  {
    self::deletePreviousLoader();
  }

  public static function uninstall(): void
  {
    self::deletePreviousLoader();
  }

  private static function filename(): string
  {
    return WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'mu-plugins' . DIRECTORY_SEPARATOR . 'mu-vverner.php';
  }

  private static function deletePreviousLoader(): void
  {
    if (vvernerToolboxFileSystem()->exists(self::filename())) :
      wp_delete_file(self::filename());
    endif;
  }


  private static function fileContent(): string
  {
    return '<?php ' . PHP_EOL . vvernerToolboxFileSystem()->get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'mu-vverner.txt');
  }
}

VernerPluginBuilder::build();
