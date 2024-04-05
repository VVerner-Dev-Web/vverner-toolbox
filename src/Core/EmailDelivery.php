<?php

namespace VVerner\Core;

use VVerner\Controllers\Emails;
use PHPMailer\PHPMailer\PHPMailer;

defined('ABSPATH') || exit('No direct script access allowed');

class EmailDelivery
{
  private Emails $controller;

  private function __construct()
  {
    $this->controller = new Emails;
  }

  public static function attach(): void
  {
    $cls = new self();
    add_filter('phpmailer_init', $cls->config(...));
  }

  private function config(PHPMailer $mailer): PHPMailer
  {
    if ($this->controller->deliveryMode !== 'smtp') :
      if ($this->controller->deliveryMode === 'none') :
        $mailer->clearAllRecipients();
      endif;

      return $mailer;
    endif;

    $mailer->XMailer     = 'VVerner';

    if ($this->controller->fromName) :
      $mailer->FromName = $this->controller->fromName;
    endif;

    if ($this->controller->fromEmail) :
      $mailer->From = $this->controller->fromEmail;
    endif;

    $allSet =
      $this->controller->host &&
      $this->controller->port &&
      $this->controller->security &&
      $this->controller->user &&
      $this->controller->password;

    if ($allSet) :
      $mailer->isSMTP();

      $mailer->SMTPAuth    = true;
      $mailer->Host        = $this->controller->host;
      $mailer->Port        = $this->controller->port;
      $mailer->SMTPSecure  = $this->controller->security;
      $mailer->Username    = $this->controller->user;
      $mailer->Password    = $this->controller->password;
    endif;

    if ($this->controller->logsEnabled) :
      $mailer->SMTPDebug   = 3;
      $mailer->Debugoutput = 'error_log';
    endif;

    return $mailer;
  }
}
