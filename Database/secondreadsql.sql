SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS Recensione ;
DROP TABLE IF EXISTS Composizione ;
DROP Table IF EXISTS Appartenenza ;
DROP Table IF EXISTS Pubblicazione ;
DROP TABLE IF EXISTS Libro;
DROP TABLE IF EXISTS Editore;
DROP TABLE IF EXISTS Categoria;
DROP TABLE IF EXISTS Autore;
DROP TABLE IF EXISTS Ordine;
DROP TABLE IF EXISTS Indirizzo;
DROP TABLE IF EXISTS Utente;

CREATE TABLE Editore (
  ID INT(4) UNSIGNED AUTO_INCREMENT,
  Nome VARCHAR(50) NOT NULL,
  PRIMARY KEY(ID)
);

CREATE TABLE Libro (
  ISBN BIGINT(13) UNSIGNED,
  Titolo VARCHAR(100) NOT NULL,
  Editore INT(4) UNSIGNED NOT NULL,
  Pagine INT(5) UNSIGNED NOT NULL,
  Prezzo DECIMAL(5,2) NOT NULL,
  Quantita INT(3) NOT NULL,
  Data_Pubblicazione DATE NOT NULL,
  Percorso VARCHAR(250) NOT NULL,
  PRIMARY KEY(ISBN),
  CONSTRAINT FK_LibroEditore
  FOREIGN KEY (Editore) REFERENCES Editore(ID)
);


CREATE TABLE Categoria (
  ID_Categoria INT(2) UNSIGNED AUTO_INCREMENT,
  Nome VARCHAR(45) NOT NULL,
  PRIMARY KEY(ID_Categoria)
);

CREATE TABLE Autore(
  ID INT(5) UNSIGNED AUTO_INCREMENT,
  Nome VARCHAR(45) NOT NULL,
  Cognome VARCHAR(45) NOT NULL,
  PRIMARY KEY(ID)
);

CREATE TABLE Pubblicazione (
  Libro_ISBN BIGINT(13) UNSIGNED,
  Autore_ID INT(5) UNSIGNED,
  PRIMARY KEY (Libro_ISBN, Autore_ID),
  CONSTRAINT FK_LibroPubblicazione
    FOREIGN KEY (Libro_ISBN) REFERENCES Libro(ISBN),
  CONSTRAINT FK_AutorePubblicazione
    FOREIGN KEY (Autore_ID) REFERENCES Autore(ID)
  );

CREATE TABLE Appartenenza (
  Libro_ISBN BIGINT(13) UNSIGNED,
  Codice_Categoria INT(2) UNSIGNED,
  PRIMARY KEY (Libro_ISBN,Codice_Categoria),
  CONSTRAINT FK_LibroAppartenenza
    FOREIGN KEY(Libro_ISBN) REFERENCES Libro(ISBN),
  CONSTRAINT FK_CategoriaAppartenenza
    FOREIGN KEY(Codice_Categoria) REFERENCES Categoria(ID_Categoria)
  );

CREATE TABLE Utente (
  Codice_identificativo INT(10) UNSIGNED AUTO_INCREMENT,
  Nome VARCHAR(45) NOT NULL,
  Cognome VARCHAR(45) NOT NULL,
  Data_nascita DATE NOT NULL,
  Username VARCHAR(5) NOT NULL,
  Email VARCHAR(60) NOT NULL UNIQUE ,
  Password VARCHAR(64) NOT NULL,
  Telefono VARCHAR(15) NOT NULL UNIQUE,
  PRIMARY KEY(Codice_identificativo)
  );

CREATE TABLE Indirizzo (
  Codice INT(6) UNSIGNED AUTO_INCREMENT,
  Via VARCHAR(50) NOT NULL,
  Città VARCHAR(20) NOT NULL,
  Cap INT(5) UNSIGNED NOT NULL,
  Num_civico INT(3) UNSIGNED NOT NULL,
  Utente INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY(Codice),
  CONSTRAINT FK_UtenteIndirizzo
  FOREIGN KEY(Utente) REFERENCES Utente(Codice_identificativo)
  );

