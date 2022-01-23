<?php

namespace DB;

use mysqli;

require_once('response_manager.php');

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

  public function get_book_by_isbn($isbn): response_manager {
    $query = "SELECT libro.*, autore.nome AS autore_nome, autore.cognome AS autore_cognome, editore.nome AS editore_nome 
              FROM libro 
              INNER JOIN pubblicazione 
              ON pubblicazione.libro_isbn = libro.isbn 
              INNER JOIN autore 
              ON autore.id = pubblicazione.autore_id 
              INNER JOIN editore 
              ON libro.editore = editore.id 
              WHERE libro.isbn = ?";
    $stmt = $this->connection->prepare($query);

    $result = array();

    if ($stmt === false || $stmt->bind_param('s', $isbn) === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    $result = array();

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Nessun libro trovato con questo ISBN");
    }

    $stmt->close();
    return $res;
  }

  public function get_book_by_title($title): response_manager {
    $query = "SELECT libro.*, autore.nome AS autore_nome, autore.cognome AS autore_cognome, editore.nome AS editore_nome  
              FROM libro 
              INNER JOIN pubblicazione 
              ON pubblicazione.libro_isbn = libro.isbn 
              INNER JOIN autore 
              ON autore.id = pubblicazione.autore_id 
              INNER JOIN editore 
              ON libro.editore = editore.id 
              WHERE libro.titolo LIKE ?";
    $stmt = $this->connection->prepare($query);
    $result = array();

    $title = '%' . $title . '%';

    $result = array();

    if ($stmt === false || $stmt->bind_param('s', $title) === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Nessun libro trovato con questo titolo");
    }

    $stmt->close();
    return $res;
  }

  public function get_books_by_author($author_firstname, $author_lastname): response_manager {
    $query = "SELECT libro.*, autore.nome AS autore_nome, autore.cognome AS autore_cognome, editore.nome AS editore_nome  
              FROM libro 
              INNER JOIN pubblicazione 
              ON pubblicazione.libro_isbn = libro.isbn 
              INNER JOIN autore 
              ON autore.id = pubblicazione.autore_id 
              INNER JOIN editore 
              ON libro.editore = editore.id 
              WHERE autore.nome LIKE ? AND autore.cognome LIKE ?";
    $stmt = $this->connection->prepare($query);
    $result = array();

    $first = '%' . $author_firstname . '%';
    $last = '%' . $author_lastname . '%';

    if ($stmt === false || $stmt->bind_param('ss', $first, $last) === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Nessun libro trovato appartenente a questo autore");
    }

    $stmt->close();
    return $res;
  }

  public function get_books_by_genre($id): response_manager {
    $query = "SELECT libro.*, categoria.nome AS categoria_nome 
    FROM libro 
    INNER JOIN appartenenza 
    
    ON libro.ISBN = appartenenza.Libro_ISBN AND appartenenza.Codice_Categoria = ?
    INNER JOIN categoria ON categoria.ID_Categoria = appartenenza.Codice_Categoria";
    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false || $stmt->bind_param('i', $id) === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Nessun libro appartenente a questo genere");
    }

    $stmt->close();
    return $res;
  }

  public function get_new_books_by_genre($id): response_manager {
    $query = "SELECT libro.* 
              FROM libro 
              INNER JOIN appartenenza 
              ON libro.ISBN = appartenenza.Libro_ISBN AND appartenenza.Codice_Categoria = ? 
              ORDER BY libro.Data_Pubblicazione DESC 
              LIMIT 5";
    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false || $stmt->bind_param('i', $id) === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Nessun libro trovato con questo genere");
    }

    $stmt->close();
    return $res;
  }


  public function get_genre_by_id($id): response_manager {
    $query = "SELECT *
              FROM categoria 
              WHERE id_categoria = ?";
    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false || $stmt->bind_param('i', $id) === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Nessun genere possiede questo ID");
    }

    $stmt->close();
    return $res;
  }


  public function get_bestsellers(): response_manager {
    $query = "SELECT libro.*, count(libro.isbn) AS sold 
              FROM libro
              INNER JOIN composizione
              ON composizione.elemento = libro.isbn
              GROUP BY libro.isbn
              ORDER BY sold DESC";

    $stmt = $this->connection->query($query);

    $result = array();

    while ($row = $stmt->fetch_assoc()) {
      array_push($result, $row);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Nessun besteller trovato");
    }

    $stmt->free();
    return $res;


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
      $type .= "s";
      $aux .= " titolo = ?,";
    }

    if (isset($editore)) {
      array_push($components, $editore);
      $type .= "i";
      $aux .= " editore = ?,";
    }
    if (isset($pagine)) {
      array_push($components, $pagine);
      $type .= "i";
      $aux .= " pagine = ?,";
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

  public function login($mail, $pass): response_manager {
    $query = "SELECT codice_identificativo, nome, cognome, data_nascita, username, email, telefono
              FROM utente
              WHERE email = ? AND password = ?";
    $stmt = $this->connection->prepare($query);
    $result = false;

    $psw = hash('sha256', $pass);

    if ($stmt === false || $stmt->bind_param('ss', $mail, $psw) === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    $result = array();

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Nessun utente trovato");
    }

    $stmt->close();
    return $res;
  }

  public function get_addresses($utente_id): response_manager {
    $query = "SELECT *
              FROM indirizzo 
              WHERE utente = ?";
    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false || $stmt->bind_param('s', $utente_id) === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Nessun indirizzo trovato");
    }

    $stmt->close();
    return $res;
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

  public function get_avg_review($isbn): response_manager {
    $query = "SELECT libro_isbn, AVG(valutazione) AS media
              FROM recensione 
              WHERE libro_isbn = ?
              GROUP BY libro_isbn";
    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false || $stmt->bind_param('s', $isbn) === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Nessuna recensione trovata per questo libro");
    }

    $stmt->close();
    return $res;
  }

  public function get_reviews_by_isbn($isbn): response_manager {
    $query = "SELECT *
              FROM recensione 
              WHERE libro_isbn = ?";
    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false || $stmt->bind_param('s', $isbn) === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Nessuna recensione per questo ISBN");
    }

    $stmt->close();
    return $res;
  }

  public function get_reviews_by_user($utente): response_manager {
    $query = "SELECT *
              FROM recensione 
              WHERE idutente = ?";
    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false || $stmt->bind_param('s', $utente) === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Nessuna recensione effettuata da questo utente");
    }

    $stmt->close();
    return $res;
  }

  public function get_review_by_user_book($utente, $isbn): response_manager {
    $query = "SELECT *
              FROM recensione 
              WHERE idutente = ? AND libro_isbn = ?";
    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false || $stmt->bind_param('ss', $utente, $isbn) === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Nessuna recensione di questo utente riferita al librio selezionato");
    }

    $stmt->close();
    return $res;
  }

  public function get_orders(): response_manager {
    $query = "SELECT *
              FROM ordine";

    $stmt = $this->connection->query($query);

    $result = array();

    while ($row = $stmt->fetch_assoc()) {
      array_push($result, $row);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Nessun ordine trovato");
    }

    $stmt->free();
    return $res;


    return $result;
  }

  public function insert_review($utenteid, $isbn, $valore, $commento): string {
    $query = "INSERT INTO Recensione(idUtente,Libro_ISBN,DataInserimento,Valutazione,Commento) VALUES (?,?,?,?,?)";

    $stmt = $this->connection->prepare($query);
    $today = date('Y-m-d');


    if ($stmt === false) {
      return "a";
    }

    if ($stmt->bind_param('sssis', $utenteid, $isbn, $today, $valore, $commento) === false) {
      return "b";
    }

    $res = $stmt->execute();

    $stmt->close();


    return $res;
  }

  public function edit_review($utenteid, $isbn, $valore, $commento): bool {
    $query = "UPDATE recensione SET ";

    $components = array();
    $aux = "";
    $type = "";


    if (isset($valore)) {
      array_push($components, $valore);
      $type .= "i";
      $aux .= " valutazione = ?,";
    }

    if (isset($commento)) {
      array_push($components, $commento);
      $type .= "s";
      $aux .= " commento = ?,";
    }

    $aux = substr($aux, 0, -1);
    array_push($components, $utenteid);
    array_push($components, $isbn);

    $aux .= " ";

    $query .= $aux . "WHERE idutente = ? AND libro_isbn = ?";

    $type .= "ss";


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

  public function delete_review($utenteid, $isbn): bool {
    $query = "DELETE FROM recensione 
              WHERE idUtente = ? AND libro_isbn = ?";

    $stmt = $this->connection->prepare($query);

    if ($stmt === false) {
      return "a";
    }

    if ($stmt->bind_param('ss', $utenteid, $isbn) === false) {
      return "b";
    }

    $res = $stmt->execute();

    $stmt->close();


    return $res;
  }

  public function get_new_books(): response_manager {
    $query = "SELECT * 
              FROM libro 
              ORDER BY libro.Data_Pubblicazione DESC 
              LIMIT 7";

    $stmt = $this->connection->query($query);

    $result = array();

    while ($row = $stmt->fetch_assoc()) {
      array_push($result, $row);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Nessun nuovo libro trovato");
    }

    $stmt->free();
    return $res;


    return $result;
  }

  public function get_books_under_5(): response_manager {
    $query = "SELECT * 
              FROM libro 
              WHERE prezzo < 5";

    $stmt = $this->connection->query($query);

    $result = array();

    while ($row = $stmt->fetch_assoc()) {
      array_push($result, $row);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Nessun libro con prezzo inferiore a €5 trovato");
    }

    $stmt->free();
    return $res;


    return $result;
  }

  public function get_all_books(): array {
    $query = "SELECT * 
              FROM libro";

    $stmt = $this->connection->query($query);

    $stmt = $this->connection->query($query);

    $result = array();

    while ($row = $stmt->fetch_assoc()) {
      array_push($result, $row);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Nessun libro trovato");
    }

    $stmt->free();
    return $res;


    return $result;
  }
}