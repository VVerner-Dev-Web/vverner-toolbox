<?php

namespace VVerner\API;

use VVerner\Adapter\Ajax;
use VVerner\Controllers\Emails;
use VVerner\Controllers\Images;
use VVerner\Core\Debug;
use VVerner\Core\JumpStart;
use VVerner\Core\Plugins;

class Admin extends Ajax
{
  public function imageSettings(): void
  {
    $this->validateNonce(__METHOD__);
    $this->validateCapability('manage_options');

    $controller = new Images;

    $controller->maxHeight = $this->getParam('maxHeight', FILTER_VALIDATE_INT);
    $controller->maxWidth  = $this->getParam('maxWidth', FILTER_VALIDATE_INT);
    $controller->quality   = $this->getParam('quality', FILTER_VALIDATE_INT);

    $controller->save();

    $this->response(['success' => true]);
  }

  public function emailSettings(): void
  {
    $this->validateNonce(__METHOD__);
    $this->validateCapability('manage_options');

    $controller = new Emails;

    $controller->deliveryMode = $this->getParam('deliveryMode');
    $controller->logsEnabled = $this->getParam('logsEnabled', FILTER_VALIDATE_BOOL);

    $controller->fromName     = $this->getParam('fromName');
    $controller->fromEmail    = $this->getParam('fromEmail', FILTER_VALIDATE_EMAIL);

    $controller->host         = $this->getParam('host');
    $controller->port         = $this->getParam('port');
    $controller->security     = $this->getParam('security');
    $controller->user         = $this->getParam('user');
    $controller->password     = $this->getParam('password');

    $controller->save();

    $this->response(['success' => true]);
  }

  public function emailTest(): void
  {
    $this->validateNonce(__METHOD__);
    $this->validateCapability('manage_options');

    wp_mail(
      $this->getParam('to', FILTER_VALIDATE_EMAIL),
      'VVerner - envio de teste',
      'Apenas uma mensagem de teste de configuração do SMTP.',
    );

    $this->response(['success' => true, 'message' => 'Tentativa de envio realizada, verifique a caixa de entrada para confirmar o recebimento.']);
  }

  public function jumpstart(): void
  {
    $this->validateNonce(__METHOD__);
    $this->validateCapability('manage_options');

    $jobs = $this->getParam('jobs', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];

    $worker = new JumpStart;

    array_map(fn ($job) => $worker->$job(), $jobs);

    $this->response(['success' => true, 'message' => 'Jumpstart concluído com sucesso.']);
  }

  public function plugins(): void
  {
    $this->validateNonce(__METHOD__);
    $this->validateCapability('install_plugins');

    $plugins = $this->getParam('plugins', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];

    array_map([Plugins::class, 'install'], $plugins);

    $this->response(['success' => true, 'message' => 'Plugins instalados e ativados com sucesso.']);
  }

  public function debug(): void
  {
    $this->validateNonce(__METHOD__);
    $this->validateCapability('manage_options');

    $worker = new Debug;

    $debugLog = filter_input(INPUT_POST, 'wp_debug_log', FILTER_VALIDATE_BOOL);
    $debugDisplay = filter_input(INPUT_POST, 'wp_debug_display', FILTER_VALIDATE_BOOL);
    $debug = $debugLog || $debugDisplay || filter_input(INPUT_POST, 'wp_debug', FILTER_VALIDATE_BOOL);

    $debug ? $worker->setConstAsTrue('wp_debug') : $worker->setConstAsFalse('wp_debug');
    $debugLog ? $worker->setConstAsTrue('wp_debug_log') : $worker->setConstAsFalse('wp_debug_log');
    $debugDisplay ? $worker->setConstAsTrue('wp_debug_display') : $worker->setConstAsFalse('wp_debug_display');

    $this->response(['success' => true, 'message' => 'Atualizações feitas com sucesso! Recarrega a página para ver os logs']);
  }

  public function clearDebug(): void
  {
    $this->validateCapability('manage_options');

    (new Debug)->clearLogs();

    $this->response(['success' => true, 'message' => 'Atualizações feitas com sucesso!']);
  }

  public function getDebugLogs(): void
  {
    $this->validateCapability('manage_options');

    $this->response((new Debug)->getCurrentLogContents());
  }
}