CREATE TABLE Ordine (
  Codice_univoco INT(8) UNSIGNED AUTO_INCREMENT,
  Cliente_Codice INT(10) UNSIGNED NOT NULL,
  Data DATE NOT NULL,
  Data_partenza DATE NOT NULL,
  Data_consegna DATE NOT NULL,
  Indirizzo INT(4) UNSIGNED NOT NULL,
  Totale DECIMAL(9,2) UNSIGNED NOT NULL,
  PRIMARY KEY(Codice_univoco),
  CONSTRAINT FK_UtenteOrdine
  FOREIGN KEY(Cliente_Codice) REFERENCES Utente(Codice_identificativo),
  CONSTRAINT FK_IndirizzoOrdine
  FOREIGN KEY (Indirizzo) REFERENCES Indirizzo(Codice)
);

CREATE TABLE Composizione (
  Elemento BIGINT(13) UNSIGNED,
  Codice_ordine INT(8) UNSIGNED NOT NULL,
  Quantita INT(3) UNSIGNED,
  PRIMARY KEY (Elemento,Codice_ordine),
  CONSTRAINT FK_LibroComposizione
  FOREIGN KEY(Elemento) REFERENCES Libro(ISBN),
  CONSTRAINT FK_OrdineComposizione
  FOREIGN KEY(Codice_ordine) REFERENCES Ordine(Codice_univoco)
  );

CREATE TABLE Recensione (
  idUtente INT(10) UNSIGNED,
  Libro_ISBN BIGINT(13) UNSIGNED,
  DataInserimento DATE NOT NULL,
  Valutazione INT(1) NOT NULL,
  Commento VARCHAR(500) NOT NULL,
  PRIMARY KEY(idUtente,Libro_ISBN),
  CONSTRAINT FK_RecensioneLibro
  FOREIGN KEY(Libro_ISBN) REFERENCES Libro(ISBN),
  FOREIGN KEY(idUtente) REFERENCES Utente(Codice_identificativo)
  );

INSERT INTO Editore(ID,Nome) VALUES
(3000,'Newton Compton Editori'),
(3001,'Baldini & Castoldi'),
(3002,'Libreria Pienogiorno'),
(3003,'Meravigli'),
(3004,'Cairo'),
(3005,'Casa Editrice il Filo di Arianna'),
(3006,'Pellegrini'),
(3007,'Editore Ianieri'),
(3008,'Editore Curcio'),
(3009,'Mazzanti Libri'),
(3010,'Star Comics'),
(3011,'Mondadori'),
(3012,'Panini Comics'),
(3013,'Youcanprint'),
(3014,'Magazzini Salani'),
(3015,'Leggereditore'),
(3016,'Goen'),
(3017,'Giunti Editore'),
(3018,'Longanesi'),
(3019,'Grimaldi & C'),
(3020,'Time Crime'),
(3021,'Feltrinelli'),
(3022,'Loescher'),
(3023,'LSWR'),
(3024,'Gribaudo'),
(3025,'Zanichelli'),
(3026,'Independently Published'),
(3027,'Codice'),
(3028,'Piccin-Nuova Libraria'),
(3029,'Terre di Mezzo'),
(3030,'Rizzoli'),
(3031,'Hoepli'),
(3032,'Il Prato'),
(3033,'Carocci'),
(3034,'De Agostini Scuola'),
(3035,'Fabbri Editori'),
(3036,'LEG Edizioni'),
(3037,'Taschen'),
(3038,'Il Castello'),
(3039,'Bompiani'),
(3040,'La nave di Teseo');

