<?php

namespace VVerner\Adapter;

use ReflectionClass;
use stdClass;
use VVerner\Core\EntityAttributeNormalizer;

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

    foreach ($props as $prop) {
      if (!$prop->isPublic() || $prop->isStatic()) :
        continue;
      endif;

      $key = $prop->getName();

      $attributeNormalizer = new EntityAttributeNormalizer($prop->getType()->getName(), $db->$key);

      $cls->$key = $attributeNormalizer->load();
    }

    return $cls;
  }

  private function db(string $returnType = 'value'): array
  {
    $props = (new ReflectionClass($this))->getProperties();
    $db    = [];

    foreach ($props as $prop) {
      if (!$prop->isPublic() || !$prop->isInitialized($this) || $prop->isStatic()) :
        continue;
      endif;

      $attributeNormalizer = new EntityAttributeNormalizer($prop->getType()->getName(), $prop->getValue($this));
      $db[$prop->getName()] = [
        'value'  =>  $attributeNormalizer->save(),
        'format' =>  $attributeNormalizer->format()
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

    $table = static::$TABLE;
    $data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));

    if ($data) :
      self::loadFromDbObject($this, $data);
    endif;
  }
}
