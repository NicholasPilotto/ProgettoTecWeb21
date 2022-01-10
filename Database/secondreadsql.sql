DROP TABLE IF EXISTS Editore;
CREATE TABLE Editore (
  ID INT(4) PRIMARY KEY AUTO_INCREMENT,
  Nome VARCHAR(50) NOT NULL
);

ALTER TABLE Editore AUTO_INCREMENT=3000;


DROP TABLE IF EXISTS Libro ;
CREATE TABLE Libro (
  ISBN INT(13) PRIMARY KEY,
  Titolo VARCHAR(45) NOT NULL,
  Editore VARCHAR(45) NOT NULL,
  Pagine INT(5) NOT NULL,
  Prezzo DECIMAL(2,2) NOT NULL,
  FOREIGN KEY (Editore) REFERENCES Editore(ID)
  );


DROP TABLE IF EXISTS Categoria ;
CREATE TABLE Categoria (
  ID_Categoria INT(2) PRIMARY KEY AUTO_INCREMENT,
  Nome VARCHAR(45) NOT NULL
);

ALTER TABLE Categoria AUTO_INCREMENT=10;


DROP TABLE IF EXISTS Autore ;
CREATE TABLE Autore(
  ID INT(5) PRIMARY KEY AUTO_INCREMENT,
  Nome VARCHAR(45) NOT NULL,
  Cognome VARCHAR(45) NOT NULL
);

ALTER TABLE Autore AUTO_INCREMENT=50000;


DROP Table IF EXISTS Pubblicazione ;
CREATE TABLE Pubblicazione (
  Libro_ISBN INT(13) NOT NULL,
  Autore_ID INT(5) NOT NULL,
  PRIMARY KEY (Libro_ISBN, Autore_ID),
  FOREIGN KEY(Libro_ISBN) REFERENCES Libro(ISBN),
  FOREIGN KEY(Autore_ID) REFERENCES Autore(ID)
  );



DROP Table IF EXISTS Appartenenza ;
CREATE TABLE Appartenenza (
  Libro_ISBN INT(13) NOT NULL,
  Codice_Categoria INT(2) NOT NULL,
  PRIMARY KEY (Libro_ISBN,Codice_Categoria),
  FOREIGN KEY(Libro_ISBN) REFERENCES Libro(ISBN),
  FOREIGN KEY(Codice_Categoria) REFERENCES Categoria(ID_Categoria)
  );


DROP TABLE IF EXISTS Recensione ;
CREATE TABLE Recensione (
  Numero INT(3) PRIMARY KEY AUTO_INCREMENT,
  Data Date NOT NULL
  );

ALTER TABLE Recensione AUTO_INCREMENT=300;


DROP Table IF EXISTS Recensione_Libro ;
CREATE TABLE Recensione_Libro (
  Recensione_Numero INT(3) NOT NULL,
  Libro_ISBN INT(13) NOT NULL,
  Valutazione INT(1) NOT NULL,
  PRIMARY KEY (Recensione_Numero,Libro_ISBN),
  FOREIGN KEY(Libro_ISBN) REFERENCES Libro(ISBN),
  FOREIGN KEY(Recensione_Numero) REFERENCES Recensione(Numero)
  );


DROP TABLE IF EXISTS Indirizzo ;
CREATE TABLE Indirizzo (
  Codice INT(6) PRIMARY KEY AUTO_INCREMENT,
  Via VARCHAR(50) NOT NULL,
  Città VARCHAR(20) NOT NULL,
  Cap INT(5) NOT NULL,
  Num_civico INT(3) NOT NULL,
  Utente VARCHAR(16) NOT NULL,
  PRIMARY KEY(Codice),
  FOREIGN KEY(Utente) REFERENCES Utente(Codice_Fiscale)
  );
 
ALTER TABLE Indirizzo AUTO_INCREMENT=100000;


