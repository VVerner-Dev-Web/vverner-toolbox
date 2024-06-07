<?php

namespace VVerner\Core;

class Schema
{
  public string $table;
  public array $fields = [];

  private array $sqlFields = [];
  private array $sqlKeys = [];

  public function __construct(string $table)
  {
    $this->table = $table;
  }

  public function delta(): bool
  {
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $this->fieldsToSql();

    $sql = "CREATE TABLE {$this->table} (" . PHP_EOL;

    $sql .= implode(', ' . PHP_EOL, $this->sqlFields);
    $sql .= $this->sqlKeys ? ', ' . PHP_EOL . implode(', ' . PHP_EOL, $this->sqlKeys) : '';
    $sql .= ") COLLATE {$wpdb->collate}";

    dbDelta($sql);
    return true;
  }

  private function fieldsToSql(): void
  {
    $this->sqlFields = [];
    foreach ($this->fields as $key => $field) {

      $sqlField = "{$key} {$field['type']}";

      if (array_key_exists('nullable', $field) && !$field['nullable']) {
        $sqlField .= " NOT NULL";
      }

      if (array_key_exists('auto_increment', $field) && $field['auto_increment']) {
        $sqlField .= " AUTO_INCREMENT";
      }

      $this->sqlFields[] = $sqlField;

      // Check for primary keys
      if (array_key_exists('primary', $field) && $field['primary']) {
        $this->sqlKeys[] = "PRIMARY KEY  ({$key})";
      }

      // Check for unique keys
      if (array_key_exists('unique', $field) && $field['unique']) {
        $this->sqlKeys[] = "UNIQUE KEY {$key}_unique ({$key})";
      }

      // Check for foreign keys
      if (array_key_exists('foreign', $field)) {
        $foreign = $field['foreign'];
        $this->sqlKeys[] = "CONSTRAINT FK_{$this->table}_{$key} FOREIGN KEY ({$key}) REFERENCES {$foreign['table']}({$foreign['column']})";
      }
    }
  }

  public function addColumn(string $name, string $type): Schema
  {
    $this->fields[$name] = ['type' => $type];
    return $this;
  }

  public function primaryKey(string $name): Schema
  {
    $this->fields[$name]['primary'] = true;
    return $this;
  }

  public function nullable(string $name): Schema
  {
    $this->fields[$name]['nullable'] = true;
    return $this;
  }

  public function unique(string $name): Schema
  {
    $this->fields[$name]['unique'] = true;
    return $this;
  }

  public function autoIncrement(string $name): Schema
  {
    $this->fields[$name]['auto_increment'] = true;
    return $this;
  }

  public function foreignKey(string $name, string $referenceTable, string $referenceColumn): Schema
  {
    $this->fields[$name]['foreign'] = [
      'table' => $referenceTable,
      'column' => $referenceColumn
    ];
    return $this;
  }
}
