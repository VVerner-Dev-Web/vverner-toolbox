<?php

namespace VVerner\Core;


class EntityAttributeNormalizer
{
  private array $rules;
  private ?string $group = null;

  public function __construct(public string $type, public mixed $value)
  {
    if (is_subclass_of($this->type, 'UnitEnum')) :
      $this->group = "Enum";
    elseif (is_subclass_of($this->type, 'DateTimeInterface')) :
      $this->group = "Date";
    endif;
    $this->bindRules();
  }

  private function bindRules()
  {
    $this->rules = match ($this->group ?? $this->type) {
      'bool'   => [
        'format' => '%d',
        'save'  => fn ($value) =>  $value ? 1 : 0,
        'load' => fn ($value) => $value ? 1 : 0
      ],
      'null'   => [
        'format' => '%d',
        'save'  => null,
        'load' => fn ($value) => $value ? 1 : 0
      ],
      'int'    => [
        'format' => '%d',
        'save'  => fn ($value) => (int) $value,
        'load' => fn ($value) => (int) $value
      ],
      'float'  => [
        'format' => '%f',
        'save'  => fn ($value) => (float) $value,
        'load' => fn ($value) => (float) $value
      ],
      'double' => [
        'format' => '%f',
        'save'  => fn ($value) => (float) $value,
        'load' => fn ($value) => (float) $value
      ],
      'array'   => [
        'format' => '%s',
        'save'  => fn ($value) => json_encode($value, true),
        'load' => fn ($value) => json_decode($value, true)
      ],
      'Enum'   => [
        'format' => '%s',
        'save'  => fn ($value) => $value->name,
        'load' => fn ($value) => constant($this->type . "::" . $value),
      ],
      'Date' => [
        'format' => '%s',
        'save'  => fn ($value) => $value->format('Y-m-d H:i:s'),
        'load' => fn ($value) => new $this->type($value)
      ],
      default => [
        'format' => '%s',
        'save'  => fn ($value) => (string) $value,
        'load' => fn ($value) => (string) $value
      ],
    };
  }

  public function save()
  {
    return $this->rules['save']($this->value);
  }

  public function load()
  {
    return $this->rules['load']($this->value);
  }

  public function format(): string
  {
    return $this->rules['format'];
  }
}
