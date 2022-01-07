DROP TABLE IF EXISTS Indirizzo ;
CREATE TABLE Indirizzo (
  Codice INT(4) PRIMARY KEY AUTO_INCREMENT,
  Via VARCHAR(50) NOT NULL,
  Città VARCHAR(20) NOT NULL,
  Cap INT(5) NOT NULL,
  Num_civico INT(3) NOT NULL
  );
 
 ALTER TABLE Indirizzo AUTO_INCREMENT=1000;

DROP TABLE IF EXISTS Utente ;
CREATE TABLE Utente(
  ID INT(6) PRIMARY KEY AUTO_INCREMENT,
  Nome VARCHAR(45) NOT NULL,
  Cognome VARCHAR(45) NOT NULL,
  Email VARCHAR(60) NOT NULL UNIQUE ,
  Password VARCHAR(10) NOT NULL,
  Metodo_pagamento INT(16) NOT NULL UNIQUE,
  Indirizzo INT(4) NOT NULL,
  Telefono INT(20) NOT NULL UNIQUE,
  Recensione INT(7) NOT NULL,
  FOREIGN KEY(Indirizzo) REFERENCES Indirizzo(Codice)
  );

 ALTER TABLE Utente AUTO_INCREMENT=300000;


DROP TABLE IF EXISTS Categorie ;
CREATE TABLE Categorie (
  Nome VARCHAR(25) NOT NULL,
  ISBN INT(13) NOT NULL,
  PRIMARY KEY( Nome, ISBN)
  );

DROP TABLE IF EXISTS Autore;
CREATE TABLE Autore (
  Nome VARCHAR(50) NOT NULL,
  ISBN INT(13) NOT NULL ,
  PRIMARY KEY (Nome, ISBN)
  );

DROP TABLE IF EXISTS Libri ;
CREATE TABLE Libri (
  Codice_identificativo INT(8) PRIMARY KEY AUTO_INCREMENT,
  ISBN INT(13) NOT NULL,
  Titolo VARCHAR(50) NOT NULL,
  Autore VARCHAR(50) NOT NULL,
  Categoria VARCHAR(25) NOT NULL,
  Condizione VARCHAR(20) NOT NULL,
  Venditore INT(6) NOT NULL,
  Pagine INT(4) NOT NULL,
  Prezzo DECIMAL(2,2) NOT NULL,
  Foto VARCHAR(50) NOT NULL,
  FOREIGN KEY(Venditore) REFERENCES Utente(ID),
  FOREIGN KEY(Categoria, ISBN) REFERENCES Categorie(Nome, ISBN),
  FOREIGN KEY(Autore, ISBN) REFERENCES Autore(Nome, ISBN)
  );
  
  ALTER TABLE Libri AUTO_INCREMENT=20000000;
  ALTER TABLE Libri CHECK (Prezzo >=0);

DROP TABLE IF EXISTS Vendite ;
CREATE TABLE Vendite (
  Codice INT(7) PRIMARY KEY AUTO_INCREMENT,
  Codice_prodotto INT(8) NOT NULL,
  ID_venditore INT(6) NOT NULL,
  Indirizzo_cliente INT(4) NOT NULL,
  Data DATE NOT NULL,
  Totale DECIMAL(2,2) NOT NULL,
  FOREIGN KEY(Codice_prodotto) REFERENCES Libri(Codice_identificativo),
  FOREIGN KEY(ID_venditore) REFERENCES Utente(ID),
  FOREIGN KEY(Indirizzo_cliente) REFERENCES Utente(Indirizzo)
  );
    
  ALTER TABLE Vendite AUTO_INCREMENT=8000000;


DROP TABLE IF EXISTS Recensioni ;
CREATE TABLE Recensioni (
  Numero INT(7) PRIMARY KEY AUTO_INCREMENT,
  ID_venditore INT(6) NOT NULL,
  Valutazione INT(1) NOT NULL,
  Commento VARCHAR(45) NOT NULL,
  Vendita INT(7) NOT NULL,

  FOREIGN KEY(ID_venditore) REFERENCES Utente(ID),
  FOREIGN KEY(Vendita) REFERENCES Vendite(Codice)
  );
 ALTER TABLE Recensioni AUTO_INCREMENT=6000000;
 ALTER TABLE Recensioni CHECK(valutazione >=1 && valutazione <=5);
