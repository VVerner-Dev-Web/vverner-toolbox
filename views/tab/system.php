<?php
global $wp_version, $VVerner;

$db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$dbVersion = $db ? mysqli_get_server_info($db) : '';
$dbVersion = explode(':', $dbVersion)[0];

$theme = wp_get_theme();
$parentTheme = $theme->parent();
?>


<h3 style="margin-bottom: 5px">Ambiente</h3>
<table>
  <tbody>
    <tr>
      <th>Servidor</th>
      <td><?= filter_input(INPUT_SERVER, 'SERVER_SOFTWARE') ?></td>
    </tr>
    <tr>
      <th>PHP</th>
      <td><?= phpversion(); ?> (<?= php_sapi_name() ?>)</td>
    </tr>
    <tr>
      <th>Banco de dados</th>
      <td><?= $dbVersion ? $dbVersion : 'Não reconhecido' ?></td>
    </tr>
    <tr>
      <th>Versão do WordPress</th>
      <td><?= $wp_version ?></td>
    </tr>
  </tbody>
</table>

<h3 style="margin-bottom: 5px">Constantes</h3>
<table>
  <tbody>
    <tr>
      <th>WP_DEBUG</th>
      <td><?= defined('WP_DEBUG') && WP_DEBUG ? 'Ativo' : 'Inativo' ?></td>
    </tr>
    <tr>
      <th>WP_DEBUG_LOG</th>
      <td><?= defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ? 'Ativo' : 'Inativo' ?></td>
    </tr>
    <tr>
      <th>WP_DEBUG_DISPLAY</th>
      <td><?= defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY ? 'Ativo' : 'Inativo' ?></td>
    </tr>
    <tr>
      <th>WP_CRON</th>
      <td><?= defined('DISABLE_WP_CRON') && DISABLE_WP_CRON ? 'Inativo' : 'Ativo' ?></td>
    </tr>
  </tbody>
</table>

<h3 style="margin-bottom: 5px">Dependências</h3>
<table>
  <tbody>
    <tr>
      <th>Tema atual</th>
      <td><?= $theme->name . ': ' . $theme->version ?></td>
    </tr>
    <tr>
      <th>Tema pai</th>
      <td><?= $parentTheme ? $parentTheme->name . ': ' . $parentTheme->version : 'Não possui' ?></td>
    </tr>
    <tr>
      <th>Versão do ACF</th>
      <td><?= defined('ACF_VERSION') ? ACF_VERSION : 'Não instalado' ?></td>
    </tr>
  </tbody>
</table>

<?php if (isVVernerUser()) : ?>

  <br>
  <h3>Autoloaders</h3>

  <table>
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
            <strong><?= $namespace ?></strong>
          </td>
          <td><?= $source ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <br>
  <h3>VJAX - Endpoints</h3>

  <table>
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
            <?= home_url('/?vjax=' . $endpoint) ?>
          </td>
          <td><?= $callback ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

<?php endif; ?>