INSERT INTO Libro(ISBN,Titolo,Editore,Pagine,Prezzo,Quantita,Data_Pubblicazione,Percorso) VALUES
(9788822760265,'La canzone romana.',3000,320,11.40,15,'2021-10-28','images/books/lacanzoneromana.jpg'),
(9788893884167,'Il poeta che non sa parlare',3001,256,17.10,2,'2021-10-14', 'images/books/ilpoetachenonsaparlare.jpg'),
(9791280229366,'La regina di Kabul',3002,173,16.02,4,'2021-11-17', 'images/books/lareginadikabul.jpg'),
(9788879554596,'Storia de Milan dal 1896',3003,176,18.05,16,'2021-10-11', 'images/books/storiademilan.jpg'),
(9788830901988,'Adrenalina.My untold stories',3004,272,18.05,7,'2021-18-02', 'images/books/adrenalina.jpg'),
(9791280034816,'Marzia,una sentenza già scritta?',3005,428,17.10,26,'2021-12-16', 'images/books/marziaunasentenca.jpg'),
(9791220500548,'Storie di Covid.Storie di persone.Per non dimenticare',3006,152,14.28,44,'2021-12-06', 'images/books/storiedicovid.jpg'),
(9791280022486,'La seconda lettera.Corrispondenza con un condannato a morte',3007,408,18.90,21,'2021-11-25', 'images/books/lasecondalettera.jpg'),
(9788868685768,'Alzati e ricomincia.La storia vera di Brunella Barbaro',3008,143,16.05,15,'2021-11-25', 'images/books/alzatiericomincia.jpg'),
(9788836210800,'Mestre e la guerra.Il secondo conflitto mondiale raccontato dai suoi testimoni',3009,209,19.00,2,'2021-10-20', 'images/books/mestreelaguerra.jpg'),
(9788822628541,'Dragon Ball Super',3010,192,4.50,3,'2022-01-07', 'images/books/dragonballsuper.jpg'),
(9788804728191,'Diabolik',3011,128,18.05,87,'2021-11-30', 'images/books/diabolik.jpg'),
(9788822628725,'One piece.Celebration edition.',3010,208,6.55,52,'2021-10-29', 'images/books/onepiececelebrationedition.jpg'),
(9788828764489,'Master Keaton.5',3012,344,14.15,45,'2021-12-30', 'images/books/masterkeaton.jpg'),
(9788828763604,'Jujutsu Kaisen.Sorcery Fight:Incidente di Shibuya',3012,200,4.90,36,'2021-11-04', 'images/books/jujutsukaisen11.jpg'),
(9788804742364,'Amore e ginnastica',3011,144,8.55,7,'2021-11-17', 'images/books/amoreeginastica.jpg'),
(9791220376419,'Quando le parlerai di me',3013,258,14.00,34,'2021-12-14', 'images/books/quandoleparleraidime.jpg'),
(9791259570321,'Nel modo in cui cade la neve',3014,480,16.05,78,'2022-01-13', 'images/books/nelmodoincuicadelaneve.jpg'),
(9788833751627,'Pazza di te',3015,352,14.15,21,'2022-01-07', 'images/books/pazzadite.jpg'),
(9788892841437,'Il sentiero dei fiori.12',3016,324,6.60,12,'2021-09-17', 'images/books/ilsentierodeifiori.jpg'),
(9788809763180,'Cime tempestose',3017,448,7.60,6,'2017-03-12', 'images/books/cimetempestose.jpg'),
(9788830453517,'La casa senza ricordi',3018,400,20.90,5,'2021-11-29', 'images/books/lacasasenzaricordi.jpg'),
(9788832063479,'Turbolo.Le avventurose storie in costiera.',3019,70,24.70,14,'2021-11-27', 'images/books/turbololeavventure.jpg'),
(9788866884095,'Alex Rider.1',3020,224,12.25,24,'2020-11-18', 'images/books/alexrider1.jpg'),
(9788804742319,'Per niente al mondo',3011,732,26.65,18,'2021-11-09', 'images/books/pernientealmondo.jpg'),
(9788807900846,'Orgoglio e Pregiudizio',3021,402,14.02,34,'2020-12-10', 'images/books/orgoglioepregiudizio.jpg'),
(9788807900693,'Il padre Goriot',3021,288,8.55,15,'2013-07-01', 'images/books/ilpadregoriot.jpg'),
(9788858333037,'Vocabolario della lingua latina in brossura',3022,2240,71.25,52,'2019-05-20', 'images/books/vocabolariodellalingualatina.jpg'),
(9788868957636,'Guarire con la medicina dolce',3023,272,18.90,13,'2020-04-08', 'images/books/guarireconlamedicinadolce.jpg'),
(9788858018460,'La grande enciclopedia del corpo umano',3024,208,20.90,6,'2017-10-19', 'images/books/lagrandeenciclopedia.jpg'),
(9788808153432,'Lo Zingarelli minore',3025,1440,31.50,8,'2021-01-01','images/books/lozingarelliminore.jpg'),
(9788809898059,'Il mio primo dizionario',3017,960,9.40,2,'2021-01-27', 'images/books/ilmioprimodizionario.jpg'),
(9798685585967,'Allena la tua salute',3026,182,16.90,16,'2020-11-19', 'images/books/allenalatuasalute.jpg'),
(9781092842280,'La motivazione',3026,54,7.99,15,'2019-04-05', 'images/books/lamotivazione.jpg'),
(9788875788049,'Sotto i ferri',3027,378,24.70,51,'2019-11-07', 'images/books/sottoiferri.jpg'),
(9798529792254,'Interpretazione dell''ECG',3026,156,18.99,4,'2021-07-01', 'images/books/interpretazioneedellecg.jpg'),
(9788829927302,'Aritmie cardiache',3028,615,47.50,1,'2015-06-24', 'images/books/aritmiecardiache.jpg'),
(9788861895034,'Buonanotte,coniglietto',3029,22,8.45,58,'2018-10-18', 'images/books/buonanotteconiglietto.jpg'),
(9788829929801,'Fisiopatologia del cuore',3028,480,42.75,100,'2019-04-01', 'images/books/fisiopatologiadelcuore.jpg'),
(9798482387986,'Sconfiggere la rabbia',3026,32,10.99,4,'2021-09-22', 'images/books/sconfiggerelarabbia.jpg'),
(9788817144988,'Vai all''inferno,Dante',3030,500,16.15,8,'2020-05-12', 'images/books/vaiallinfernodante.jpg'),
(9788820362713,'Il metodo di Warren Buffett.I segreti del piu grane investitore del mondo',3031,320,23.65,16,'2014-04-28', 'images/books/ilmetododiwarren.jpg'),
(9798650853428,'I 7 peccati finanziari.Fai luce sugli errorri finanziari piu comuni',3026,250,18.00,17,'2020-08-31', 'images/books/i7peccatifinanziari.jpg'),
(9798786890663,'METAVERSO:Investire nel trend del Futuro con azioni,ETF,Token e NFT',3026,166,19.66,18,'2021-12-18', 'images/books/metaverso.jpg'),
(9788863365474,'Nextgozio.Commercio al dettaglio nell''era digitale:quale futuro dopo il Covid-19',3032,208,23.65,22,'2021-03-29', 'images/books/nextgozio.jpg'),
(9788843088188,'Didattica speciale e inclusione scolastica',3033,435,35.15,85,'2017-12-05','images/books/didatticaspecialeeinclusionescolastica.jpg'),
(9788851157630,'Esatto!',3034,224,27.20,44,'2018-09-01', 'images/books/esatto.jpg'),
(9788891534613,'Autori e lettori',3035,1236,29.40,64,'2018-09-01', 'images/books/autorielettori.jpg'),
(9788891520180,'La parola alla storia',3035,209,6.65,87,'2016-07-01', 'images/books/laparolaallastoria.jpg'),
(9788808064851,'Analisi Matematica 1',3025,392,35.72,56,'2008-09-17', 'images/books/analisimatematica1.jpg'),
(9788861026223,'I Profetti dell''arte',3036,127,13.30,4,'2019-09-26', 'images/books/iprofettidellarte.jpg'),
(9783836589055,'Caravaggio',3037,512,19.00,2,'2021-09-14', 'images/books/caravaggio.jpg'),
(9783836559591,'Van Gogh.Tutti i dipinti',3037,744,15.20,44,'2015-06-16', 'images/books/vangogh.jpg'),
(9788865204528,'Dipingere con i colori acrilici',3038,48,6.17,15,'2014-06-02', 'images/books/dipingereconicolori.jpg'),
(9781675491300,'Riordinare casa in 10 minuti',3026,97,8.99,18,'2019-12-14', 'images/books/riordinarecasain10minuti.jpg'),
(9798709878938,'Lavorare a maglia',3026,128,11.99,77,'2021-03-12','images/books/lavorareamaglia.jpg'),
(9798455911064,'Fisica Quantistica',3026,135,23.97,98,'2021-08-21', 'images/books/fisicaquantistica.jpg'),
(9788834609484,'Filosofi in liberta',3040,224,11.40,14,'2022-01-07', 'images/books/filosofiainlibertà.jpg'),
(9798513926276,'Come smettere di Pensare Troppo',3026,139,24.53,11,'2021-06-02','images/books/comesmetteredipensaretroppo.jpg'),
(9788845278334,'La scienza della fantascienza',3039,544,23.75,2,'2015-01-15', 'images/books/lascienzadellafantascienza.jpg'),
(9798796912720,'Il figlio',3026,280,13.50,6,'2019-05-07', 'images/books/ilfiglio.jpg');