DROP TABLE IF EXISTS Utente;
CREATE TABLE Utente (
  Codice_Fiscale VARCHAR(16) PRIMARY KEY,
  Nome VARCHAR(45) NOT NULL,
  Cognome VARCHAR(45) NOT NULL,
  Data_nascita DATE NOT NULL,
  Email VARCHAR(60) NOT NULL UNIQUE ,
  Password VARCHAR(10) NOT NULL,
  IBAN VARCHAR(27) NULL DEFAULT 0,
  Telefono INT(20) NOT NULL UNIQUE,
  Credito DECIMAL(9,2)
  );


DROP TABLE IF EXISTS Recensione_Venditore ;
CREATE TABLE Recensione_Venditore (
   Recensione_Numero INT(3) NOT NULL,
   Venditore_CF VARCHAR(16) NOT NULL,
   Commento VARCHAR(300) NOT NULL,
   PRIMARY KEY(Recensione_Numero, Venditore_CF),
   FOREIGN KEY(Recensione_Numero) REFERENCES Recensione(Numero),
   FOREIGN KEY(Venditore_CF) REFERENCES Utente(Codice_Fiscale)
);


DROP TABLE IF EXISTS Stock ;
CREATE TABLE Stock (
  Codice_elemento INT(8) PRIMARY KEY AUTO_INCREMENT,
  ISBN_Libro INT(13) UNSIGNED NOT NULL,
  CF_Venditore VARCHAR(16) NOT NULL,
  Prezzo DECIMAL(3,2) NOT NULL,
  Usato BOOLEAN NOT NULL,            /*O è oonsiderato false, mentre 1 true*/
  FOREIGN KEY(ISBN_Libro) REFERENCES Libro(ISBN),
  FOREIGN KEY(CF_Venditore) REFERENCES Utente(Codice_Fiscale)
  );

ALTER TABLE Stock AUTO_INCREMENT=80000000;


DROP TABLE IF EXISTS Ordine ;
CREATE TABLE Ordine (
  Codice_univoco INT(8) PRIMARY KEY AUTO_INCREMENT,
  Cliente_CF VARCHAR(16) NOT NULL UNIQUE,
  Data DATETIME NOT NULL,
  Indirizzo INT(4) NOT NULL,
  FOREIGN KEY(Cliente_CF) REFERENCES Utente(Codice_Fiscale),
  FOREIGN KEY (Indirizzo) REFERENCES Indirizzo(Codice)
);

ALTER TABLE Ordine AUTO_INCREMENT=50000000;


DROP TABLE IF EXISTS Composizione ;
CREATE TABLE Composizione (
  Elemento INT(8) NOT NULL,
  CF_Venditore VARCHAR(16) NOT NULL,
  Codice_ordine INT(8) NOT NULL,
  Totale DECIMAL(9,2) NOT NULL,
  Quantità INT(3) NOT NULL,
  PRIMARY KEY (Elemento,Codice_ordine,CF_Venditore),
  FOREIGN KEY(Elemento) REFERENCES Stock(Codice_elemento),
  FOREIGN KEY(Codice_ordine) REFERENCES Ordine(Codice_univoco),
  FOREIGN KEY(CF_Venditore) REFERENCES Stock(CF_Venditore)
  );


DROP TABLE IF EXISTS Fatturazione ;
CREATE TABLE Fatturazione (
  ID INT(6) PRIMARY KEY AUTO_INCREMENT,
  Totale DECIMAL(9,2) NOT NULL,
  Utente_cf VARCHAR(16) NOT NULL,
  Data DATETIME NOT NULL,
  FOREIGN KEY (Utente_cf) REFERENCES Utente(Codice_Fiscale)
    );

ALTER TABLE Fatturazione AUTO_INCREMENT=600000;
 

DROP TABLE IF EXISTS Foto;
CREATE TABLE Foto (
  ID INT(15) PRIMARY KEY AUTO_INCREMENT,
  Percorso VARCHAR(50) NULL,
  Libro INT(8) NOT NULL,
  CF_Venditore VARCHAR(16) NOT NULL,
  FOREIGN KEY (Libro) REFERENCES Stock(Codice_elemento)
);

ALTER TABLE Foto AUTO_INCREMENT= 700000000000000;
