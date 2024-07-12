<?php defined('ABSPATH') || exit;

use VVerner\Core\Plugins;

?>
<form class="vverner-configuration-form">
  <input type="hidden" name="vjax" value="vverner/admin/plugins">
  <?php wp_nonce_field('vverner/admin/plugins'); ?>

  <h3>Instalador de plugins</h3>

  <?php foreach (Plugins::recommendPlugins() as $slug => $name) : ?>
    <div style="margin-bottom: 5px">
      <label for="plugins-<?php echo esc_attr($slug) ?>">
        <input type="checkbox" name="plugins[]" id="plugins-<?php echo esc_attr($slug) ?>" value="<?php echo esc_attr($slug) ?>">
        <span>
          <strong><?php echo esc_attr($name) ?></strong> &dash; <?php echo is_dir(WP_CONTENT_DIR . '/plugins/' . $slug) ? 'Instalado' : 'Não instalado' ?>
        </span>
      </label>
    </div>
  <?php endforeach; ?>

  <br>

  <button type="submit">Instalar</button>
</form>