INSERT INTO Categoria(ID_Categoria,Nome) VALUES
(10,'Storia e Biografie'),
(11,'Fumetti e Manga'),
(12,'Classici e Romanzi'),
(13,'Avventura e Azione'),
(14,'Scuole ed Università'),
(15,'Arte e Tempo libero'),
(16,'Filosofia e Psicologia'),
(17,'Scienza e Fantascienza'),
(18,'Economia e Business'),
(19,'Dizionari ed Enciclopedie'),
(20,'Medicina e Salute'),
(21,'Bambini e Ragazzi');

INSERT INTO Autore(ID,Nome,Cognome) VALUES
(50000,'Elena',' Bonelli'),
(50001,'Nino',"D'Angelo"),
(50002,'Vauro',' Senesi'),
(50003,'Renato',' Manicardi'),
(50004,'Zlatan','Ibrahimovic'),
(50005,'Luigi','Garlando'),
(50006,'Sondra','Coggio'),
(50007,'Giovanna','Cristiano'),
(50008,'Laura','Bellotti'),
(50009,'Alessandra','Laricchia'),
(50010,'Umberto','Zane'),
(50011,'Akira','Toriyama'),
(50012,'Toyotaro','-'),
(50013,'Mondadori','-'),
(50014,'Eiichiro','Oda'),
(50015,'Naoki','Urasawa'),
(50016,'Hokusei','Katasushika'),
(50017,'Takashi','Nagasaki'),
(50018,'Gege','Akutami'),
(50019,'Edmondo','De Amicis'),
(50020,'Cinzia','De Martini'),
(50021,'Erin','Doom'),
(50022,'Sophia','Blakee'),
(50023,'Ako','Shimaki'),
(50024,'Emily','Bronte'),
(50025,'Donato','Carrisi'),
(50026,'Francesca','Moretti'),
(50027,'Anthony','Horowitz'),
(50028,'Ken','Follett'),
(50029,'Luigi','Castiglioni'),
(50030,'Scevoca','Mariotti'),
(50031,'Roberto','Mari'),
(50032,'Nicola','Zingarelli'),
(50033,'Sonia','Sferzi'),
(50034,'Alessandro','Solerio'),
(50035,'Stefano','Bonato'),
(50036,'Giovanni','Speranza'),
(50037,'Arnold','Van De Laar'),
(50038,'Nathan','Orwell'),
(50039,'Maurizio','Chiaranda'),
(50040,'Jane','Austen'),
(50041,'Honoré','De Balzac'),
(50042,'Umberto','Eco'),
(50043,'George','White'),
(50044,'Vittorio','Sgarbi'),
(50045,'Sebastian','Schutze'),
(50046,'Rainer','Metzen'),
(50047,'Wendy','Jelbert'),
(50048,'Nicola','Arcimboldi'),
(50049,'Virginia','Calm'),
(50050,'Leonard','S.Lilly'),
(50051,'Jorg','Mule'),
(50052,'Elizabeth','Cole'),
(50053,'Luigi','Garlando'),
(50054,'Lucio','Cottini'),
(50055,'Anna','Montemurro'),
(50056,'Rosetta','Zordan'),
(50057,'Barbara','Biggio'),
(50058,'Marco','Bramanti'),
(50059,'Pierpaolo','Amadeo'),
(50060,'Robert','G.Hagstrom'),
(50061,'Alessandro','Moretti'),
(50062,'Andrea','Maranti'),
(50063,'Manuel','Faè'),
(50064,'Patrizio','Bertin'),
(50065,'Renato','Giovannoli'),
(50066,'John','Henry');


