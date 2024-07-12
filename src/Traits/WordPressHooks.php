<?php

namespace VVerner\Traits;

defined('ABSPATH') || exit;

use ReflectionFunction;

trait WordPressHooks
{
  function action(string $hook, callable $callback, int $priority = 10, int $arguments = 1)
  {
    global $VVerner;

    $ref = new ReflectionFunction($callback);

    $VVerner['hooks'][] = [
      'type'    => 'action',
      'hook'    => $hook,
      'source'  => get_called_class() . '::' . $ref->name . " ($priority, $arguments)",
    ];

    add_action($hook, $callback, $priority, $arguments);
  }

  function filter(string $filter, callable $callback, int $priority = 10, int $arguments = 1)
  {
    global $VVerner;

    $VVerner['hooks'][] = [
      'type'    => 'filter',
      'hook'    => $filter,
      'source'  => get_called_class(),
    ];

    add_filter($filter, $callback, $priority, $arguments);
  }
}
