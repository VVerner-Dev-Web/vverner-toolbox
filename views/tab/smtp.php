<?php

use VVerner\Controllers\Emails;

$controller = new Emails;
?>

<form class="vverner-configuration-form">
  <input type="hidden" name="vjax" value="vverner/admin/email-settings">
  <?php wp_nonce_field('vverner/admin/email-settings'); ?>

  <h3>Ambiente</h3>

  <div class="input-container">
    <label for="emailDeliveryMode">Configuração do disparo de emails</label>
    <select name="deliveryMode" id="emailDeliveryMode">
      <option value="wordpress" <?php selected('wordpress', $controller->deliveryMode) ?>>Padrão do WordPress - Não modificar</option>
      <option value="smtp" <?php selected('smtp', $controller->deliveryMode) ?>>Configurações SMTP - VVerner</option>
      <option value="none" <?php selected('none', $controller->deliveryMode) ?>>Bloquear envios de email</option>
    </select>
  </div>

  <div class="input-container">
    <label for="emailLogsEnabled">Logs</label>
    <select name="logsEnabled" id="emailLogsEnabled">
      <option value="0" <?php selected(!$controller->logsEnabled) ?>>Desativados</option>
      <option value="1" <?php selected($controller->logsEnabled) ?>>Ativados</option>
    </select>
  </div>

  <h3>Remetente</h3>

  <div class="input-container">
    <label for="emailFromName">Nome do remetente</label>
    <input type="text" name="fromName" id="emailFromName" value="<?= $controller->fromName ?>">
  </div>

  <div class="input-container">
    <label for="emailFromEmail">E-mail do remetente</label>
    <input type="email" name="fromEmail" id="emailFromEmail" value="<?= $controller->fromEmail ?>">
  </div>

  <h3>SMTP</h3>

  <div class="input-container">
    <label for="emailHost">Servidor SMTP</label>
    <input type="text" name="host" id="emailHost" value="<?= $controller->host ?>">
  </div>

  <div class="input-container">
    <label for="emailPort">Porta SMTP</label>
    <input type="number" name="port" id="emailPort" value="<?= $controller->port ?>" min="0" step="1">
  </div>

  <div class="input-container">
    <label for="emailSecurity">Criptografia</label>
    <select name="security" id="emailSecurity">
      <option value="none" <?php selected('none', $controller->security) ?>>Nenhuma</option>
      <option value="ssl" <?php selected('ssl', $controller->security) ?>>SSL</option>
      <option value="tls" <?php selected('tls', $controller->security) ?>>TLS</option>
    </select>
  </div>

  <div class="input-container">
    <label for="emailUser">Usuário</label>
    <input type="email" name="user" id="emailUser" value="<?= $controller->user ?>">
  </div>

  <div class="input-container">
    <label for="emailPassword">Senha</label>
    <input type="password" name="password" id="emailPassword" value="<?= $controller->password ?>">
  </div>

  <button type="submit">Salvar</button>
</form>

<form class="vverner-configuration-form">
  <input type="hidden" name="vjax" value="vverner/admin/email-test">
  <?php wp_nonce_field('vverner/admin/email-test'); ?>

  <h3>Teste de envio</h3>

  <div class="input-container">
    <label for="emailTestDestination">Email de teste</label>
    <input type="email" name="to" id="emailTestDestination" required>
  </div>

  <button type="submit">Enviar</button>
</form>