INSERT INTO Pubblicazione(Libro_ISBN,Autore_ID) VALUES
(9788822760265,50000),
(9788893884167,50001),
(9791280229366,50002),
(9788879554596,50003),
(9788830901988,50004),
(9788830901988,50005),
(9791280034816,50006),
(9791220500548,50007),
(9791280022486,50008),
(9788868685768,50009),
(9788836210800,50010),
(9788822628541,50011),
(9788822628541,50012),
(9788804728191,50013),
(9788822628725,50014),
(9788828764489,50015),
(9788828764489,50016),
(9788828764489,50017),
(9788828763604,50018),
(9788804742364,50019),
(9791220376419,50020),
(9791259570321,50021),
(9788833751627,50022),
(9788892841437,50023),
(9788809763180,50024),
(9788830453517,50025),
(9788832063479,50026),
(9788866884095,50027),
(9788804742319,50028),
(9788807900846,50040),
(9788807900693,50041),
(9788858333037,50029),
(9788858333037,50030),
(9788868957636,50034),
(9788858018460,50033),
(9788808153432,50032),
(9788809898059,50031),
(9798685585967,50035),
(9781092842280,50036),
(9788875788049,50037),
(9798529792254,50038),
(9788829927302,50039),
(9788861895034,50051),
(9788829929801,50050),
(9798482387986,50052),
(9788817144988,50053),
(9788820362713,50060),
(9798650853428,50061),
(9798786890663,50062),
(9788863365474,50063),
(9788863365474,50064),
(9788843088188,50054),
(9788851157630,50055),
(9788891534613,50056),
(9788891520180,50057),
(9788808064851,50058),
(9788861026223,50044),
(9783836589055,50045),
(9783836559591,50046),
(9788865204528,50047),
(9781675491300,50048),
(9798709878938,50049),
(9798455911064,50059),
(9788834609484,50042),
(9798513926276,50043),
(9788845278334,50065),
(9798796912720,50066);


