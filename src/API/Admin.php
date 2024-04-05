<?php

namespace VVerner\API;

use VVerner\Adapter\Ajax;
use VVerner\Controllers\Emails;
use VVerner\Controllers\Images;
use VVerner\Core\JumpStart;

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

    if (!isVVernerUser()) :
      $this->response(['success' => false, 'message' => 'Sem permissão para executar o jumpstart.']);
    endif;

    $jobs = $this->getParam('jobs', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];

    $worker = new JumpStart;

    array_map(fn ($job) => $worker->$job(), $jobs);

    $this->response(['success' => true, 'message' => 'Jumpstart concluído com sucesso.']);
  }
}
