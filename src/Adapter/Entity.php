<?php

namespace VVerner\Adapter;

use ReflectionClass;
use stdClass;
use VVerner\Core\Schema;

abstract class Entity
{
  public static string $TABLE;

  public int $id;

  public function __construct(int $id = null)
  {
    if ($id) :
      $this->load($id);
    endif;
  }

  public static function loadFromDbObject(Entity $cls, stdClass $db): Entity
  {
    $props = (new ReflectionClass($cls))->getProperties();
    $dbFormats = Schema::dbFormats();

    foreach ($props as $prop) {
      if (!$prop->isPublic() || $prop->isStatic()) :
        continue;
      endif;

      $key = $prop->getName();
      $cb  = $dbFormats[$prop->getType()->getName()]['normalizer'];

      $cls->$key = $cb($db->$key);
    }

    return $cls;
  }

  private function db(string $returnType = 'value'): array
  {
    $props = (new ReflectionClass($this))->getProperties();
    $db    = [];
    $dbFormats = Schema::dbFormats();

    foreach ($props as $prop) {
      if (!$prop->isPublic() || !$prop->isInitialized($this) || $prop->isStatic()) :
        continue;
      endif;

      $db[$prop->getName()] = [
        'value'  => $prop->getValue($this),
        'format' => $dbFormats[$prop->getType()->getName()]['format']
      ];
    }

    return array_map(fn ($item): mixed => $item[$returnType], $db);
  }

  public function save(): bool
  {
    return isset($this->id) ? $this->update() : $this->create();
  }

  public function delete(): void
  {
    global $wpdb;

    $wpdb->delete(
      static::$TABLE,
      ['id' => $this->id],
      ['%d']
    );
  }

  protected function create(): bool
  {
    global $wpdb;

    $created = $wpdb->insert(static::$TABLE, $this->db('value'), $this->db('format'));

    if ($created) :
      $this->id = (int) $wpdb->insert_id;
    endif;

    return (bool) $created;
  }

  protected function update(): bool
  {
    global $wpdb;

    return (bool)  $wpdb->update(
      static::$TABLE,
      $this->db('value'),
      ['id' => $this->id],
      $this->db('format'),
      ['%d']
    );
  }

  protected function load(int $id): void
  {
    global $wpdb;

    $data = $wpdb->get_row("SELECT * FROM " . static::$TABLE . ' WHERE id = ' . $id);

    if ($data) :
      self::loadFromDbObject($this, $data);
    endif;
  }
}