INSERT INTO Appartenenza(Libro_ISBN,Codice_Categoria) VALUES
(9788822760265,10),
(9788893884167,10),
(9791280229366,10),
(9788879554596,10),
(9788830901988,10),
(9788830901988,15),
(9791280034816,10),
(9791220500548,10),
(9791280022486,10),
(9788868685768,10),
(9788836210800,10),
(9788822628541,11),
(9788804728191,11),
(9788822628725,11),
(9788828764489,11),
(9788828763604,11),
(9788828763604,13),
(9788804742364,12),
(9788804742364,15),
(9791220376419,12),
(9791220376419,21),
(9791259570321,12),
(9788833751627,12),
(9788833751627,21),
(9788892841437,12),
(9788809763180,12),
(9788830453517,13),
(9788832063479,13),
(9788866884095,13),
(9788804742319,13),
(9788807900846,12),
(9788807900693,12),
(9788858333037,19),
(9788868957636,20),
(9788858018460,19),
(9788808153432,19),
(9788809898059,19),
(9798685585967,20),
(9798685585967,15),
(9781092842280,20),
(9788875788049,20),
(9798529792254,20),
(9798529792254,14),
(9788829927302,20),
(9788861895034,21),
(9788829929801,20),
(9788829929801,14),
(9798482387986,21),
(9788817144988,21),
(9788820362713,18),
(9788820362713,14),
(9798650853428,18),
(9798786890663,18),
(9788863365474,18),
(9788843088188,14),
(9788851157630,14),
(9788891534613,14),
(9788891520180,14),
(9788808064851,14),
(9788861026223,15),
(9783836589055,15),
(9783836559591,15),
(9788865204528,15),
(9788865204528,14),
(9781675491300,15),
(9798709878938,15),
(9798709878938,14),
(9798455911064,14),
(9788834609484,16),
(9798513926276,16),
(9798513926276,14),
(9788845278334,17),
(9798796912720,17);

