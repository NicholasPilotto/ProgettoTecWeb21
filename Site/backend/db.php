<?php

namespace DB;

use cart;
use mysqli;

require_once('response_manager.php');

class Constant {
  protected const HOST_DB = "127.0.0.1";
  protected const DATABASE_NAME = "";
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
    $query = "SELECT libro.*, autore.nome AS autore_nome, autore.cognome AS autore_cognome, editore.nome AS editore_nome, offerte.sconto, offerte.data_fine 
              FROM libro 
              INNER JOIN pubblicazione 
              ON pubblicazione.libro_isbn = libro.isbn 
              INNER JOIN autore 
              ON autore.id = pubblicazione.autore_id 
              INNER JOIN editore 
              ON libro.editore = editore.id 
              LEFT JOIN offerte
              ON libro.isbn = offerte.libro_isbn AND offerte.data_fine > DATE(NOW())
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
      $res->set_error_message("Nessun libro ha questo titolo");
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
      $res->set_error_message("Nessun libro ha questo autore");
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
      $res->set_error_message("Nessun libro appartenente a questo genere");
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

  public function get_utente_by_email($email): response_manager {
    $query = "SELECT *
              FROM utente
              WHERE email = ?";
    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false || $stmt->bind_param('s', $email) === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Nessun utente possiede questa mail");
    }

