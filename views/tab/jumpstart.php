<?php defined('ABSPATH') || exit;

use VVerner\Core\JumpStart;

?>
<form class="vverner-configuration-form">
  <input type="hidden" name="vjax" value="vverner/admin/jumpstart">
  <?php wp_nonce_field('vverner/admin/jumpstart'); ?>

  <h3>Jumpstart</h3>

  <?php foreach (JumpStart::availableJobs() as $index => $job) : ?>
    <div style="margin-bottom: 5px">
      <label for="jumpstart-<?php echo esc_attr($index) ?>">
        <input type="checkbox" name="jobs[]" id="jumpstart-<?php echo esc_attr($index) ?>" value="<?php echo esc_attr($index) ?>">
        <span>
          <?php echo esc_attr($job) ?>
        </span>
      </label>
    </div>
  <?php endforeach; ?>

  <br>

  <button type="submit">Começar</button>
</form>