INSERT INTO Utente(Codice_identificativo,Nome,Cognome,Data_nascita,Username,Email,Password,Telefono) VALUES
(1000000000,'Annalisa','Bianchi','2000-05-10','anna5','anabianchi42@gmail.com','milano10','1597863412'),
(1000000001,'Fiona','Rossi','1997-03-12','fior7','fiona12r@gmail.com','luna20','2548213745'),
(1000000002,'Andrea','Pavin','1989-08-15','pavn5','andapav89@gmail.com','millevisi8','5878134625'),
(1000000003,'Marco','Danielli','2005-11-28','dan28','marcodan@gmail.com','quarantaventi88','7225846192'),
(1000000004,'Lucia','Verdi','1964-08-20','luc64','luciaverdi12@gmail.com','fabbionicola2','9736182341'),
(1000000005,'Davide','Nosella','1995-11-14','nov15','davide95@gmail.com','barcelona88','4612546158'),
(1000000006,'Admin','admin','1999-12-09','admin','admin@gmail.com','admin1234','6380571935');

INSERT INTO Indirizzo(Codice,Via,Città,Cap,Num_civico,Utente) VALUES
(100000, 'Via Giambattista Belzoni', 'Padova', 35121 , 12, 1000000000),
(100001, 'Via Vittorio Veneto', 'Firenze', 50050, 8, 1000000001),
(100002, 'Via Chavanne', 'Aosta' , 11100, 23, 1000000002),
(100003, 'Via Dante', 'Venezia' , 30039 , 11, 1000000003),
(100004, 'Via Castiglione ', 'Bologna', 40125  , 91, 1000000004),
(100005, 'Via Rosa Salvador', 'Napoli' , 80135, 61, 1000000005),
(100006, 'Via Cortelonga' , 'Milano', 20900, 8, 1000000006);

 INSERT INTO Ordine(Codice_univoco,Cliente_Codice,Data,Data_partenza,Data_consegna,Indirizzo,Totale) VALUES 
 (50000000, 1000000000,'2019-07-22','2019-07-23','2019-07-25', 100000,65.25),
 (50000001, 1000000000,'2019-05-10','2019-05-11','2019-05-28', 100000,45.80),
 (50000002, 1000000002,'2018-11-11','2018-11-13','2018-11-24', 100002,84.50),
 (50000003, 1000000005,'2020-01-25','2022-01-26','2022-01-29',100005,96.60),
 (50000004, 1000000003,'2019-12-31','2020-01-02','2020-01-05',100003,84.12),
 (50000005, 1000000004,'2021-07-30','2021-08-02','2021-09-03',100004,104.50);

INSERT INTO Composizione(Elemento,Codice_ordine,Quantita) VALUES
(9788893884167,50000000,1),
(9788868685768,50000000,3),
(9788861026223,50000001,1),
(9798796912720,50000001,1),
(9783836589055,50000001,1),
(9788861895034,50000002,10),
(9788820362713,50000003,4),
(9788875788049,50000004,2),
(9788808064851,50000004,1),
(9788858018460,50000005,5);


