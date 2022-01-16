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
    $query = "SELECT * FROM libro INNER JOIN pubblicazione ON pubblicazione.libro_isbn = libro.isbn INNER JOIN autore ON autore.id = pubblicazione.autore_id INNER JOIN editore ON libro.editore = editore.id WHERE libro.isbn = ?";
    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false) {
      return $result;
    }

    if ($stmt->bind_param('s', $isbn) === false) {
      return $result;
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    if ($tmp->num_rows == 0) {
      return $result;
    }

    $result = array();

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $stmt->close();
    return $result;
  }

  public function get_book_by_title($title): array {
    $query = "SELECT * FROM libro INNER JOIN pubblicazione ON pubblicazione.libro_isbn = libro.isbn INNER JOIN autore ON autore.id = pubblicazione.autore_id INNER JOIN editore ON libro.editore = editore.id WHERE libro.titolo LIKE ?";
    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false) {
      return $result;
    }

    $title = '%' . $title . '%';

    if ($stmt->bind_param('s', $title) === false) {
      return $result;
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    if ($tmp->num_rows == 0) {
      return $result;
    }

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $stmt->close();
    return $result;
  }

  public function get_books_by_author($author_firstname, $author_lastname): array {
    $query = "SELECT * FROM libro INNER JOIN pubblicazione ON pubblicazione.libro_isbn = libro.isbn INNER JOIN autore ON autore.id = pubblicazione.autore_id INNER JOIN editore ON libro.editore = editore.id WHERE autore.nome LIKE ? AND autore.cognome LIKE ?";
    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false) {
      return $result;
    }

    $first = '%' . $author_firstname . '%';
    $last = '%' . $author_lastname . '%';


    if ($stmt->bind_param('ss', $first, $last) === false) {
      return $result;
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    if ($tmp->num_rows == 0) {
      return $result;
    }

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $stmt->close();
    return $result;
  }

  public function get_books_by_genre_new($id): array {
    $query = "SELECT * FROM libro INNER JOIN appartenenza ON libro.ISBN = appartenenza.Libro_ISBN AND appartenenza.Codice_Categoria = ? ORDER BY libro.Data_Pubblicazione DESC LIMIT 5";
    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false) {
      return $result;
    }

    if ($stmt->bind_param('i', $id) === false) {
      return $result;
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    if ($tmp->num_rows == 0) {
      return $result;
    }

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $stmt->close();
    return $result;
  }

  public function get_new_books_by_genre($id): array {
    $query = "SELECT * FROM libro INNER JOIN appartenenza ON libro.ISBN = appartenenza.Libro_ISBN AND appartenenza.Codice_Categoria = ? ORDER BY libro.Data_Pubblicazione DESC LIMIT 5";
    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false) {
      return $result;
    }

    if ($stmt->bind_param('i', $id) === false) {
      return $result;
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    if ($tmp->num_rows == 0) {
      return $result;
    }

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $stmt->close();
    return $result;
  }

  public function get_loved_books_by_genre($id): array{
    $query = "SELECT * FROM libro INNER JOIN recensioni ON libro.ISBN = appartenenza.Libro_ISBN AND appartenenza.Codice_Categoria = ? ORDER BY libro.Data_Pubblicazione DESC LIMIT 5";
    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false) {
      return $result;
    }

    if ($stmt->bind_param('i', $id) === false) {
      return $result;
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    if ($tmp->num_rows == 0) {
      return $result;
    }

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $stmt->close();
    return $result;
  }


  public function get_genre_by_id($id): array {
    $query = "SELECT * FROM categoria WHERE id_categoria = ?";
    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false) {
      return $result;
    }

    if ($stmt->bind_param('i', $id) === false) {
      return $result;
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    if ($tmp->num_rows == 0) {
      return $result;
    }

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $stmt->close();
    return $result;
  }


  public function get_bestsellers(): array {
    $query = "SELECT *, count(libro.isbn) AS sold 
              FROM libro
              INNER JOIN editore
              ON editore.id = libro.editore
              INNER JOIN composizione
              ON composizione.elemento = libro.isbn
              GROUP BY libro.isbn
              ORDER BY sold DESC";
    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false) {
      return $result;
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    if ($tmp->num_rows == 0) {
      return $result;
    }

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $stmt->close();
    return $result;
  }
}