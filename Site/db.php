<?php

namespace DB;

use mysqli;

class Constant {
  protected const HOST_DB = "";
  protected const DATABASE_NAME = "";
  protected const USERNAME = "";
  protected const PASSWORD = "";
}

class Service extends Constant {
  private $connection;

  public function openConnection(): bool {
    $this->connection = new mysqli(parent::HOST_DB, parent::USERNAME, parent::PASSWORD, parent::DATABASE_NAME);

    if ($this->connection->connect_errno) {
      return false;
    }

    return true;
  }

  public function closeConnection(): void {
    $this->connection->close();
  }

  public function get_book_by_isbn($isbn): array {
    $query = "SELECT * FROM libro INNER JOIN pubblicazione ON pubblicazione.libro_isbn = libro.isbn INNER JOIN autore ON autore.id = pubblicazione-autore_id WHERE libro.isbn = ?";
    $stmt = $this->connection->prepare($query);
    $stmt->bind_param('s', $isbn);
    $tmp = $stmt->execute();

    if ($tmp->num_rows == 0) {
      return NULL;
    }

    $result = array();

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $tmp->free();
    $stmt->close();
    return $result;
  }

  public function get_book_reviews($isbn): array {
    $query = "SELECT * FROM recensione WHERE libro_isbn = ?";
    $stmt = $this->connection->prepare($query);
    $stmt->bind_param('s', $isbn);
    $tmp = $stmt->execute();

    if ($tmp->num_rows == 0) {
      return NULL;
    }

    $result = array();

    while ($row = $tmp->fetch_assoc()) {
      array_push($resul, $row);
    }

    $tmp->free();
    $stmt->close();
    return $result;
  }

  public function get_books_by_tag($tag): array {
    $query = "SELECT * FROM libro INNER JOIN tipologia ON tipologia.libro_isbn = libro.isbn WHERE tipologia.tag_id = ?";
    $stmt = $this->connection->prepare($query);
    $stmt->bind_param('i', $tag);
    $tmp = $stmt->execute();

    if ($tmp->num_rows == 0) {
      return NULL;
    }

    $result = array();

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $tmp->free();
    $stmt->close();
    return $result;
  }

  public function login($mail, $password): bool {
    $query = "SELECT * FROM utente WHERE mail = ? AND password = ?";
    $stmt = $this->connection->prepare($query);
    $hash_password = hash('sha256', $password);
    $stmt->bind_param('ss', $mail, $hash_password);
    $tmp = $stmt->execute();

    return $tmp->num_rows;
  }
}
