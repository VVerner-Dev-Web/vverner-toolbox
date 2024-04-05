<?php

use VVerner\Controllers\Images;

$controller = new Images;

?>

<form class="vverner-configuration-form">
  <input type="hidden" name="vjax" value="vverner/admin/image-settings">
  <?php wp_nonce_field('vverner/admin/image-settings'); ?>

  <h3>Novos uploads</h3>

  <div class="input-container">
    <label for="imageMaxWidth">Largura máxima</label>
    <input type="number" name="maxWidth" id="imageMaxWidth" value="<?= $controller->maxWidth ?>" min="0" step="1" max="10000" required>
  </div>

  <div class="input-container">
    <label for="imageMaxHeight">Altura máxima</label>
    <input type="number" name="maxHeight" id="imageMaxHeight" value="<?= $controller->maxHeight ?>" min="0" step="1" max="10000" required>
  </div>

  <div class="input-container">
    <label for="imageQuality">Qualidade após envio</label>
    <input type="number" name="quality" id="imageQuality" value="<?= $controller->quality ?>" min="0" step="1" max="100" required>
  </div>

  <button type="submit">Salvar</button>
</form>