<?php
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

?>

<h1 id="vverner-logo">
  <img src="<?= vvernerToolboxAssetUrl('imgs/logo.png') ?>" alt="VVerner">
</h1>

<div id="vverner-tabs">
  <ul class="navigator">
    <?php foreach ($tabs as $index => $tab) : ?>
      <li class="tab">
        <a class="<?= 'welcome' === $index ? 'active' : '' ?>" href="#tab-<?= $index ?>"><?= $tab ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
  <div class="tabs-container">
    <?php foreach ($tabs as $index => $tab) : ?>
      <div id="tab-<?= $index ?>" class="tab-content <?= 'welcome' === $index ? 'active' : '' ?>">
        <?php require_once __DIR__ . DIRECTORY_SEPARATOR . 'tab/' . $index . '.php' ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>