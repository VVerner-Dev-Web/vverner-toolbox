<?php

namespace VVerner\Adapter;

abstract class Configuration
{
  public function __construct()
  {
    $props = get_class_vars(static::class);

    foreach ($props as $prop => $default) :
      $this->$prop = $this->get($prop, $default);
    endforeach;
  }
  public function save(): void
  {
    $props = array_keys(get_class_vars(static::class));

    foreach ($props as $prop) :
      $this->set($prop, $this->$prop);
    endforeach;
  }

  private function get(string $option, $default = null): mixed
  {
    return get_option($this->getPrefix() . $option, $default);
  }

  private function set(string $option, $value): void
  {
    update_option($this->getPrefix() . $option, $value);
  }

  private function getPrefix(): string
  {
    return strtolower(str_replace('\\', '/', static::class) . '/');
  }
}
