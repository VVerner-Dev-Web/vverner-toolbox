<?php

namespace VVerner\Adapter;

use stdClass;

class DbQuery
{
  private string $select = '*';
  private string $from = '';
  private string $where = '1';
  private string $orderby = '';
  private string $order = 'DESC';

  private ?string $objectClass = null;

  public function select(string $select): self
  {
    $this->select = $select;

    if (class_exists($select) && is_subclass_of($select, Entity::class)) {
      $this->from($select::$TABLE);
      $this->objectClass = $select;
      $this->select = '*';
    }

    return $this;
  }

  public function from(string $from): self
  {
    $this->from = $from;
    return $this;
  }

  public function where(string $where): self
  {
    $this->where = $where;
    return $this;
  }

  public function orderby(string $orderby): self
  {
    $this->orderby = $orderby;
    return $this;
  }

  public function order(string $order): self
  {
    $this->order = $order;
    return $this;
  }

  public function fetchAll(): array
  {
    $sql = "SELECT {$this->select} FROM {$this->from} WHERE {$this->where} {$this->orderQuery()}";
    return $this->fetch($sql);
  }

  public function fetchOne(): mixed
  {
    $sql     = "SELECT {$this->select} FROM {$this->from} WHERE {$this->where} {$this->orderQuery()} LIMIT 1";
    $results = $this->fetch($sql);

    return array_shift($results);
  }

  public function fetchPage(int $currentPage, int $itemsPerPage = 30): stdClass
  {
    $pagination = (object) [
      'currentPage'   => $currentPage,
      'totalPages'    => 0,
      'nextPage'      => null,
      'previousPage'  => $currentPage > 1 ? $currentPage - 1 : null,
    ];

    $sql = "SELECT COUNT(*) as total FROM {$this->from} WHERE {$this->where}";
    $totalItems = (int) $this->fetch($sql, true)[0]->total;

    $pagination->totalPages = (int) max(1, ceil($totalItems / $itemsPerPage));
    $pagination->nextPage = $currentPage >= $pagination->totalPages ? null : 1 + $currentPage;

    $offset = $itemsPerPage * ($currentPage - 1);
    $limit  = "LIMIT $offset," . $itemsPerPage;

    $sql  = str_replace('COUNT(*) as total', $this->select, $sql);
    $sql .= " {$this->orderQuery()} $limit";

    $results = $this->fetch($sql);

    return (object) ['pagination' => $pagination, 'results' => $results];
  }

  private function fetch(string $sql, bool $counting = false)
  {
    global $wpdb;

    if (vvernerToolboxInDev()) :
      error_log($sql);
    endif;

    $results = $this->fetchingCol() ? $wpdb->get_col($sql) : $wpdb->get_results($sql);

    if ($this->objectClass && !$counting) :
      $results = array_map(fn ($row) => $this->objectClass::loadFromDbObject(new $this->objectClass, $row), $results);
    endif;

    return $results;
  }

  private function fetchingCol(): bool
  {
    return '*' !== $this->select && count(explode(',', $this->select)) === 1;
  }

  private function orderQuery(): string
  {
    return $this->orderby ? " ORDER BY {$this->orderby} $this->order" : "";
  }
}
