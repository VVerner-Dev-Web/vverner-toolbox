<?php

namespace VVerner\Controllers;

use VVerner\Adapter\Configuration;

class Emails extends Configuration
{
  public ?string $fromName;
  public ?string $fromEmail;

  public ?string $host;
  public ?string $port;
  public ?string $security;
  public ?string $user;
  public ?string $password;

  public string $deliveryMode = 'wordpress';
  public bool $logsEnabled = false;
}
