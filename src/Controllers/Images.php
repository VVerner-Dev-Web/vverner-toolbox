<?php

namespace VVerner\Controllers;

defined('ABSPATH') || exit;

use VVerner\Adapter\Configuration;

class Images extends Configuration
{
  public int $maxWidth  = 1200;
  public int $maxHeight = 1200;
  public int $quality   = 85;
}
