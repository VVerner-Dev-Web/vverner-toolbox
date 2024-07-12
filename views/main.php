<?php defined('ABSPATH') || exit;

$tabs = [
  'welcome' => 'Bem vindo',
  'images'  => 'Imagens',
  'smtp'    => 'E-mails',
  'debug'   => 'Debug',
  'system'  => 'Sistema'
];

if (current_user_can('manage_options')) :
  $tabs['jumpstart'] = 'Jumpstart';
endif;

if (current_user_can('install_plugins')) :
  $tabs['plugins'] = 'Plugins';
endif;

?>

<h1 id="vverner-logo">
  <img src="<?php echo esc_url(vvernerToolboxAssetUrl('imgs/logo.png')) ?>" alt="VVerner">
</h1>

<div id="vverner-tabs">
  <ul class="navigator">
    <?php foreach ($tabs as $index => $tab) : ?>
      <li class="tab">
        <a class="<?php echo 'welcome' === $index ? 'active' : '' ?>" href="#tab-<?php echo esc_attr($index) ?>">
          <?php echo esc_attr($tab)  ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
  <div class="tabs-container">
    <?php foreach ($tabs as $index => $tab) : ?>
      <div id="tab-<?php echo esc_attr($index) ?>" class="tab-content <?php echo 'welcome' === $index ? 'active' : '' ?>">
        <?php require_once __DIR__ . DIRECTORY_SEPARATOR . 'tab/' . $index . '.php' ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>