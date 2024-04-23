<?php

namespace VVerner\Adapter;

use stdClass;

abstract class Entity
{
  public static string $TABLE;

  public int $id;

  public function __construct(int $id = null)
  {
    if ($id === null || $id === 0) {
      return;
    }
    $this->load($id);
  }

  abstract public static function loadFromDbObject(Entity $cls, stdClass $db): Entity;
  // {
  //   return $cls;
  // }

  abstract protected function db(string $returnType = 'value'): array;
  // {
  //   return array_map(fn ($item): mixed => $item[$returnType], [
  //     'origin'       => [
  //       'value'   => $this->id,
  //       'format'  => '%d'
  //     ]
  //   ]);
  // }

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

    $sql  = "SELECT * FROM " . static::$TABLE . ' WHERE id = ' . $id;
    $data = $wpdb->get_row($sql);

    if ($data) :
      static::loadFromDbObject($this, $data);
    endif;
  }
}