INSERT INTO Recensione(idUtente,Libro_ISBN,DataInserimento,Valutazione,Commento) VALUES 
(1000000003,9783836589055, '2020-01-12', 4, "Bello"),
(1000000003,9788808064851, '2020-01-16', 3, "Carino..."),
(1000000003,9788820362713, '2020-02-20', 1, "Orribile, il mio falegname con 5 lire lo faceva meglio!!"),
(1000000003,9788858018460, '2020-02-26', 5, "Stupendo!!"),
(1000000004,9788861026223, '2020-04-15', 5, "OMG!! Emozionante!!"),
(1000000004,9788861895034, '2020-04-20', 2, "Noioso..."),
(1000000004,9783836589055, '2020-04-26', 3, "Non male!"),
(1000000004,9788808064851, '2020-01-16', 4, "Bellissimo libro ma il finale non mi ha convinto!"),
(1000000004,9788820362713, '2020-05-13', 1, "L'autore dovrebbe tornare a scuola..."),
(1000000004,9791220500548, '2020-06-11', 2, "Personaggi noiosi e dialoghi poveri."),
(1000000004,9791280022486, '2020-05-16', 4, "Entusiasmante!"),
(1000000004,9791280229366, '2020-07-05', 5, "Un classico della letteratura!"),
(1000000000,9798650853428,'2019-07-28',4,'Un libro scritto in modo semplice e scorrevole , con un racconto che serve da legame per accennare e approfondire diversi aspetti base della gestione della finanza personale con la quale bisogna fare i conti in diverse fasi della nostra vita. Il libro lo affronta con esempi e strategie applicabili ai nostri giorni.'),
(1000000000,9783836559591,'2019-06-01',2,'La storia di VAN GOGH è trattata molto bene e sono riportate le immagini delle sue opere. C''è scritto che sono riportate tutti i suoi dipinti. L''unico problema che il libro è in formato tascabile e le immagini sono piccole e alcune sono microscopiche quindi il libro l''ho utilizzato solo come spunto per poi cercare il dipinto su internet.
L''informazione utilissima è l''indicazione del luogo in cui si trovano i dipinti. In fondo è riportato l''elenco completo dei dipinti.'),
(1000000001,9788830453517,'2018-11-27',5 ,'L’ho letto tutto d’un fiato. Libro che ti tiene incollato alle pagine fino alla fine. L’unica pecca? È che ti lascia con un vuoto immenso! Leggetelo perché ne merita davvero la pena. MERAVIGLIOSO.'),
(1000000002,9788807900693,'2022-01-30',4,'Un classico che vedo più per una interpretazione teatrale piuttosto che per lettura vera e propria. Mi vedo i protagonisti meglio interpretati dal vivo è sicuramente è stato tradotto in tal senso. Io non seguo il teatro perchè preferisco l''opera lirica.'),
(1000000002,9788804728191,'2020-02-02',3 ,'La versione a fumetti del film dei Manetti!'),
(1000000002,9788858018460,'2021-09-13',2,' Arrivato perfettamente nei tempi e con una rilegatura forte e robusta. All''interno immagini vivide e colorate che invogliano a leggere. L''ho acquistato per mia figlia che sta studiando il corpo umano a scuola. Ha voluto subito portarlo a scuola per farlo vedere ai compagni. Acquisto azzeccato.');


ALTER TABLE Editore AUTO_INCREMENT=3041;
ALTER TABLE Categoria AUTO_INCREMENT=22;
ALTER TABLE Autore AUTO_INCREMENT=50067;
ALTER TABLE Utente AUTO_INCREMENT=1000000007;
ALTER TABLE Indirizzo AUTO_INCREMENT=100007;
ALTER TABLE Ordine AUTO_INCREMENT=50000006;
ALTER TABLE Recensione AUTO_INCREMENT=306;
