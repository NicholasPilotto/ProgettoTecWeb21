<?php

namespace DB;

use mysqli;

class Constant {
  protected const HOST_DB = "127.0.0.1";
  protected const DATABASE_NAME = "secondread";
  protected const USERNAME = "";
  protected const PASSWORD = "";
}

class Service extends Constant {
  private $connection;

  public function openConnection(): bool {
    $this->connection = new mysqli(parent::HOST_DB, parent::USERNAME, parent::PASSWORD, parent::DATABASE_NAME);
    $this->connection->set_charset("utf8");

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

  public function get_books_by_genre($id): array {
    $query = "SELECT * FROM libro INNER JOIN appartenenza ON libro.ISBN = appartenenza.Libro_ISBN AND appartenenza.Codice_Categoria = ? LIMIT 5";
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

  public function insert_book($isbn, $titolo, $editore, $pagine, $prezzo, $quantita, $data_pub, $percorso): bool {
    $query = "INSERT INTO Libro(ISBN,Titolo,Editore,Pagine,Prezzo,Quantita,Data_Pubblicazione,Percorso) VALUES (?,?,?,?,?,?,?,?)";
    $stmt = $this->connection->prepare($query);

    if ($stmt === false) {
      return false;
    }

    if ($stmt->bind_param('ssiidiss', $isbn, $titolo, $editore, $pagine, $prezzo, $quantita, $data_pub, $percorso) === false) {
      return false;
    }

    $res = $stmt->execute();
    $stmt->close();

    return $res;
  }

  public function edit_book($isbn, $titolo, $editore, $pagine, $prezzo, $quantita, $data_pub, $percorso): bool {
    $query = "UPDATE libro SET ";
    $components = array();
    $aux = "";
    $type = "";


    if (isset($titolo)) {
      array_push($components, $titolo);
      $type . "s";
      $aux . " titolo = ?,";
    }

    if (isset($editore)) {
      array_push($components, $editore);
      $type . "i";
      $aux . " editore = ?,";
    }
    if (isset($pagine)) {
      array_push($components, $pagine);
      $type . "i";
      $aux . " pagine = ?,";
    }
    if (isset($prezzo)) {
      array_push($components, $prezzo);
      $type .= "d";
      $aux .= " prezzo = ?,";
    }
    if (isset($quantita)) {
      array_push($components, $quantita);
      $type .= "i";
      $aux .= " quantita = ?,";
    }
    if (isset($data_pub)) {
      array_push($components, $data_pub);
      $type .= "s";
      $aux .= " data_pubblicazione = ?,";
    }
    if (isset($percorso)) {
      array_push($components, $percorso);
      $type .= "s";
      $aux .= " editore = ?,";
    }

    $aux = substr($aux, 0, -1);
    array_push($components, $isbn);

    $aux .= " ";

    $query .= $aux . "WHERE isbn = ?";

    $type .= "s";

    $stmt = $this->connection->prepare($query);


    if ($stmt === false) {
      return false;
    }


    if ($stmt->bind_param($type, ...$components) === false) {
      return false;
    }

    $res = $stmt->execute();

    $stmt->close();
    return $res;
  }

  public function signin($nome, $cognome, $nascita, $username, $email, $pass, $tel): bool {
    $query = "INSERT INTO Utente (Nome,Cognome,Data_nascita,Username,Email,password,Telefono) VALUES (?,?,?,?,?,?,?)";
    $stmt = $this->connection->prepare($query);
    $psw = hash('sha256', $pass);

    if ($stmt === false) {
      return false;
    }

    if ($stmt->bind_param('sssssss', $nome, $cognome, $nascita, $username, $email, $psw, $tel) === false) {
      return false;
    }

    $res = $stmt->execute();

    $stmt->close();


    return $res;
  }

  public function login($mail, $pass): bool {
    $query = "SELECT *
              FROM utente
              WHERE email = ? AND password = ?";
    $stmt = $this->connection->prepare($query);
    $result = false;

    $psw = hash('sha256', $pass);

    if ($stmt === false) {
      return false;
    }

    if ($stmt->bind_param('ss', $mail, $psw) === false) {
      return false;
    }

    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows != 0) {
      $result = true;
    }
    $stmt->close();

    return $result;
  }

  public function get_addresses($utente_id): array {
    $query = "SELECT *
     FROM indirizzo 
     WHERE utente = ?";
    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false) {
      return false;
    }

    if ($stmt->bind_param('s', $utente_id) === false) {
      return false;
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

  public function insert_address($utente_id, $via, $citta, $cap, $civico): bool {
    $query = "INSERT INTO Indirizzo(Via,Città,Cap,Num_civico,Utente) VALUES (?,?,?,?,?)";
    $stmt = $this->connection->prepare($query);


    if ($stmt === false) {
      return false;
    }

    if ($stmt->bind_param('ssiis', $via, $citta, $cap, $civico, $utente_id) === false) {
      return false;
    }

    $res = $stmt->execute();
    $stmt->close();

    return $res;
  }

  public function get_reviews($isbn): array {
    $query = "SELECT *
     FROM recensione 
     WHERE libro_isbn = ?";
    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false) {
      return false;
    }

    if ($stmt->bind_param('s', $isbn) === false) {
      return false;
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
