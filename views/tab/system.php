<?php defined('ABSPATH') || exit;

global $wp_version, $VVerner, $wpdb;

$theme = wp_get_theme();
$parentTheme = $theme->parent();
?>

<h3 style="margin-bottom: 5px">Ambiente</h3>
<table class="striped widefat">
  <tbody>
    <tr>
      <th>
        <strong>Servidor</strong>
      </th>
      <td><?php echo esc_html(filter_input(INPUT_SERVER, 'SERVER_SOFTWARE')) ?></td>
    </tr>
    <tr>
      <th>
        <strong>PHP</strong>
      </th>
      <td><?php echo esc_html(phpversion()); ?> (<?php echo esc_html(php_sapi_name()) ?>)</td>
    </tr>
    <tr>
      <th>
        <strong>Banco de dados</strong>
      </th>
      <td><?php echo esc_html($wpdb->db_server_info()) ?></td>
    </tr>
    <tr>
      <th>
        <strong>Versão do WordPress</strong>
      </th>
      <td><?php echo esc_html($wp_version) ?></td>
    </tr>
  </tbody>
</table>

<h3 style="margin-bottom: 5px">Constantes</h3>
<table class="striped widefat">
  <tbody>
    <tr>
      <th>
        <strong>WP_DEBUG</strong>
      </th>
      <td><?php echo defined('WP_DEBUG') && WP_DEBUG ? 'Ativo' : 'Inativo' ?></td>
    </tr>
    <tr>
      <th>
        <strong>WP_DEBUG_LOG</strong>
      </th>
      <td><?php echo defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ? 'Ativo' : 'Inativo' ?></td>
    </tr>
    <tr>
      <th>
        <strong>WP_DEBUG_DISPLAY</strong>
      </th>
      <td><?php echo defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY ? 'Ativo' : 'Inativo' ?></td>
    </tr>
    <tr>
      <th>
        <strong>WP_CRON</strong>
      </th>
      <td><?php echo defined('DISABLE_WP_CRON') && DISABLE_WP_CRON ? 'Inativo' : 'Ativo' ?></td>
    </tr>
  </tbody>
</table>

<h3 style="margin-bottom: 5px">Dependências</h3>
<table class="striped widefat">
  <tbody>
    <tr>
      <th>Tema atual</th>
      <td><?php echo esc_html($theme->name) . ': ' . esc_html($theme->version) ?></td>
    </tr>
    <tr>
      <th>Tema pai</th>
      <td><?php echo $parentTheme ? esc_html($parentTheme->name) . ': ' . esc_html($parentTheme->version) : esc_html_e('Não possui', 'vverner') ?></td>
    </tr>
    <tr>
      <th>Versão do ACF</th>
      <td><?php echo defined('ACF_VERSION') ? esc_html(ACF_VERSION) : esc_html_e('Não possui', 'vverner') ?></td>
    </tr>
  </tbody>
</table>

<?php if (current_user_can('manage_options')) : ?>

  <br>
  <h3>Autoloaders</h3>

  <table class="striped widefat">
    <thead>
      <tr>
        <th>Namespace</th>
        <th>Source</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($VVerner['autoloader'] as $namespace => $source) : ?>
        <tr>
          <td>
            <strong><?php echo esc_html($namespace) ?></strong>
          </td>
          <td><?php echo esc_html($source) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <br>
  <h3>VJAX - Endpoints</h3>

  <table class="striped widefat">
    <thead>
      <tr>
        <th>Endpoint</th>
        <th>Callback</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($VVerner['vjax'] as $endpoint => $callback) : ?>
        <tr>
          <td>
            <?php echo esc_url(home_url('/?vjax=' . $endpoint)) ?>
          </td>
          <td><?php echo esc_html($callback) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <br>
  <h3>Hooks utilizados</h3>

  <table class="striped widefat">
    <thead>
      <tr>
        <th>Tipo</th>
        <th>Hook</th>
        <th>Callback</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($VVerner['hooks'] as $hook) : ?>
        <tr>
          <td>
            <?php echo esc_html($hook['type']) ?>
          </td>
          <td>
            <?php echo esc_html($hook['hook']) ?>
          </td>
          <td>
            <?php echo esc_html($hook['source']) ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

<?php endif; ?>