    $stmt->close();
    return $res;
  }

  public function get_utente_by_id($id): response_manager {
    $query = "SELECT *
              FROM utente
              WHERE Codice_identificativo = ?";
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
      $res->set_error_message("Nessun utente possiede questo ID");
    }

    $stmt->close();
    return $res;
  }

  public function get_bestsellers(): response_manager {
    $query = "SELECT libro.*, count(Libro.isbn) AS sold 
              FROM libro 
              INNER JOIN composizione 
              ON composizione.elemento = libro.isbn 
              GROUP BY libro.isbn 
              ORDER BY sold DESC";

    $stmt = $this->connection->prepare($query);
    $result = array();
    if ($stmt === false) {
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
      $res->set_error_message("Nessun besteller trovato");
    }

    return $res;
  }

  public function insert_book($isbn, $titolo, $editore, $pagine, $prezzo, $quantita, $data_pub, $percorso): response_manager {
    $query = "INSERT INTO Libro(ISBN,Titolo,Editore,Pagine,Prezzo,Quantita,Data_Pubblicazione,Percorso) VALUES (?,?,?,?,?,?,?,?)";
    $stmt = $this->connection->prepare($query);

    if ($stmt === false || $stmt->bind_param('ssiidiss', $isbn, $titolo, $editore, $pagine, $prezzo, $quantita, $data_pub, $percorso) === false) {
      return new response_manager(array(), $this->connection, "Qualcosa sembra essere andato storto");
    }

    $response = $stmt->execute();

    $stmt->close();

    if (!$response) {
      return new response_manager(array(), $this->connection, "Qualcosa sembra essere andato storto");
    }
    return new response_manager(array(true), $this->connection, "");
  }

  public function edit_book($isbn, $titolo, $editore, $pagine, $prezzo, $quantita, $data_pub, $percorso): response_manager {
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


    if ($stmt === false || $stmt->bind_param($type, ...$components) === false) {
      return new response_manager(array(), $this->connection, "Qualcosa sembra essere andato storto");
    }

    $response = $stmt->execute();

    $stmt->close();

    if (!$response) {
      return new response_manager(array(), $this->connection, "Qualcosa sembra essere andato storto");
    }
    return new response_manager(array(true), $this->connection, "");
  }

  public function signin($nome, $cognome, $nascita, $username, $email, $pass, $tel): response_manager {
    $query = "INSERT INTO utente (nome,cognome,data_nascita,username,email,password,telefono) VALUES (?,?,?,?,?,?,?)";
    $stmt = $this->connection->prepare($query);
    $psw = hash('sha256', $pass);

    if ($stmt === false || $stmt->bind_param('sssssss', $nome, $cognome, $nascita, $username, $email, $psw, $tel) === false) {
      return new response_manager(array(), $this->connection, "Qualcosa sembra essere andato storto");
    }

    $response = $stmt->execute();

    $stmt->close();

    if (!$response) {
      return new response_manager(array(), $this->connection, "Qualcosa sembra essere andato storto");
    }
    return $this->login($email, $pass);
  }

  public function login($mail, $pass): response_manager {
    $query = "SELECT codice_identificativo, nome, cognome, data_nascita, username, email, telefono
              FROM utente
              WHERE email = ? AND password = ?";
    $stmt = $this->connection->prepare($query);
    $result = array();

    $psw = hash('sha256', $pass);

    if ($stmt === false || $stmt->bind_param('ss', $mail, $psw) === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Questo utente non esiste");
    }

    $stmt->close();
    return $res;
  }

  public function update_user_data($utente_id, $old_data, $new_data): response_manager {
    $query = "UPDATE utente SET ";

    $components = array();
    $aux = "";
    $type = "";

    $result = array();

    $result = array_diff($new_data, $old_data);


    if (isset($result["username"])) {
      array_push($components, $result["username"]);
      $type .= "s";
      $aux .= " username = ?,";
    }

    if (isset($$result["email"])) {
      array_push($components, $$result["email"]);
      $type .= "s";
      $aux .= " email = ?,";
    }

    if (isset($$result["password"])) {
      array_push($components, $$result["password"]);
      $type .= "s";
      $aux .= " password = ?,";
    }

    $aux = substr($aux, 0, -1);
    array_push($components, $utente_id);

    $aux .= " ";

    $query .= $aux . "WHERE codice_identificativo = ?";

    $type .= "s";


    $stmt = $this->connection->prepare($query);


    if ($stmt === false || $stmt->bind_param($type, ...$components) === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $res = $stmt->execute();

    if (!$res) {
      return new response_manager($result, $this->connection, "Non è stato possibile aggiornare i dati");
    }
    return new response_manager(array(true), $this->connection, "");
  }

  public function get_addresses($utente_id): response_manager {
    $query = "SELECT *
              FROM indirizzo 
              WHERE utente = ?";
    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false || $stmt->bind_param('s', $utente_id) === false) {
      return new response_manager(array(), $this->connection, "Qualcosa sembra essere andato storto");
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

  public function insert_address($utente_id, $via, $citta, $cap, $civico): response_manager {
    $query = "INSERT INTO Indirizzo(Via,Città,Cap,Num_civico,Utente) VALUES (?,?,?,?,?)";
    $stmt = $this->connection->prepare($query);

    $result = array();

    if ($stmt === false || $stmt->bind_param('ssiis', $via, $citta, $cap, $civico, $utente_id) === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $tmp = $stmt->execute();

    if (!$tmp) {
      return new response_manager($result, $this->connection, "Non è stato possibile inserire un indirizzo");
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Nessuna recensione effettuata da questo utente");
    }

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
      $res->set_error_message("Nessuna recensione trovata per questo libro");
    }

    $stmt->close();
    return $res;
  }

  public function get_reviews_by_user($utente): response_manager {
    $query = "SELECT *
              FROM recensione
              INNER JOIN libro ON libro.isbn = recensione.libro_isbn
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
      $res->set_error_message("Nessuna recensione effettuata da questo utente per questo libro");
    }

    $stmt->close();
    return $res;
  }

  public function get_orders(): response_manager {
    $query = "SELECT *
              FROM ordine";

    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Nessun ordine");
    }

    $stmt->close();
    return $res;
  }

  public function get_order_books($order): response_manager {
    $query = "SELECT ordine.codice_univoco, libro.titolo, libro.isbn, ordine.data, ordine.data_consegna, ordine.totale, composizione.quantita
              FROM ordine
              INNER JOIN composizione 
              ON ordine.Codice_univoco = composizione.Codice_ordine
              INNER JOIN libro
              ON composizione.Elemento = libro.ISBN
              WHERE ordine.Cliente_Codice = ?
              ORDER BY ordine.codice_univoco";

    /*
    $query = "SELECT *
              FROM ordine
              INNER JOIN composizione 
              ON ordine.Codice_univoco = composizione.Codice_ordine
              INNER JOIN libro
              ON composizione.Elemento = libro.ISBN
              WHERE ordine.Cliente_Codice = ?";*/

    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false || $stmt->bind_param('s', $order) === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Nessun libro per questo ordine");
    }

    $stmt->close();
    return $res;
  }

  public function insert_review($utenteid, $isbn, $valore, $commento): response_manager {
    $query = "INSERT INTO Recensione(idUtente,Libro_ISBN,DataInserimento,Valutazione,Commento) VALUES (?,?,?,?,?)";

    $stmt = $this->connection->prepare($query);
    $today = date('Y-m-d');
    $result = array();


    if ($stmt === false || $stmt->bind_param('sssis', $utenteid, $isbn, $today, $valore, $commento) === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $res = $stmt->execute();

    if (!$res) {
      return new response_manager($result, $this->connection, "Non è stato possibile inserire una recensione");
    }
    return new response_manager(array(true), $this->connection, "");
  }

  public function edit_review($utenteid, $isbn, $valore, $commento): response_manager {
    $query = "UPDATE recensione SET ";

    $components = array();
    $aux = "";
    $type = "";

    $result = array();

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


    if ($stmt === false || $stmt->bind_param($type, ...$components) === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $res = $stmt->execute();

    if (!$res) {
      return new response_manager($result, $this->connection, "Non è stato possibile inserire una recensione");
    }
    return new response_manager(array(true), $this->connection, "");
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
              ORDER BY libro.data_pubblicazione DESC 
              LIMIT 7";

    $stmt = $this->connection->prepare($query);
    $result = array();
    if ($stmt === false) {
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
      $res->set_error_message("Nessun besteller trovato");
    }

    return $res;
  }

  public function get_books_under_5(): response_manager {
    $query = "SELECT * 
              FROM libro
              WHERE prezzo < 5";

    $result = array();
    $stmt = $this->connection->prepare($query);
    $result = array();
    if ($stmt === false) {
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
      $res->set_error_message("Nessun besteller trovato");
    }

    return $res;
  }

  public function get_all_books(): response_manager {
    $query = "SELECT libro.*, autore.nome AS autore_nome, autore.cognome AS autore_cognome, editore.nome AS editore_nome, offerte.sconto, offerte.data_fine 
              FROM libro 
              INNER JOIN pubblicazione 
              ON pubblicazione.libro_isbn = libro.isbn 
              INNER JOIN autore 
              ON autore.id = pubblicazione.autore_id 
              INNER JOIN editore 
              ON libro.editore = editore.id
              LEFT JOIN offerte
              ON libro.isbn = offerte.libro_isbn AND offerte.data_fine > DATE(NOW())";

    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false) {
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
      $res->set_error_message("Nessun libro trovato");
    }

    $stmt->close();
    return $res;
  }

  public function get_genres_from_isbn($isbn): response_manager {
    $query = "SELECT * FROM categoria
              INNER JOIN appartenenza 
              ON categoria.ID_Categoria = appartenenza.Codice_Categoria
              WHERE appartenenza.Libro_ISBN = ?";
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
      $res->set_error_message("Nessuna genere per questo ISBN");
    }

    $stmt->close();
    return $res;
  }

  public function get_books_with_offers(): response_manager {
    $query = "SELECT * FROM libro
              INNER JOIN offerte
              ON offerte.libro_ISBN = libro.ISBN
              WHERE offerte.data_fine < DATE(NOW())";
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
  }

  public function get_active_offer_by_isbn($isbn): response_manager {
    $query = "SELECT sconto
              FROM offerte
              WHERE libro_isbn = ? AND data_fine > DATE(NOW())";
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
      $res->set_error_message("Nessuna offerta trovata con questo ISBN");
    }

    $stmt->close();
    return $res;
  }

  public function restore_code($utente): response_manager {
    $query = "INSERT INTO Recupero (id, utente) VALUES (?,?)
              ON DUPLICATE KEY UPDATE 
              id=?";
    $id = md5(uniqid(rand(), true));
    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false || $stmt->bind_param('sss', $id, $utente, $id) === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $stmt->execute();

    array_push($result, $id);

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Non è stato possibile generare il codice");
    }

    $stmt->close();

    return $res;
  }

  public function is_code_correct($id, $utente): response_manager {
    $query = "SELECT *
              FROM Recupero
              WHERE id = ? AND utente = ?";
    $stmt = $this->connection->prepare($query);
    $result = array();

    if ($stmt === false || $stmt->bind_param('ss', $id, $utente) === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $stmt->execute();
    $tmp = $stmt->get_result();

    while ($row = $tmp->fetch_assoc()) {
      array_push($result, $row);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Il codice non corrisponde");
    }

    $stmt->close();

    return $res;
  }

  public function get_reward_badge($utente): response_manager {
    $query = "SELECT utente.codice_identificativo AS utente, count(ordine.codice_univoco) AS total
              FROM utente
              LEFT JOIN ordine
              ON ordine.cliente_codice = utente.codice_identificativo
              WHERE utente.codice_identificativo = ?
              GROUP BY utente.codice_identificativo";

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
      $res->set_error_message("Nessun utente trovato");
    }

    $stmt->close();

    return $res;
  }

  public function restore_psw($utente, $pass, $code): response_manager {
    $result = array();
    $query2 = "DELETE FROM recupero WHERE id = ? AND utente = ?";
    $stmt = $this->connection->prepare($query2);

    if ($stmt === false || $stmt->bind_param('ss', $code, $utente) === false) {
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $stmt->execute();

    if ($stmt->affected_rows != 0) {
      $query1 = "UPDATE utente SET password = ? WHERE codice_identificativo = ?";
      $stmt = $this->connection->prepare($query1);
      $psw = hash('sha256', $pass);

      if ($stmt === false || $stmt->bind_param('ss', $psw, $utente) === false) {
        return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
      }

      $tmp = $stmt->execute();

      array_push($result, $tmp);

      $res = new response_manager($result, $this->connection, "");

      if (!$res->ok()) {
        $res->set_error_message("Non è stato possibile generare il codice");
      }

      $stmt->close();

      return $res;
    }
    return new response_manager($result, $this->connection, "Il codice inserito non è stato trovato");
  }

  public function insert_order($cliente, $indirizzo, cart $carrello): response_manager {
    $this->connection->autocommit(false);
    $this->connection->begin_transaction();
    try {
      $query1 = "INSERT INTO ordine (Cliente_Codice, Data, Data_Partenza, Data_Consegna, Indirizzo, Totale)
              VALUES (?,?,?,?,?,?)";
      $stmt = $this->connection->prepare($query1);

      $result = array();

      $today = date('Y-m-d');
      $shipping_date = date('Y-m-d', strtotime('+ 2 days'));
      $arriving_date = date('Y-m-d', strtotime('+ 6 days'));
      $totale = $carrello->get_total();
      if ($stmt === false || $stmt->bind_param('isssid', $cliente, $today, $shipping_date, $arriving_date, $indirizzo, $totale) === false) {
        return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
      }

      $tmp = $stmt->execute();

      $orderID = $stmt->insert_id;

      $books_array = $carrello->get_cart();

      foreach ($books_array as $isbn => $data) {
        $query2 = "INSERT INTO composizione(elemento, codice_ordine, quantita) VALUES (?,?,?)";
        $stmt = $this->connection->prepare($query2);
        if ($stmt === false || $stmt->bind_param('ssi', $isbn, $orderID, $data->quant) === false) {
          return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
        }
        $tmp = $stmt->execute();
      }

      $this->connection->commit();
    } catch (\Throwable $exception) {
      $this->connection->rollback();
      return new response_manager($result, $this->connection, "Qualcosa sembra essere andato storto");
    }

    $this->connection->autocommit(true);

    if ($tmp) {
      array_push($result, $tmp);
    }

    $res = new response_manager($result, $this->connection, "");

    if (!$res->ok()) {
      $res->set_error_message("Non è stato possibile inserire l'ordine");
    }

    $stmt->close();
    return $res;
  }
}
