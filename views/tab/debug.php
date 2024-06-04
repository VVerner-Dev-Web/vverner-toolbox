<?php

use VVerner\Core\Debug;

$debug = new Debug();
?>

<form class="vverner-configuration-form">
  <input type="hidden" name="vjax" value="vverner/admin/debug">
  <?php wp_nonce_field('vverner/admin/debug'); ?>
  <h3>Informações de Debug</h3>
  <p>Configurações referentes ao modo de testes do <a href="https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/" target="_blank" rel="noopener noreferrer">WordPress</a></p>

  <div class="input-container">
    <label for="wp_debug">WP Debug &dash; Geral</label>
    <select name="wp_debug" id="wp_debug">
      <option value="0">Desativado</option>
      <option value="1" <?php selected($debug->getConst('WP_DEBUG')) ?>>Ativado</option>
    </select>
  </div>

  <div class="input-container">
    <label for="wp_debug_log">WP Debug &dash; Log</label>
    <select name="wp_debug_log" id="wp_debug_log">
      <option value="0">Desativado</option>
      <option value="1" <?php selected($debug->getConst('WP_DEBUG_LOG')) ?>>Ativado</option>
    </select>
  </div>

  <div class="input-container">
    <label for="wp_debug_display">WP Debug &dash; Display</label>
    <select name="wp_debug_display" id="wp_debug_display">
      <option value="0">Desativado</option>
      <option value="1" <?php selected($debug->getConst('WP_DEBUG_DISPLAY')) ?>>Ativado</option>
    </select>
  </div>

  <br>

  <button type="submit">Atualizar</button>
</form>

<br>
<br>
<br>

<?php if ($debug->getConst('WP_DEBUG_LOG')) : ?>
  <h3>Log de erros atual</h3>

  <div id="debug-timeout"></div>
  <pre id="vverner-debug-logs"><?php esc_html($debug->getCurrentLogContents()) ?></pre>

  <a id="clear-logs" style="color: red">Limpar arquivo</a>
<?php endif; ?>