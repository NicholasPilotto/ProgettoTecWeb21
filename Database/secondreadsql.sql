-- MySQL Script generated by MySQL Workbench
-- Wed Jan  5 15:09:14 2022
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema SecondRead
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema SecondRead
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `SecondRead` DEFAULT CHARACTER SET utf8 ;
USE `SecondRead` ;

-- -----------------------------------------------------
-- Table `SecondRead`.`libro`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SecondRead`.`libro` (
  `isbn` BIGINT UNSIGNED NOT NULL,
  `titolo` VARCHAR(45) NOT NULL,
  `anno` DATETIME NOT NULL,
  `edizione` SMALLINT NOT NULL,
  `numero_pagine` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`isbn`),
  UNIQUE INDEX `isbn_UNIQUE` (`isbn` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SecondRead`.`autore`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SecondRead`.`autore` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NULL,
  `cognome` VARCHAR(45) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SecondRead`.`recensione`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SecondRead`.`recensione` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `valore` TINYINT NOT NULL,
  `libro_isbn` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`, `libro_isbn`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SecondRead`.`descrizione`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SecondRead`.`descrizione` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `testo` VARCHAR(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SecondRead`.`utente`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SecondRead`.`utente` (
  `cf` VARCHAR(16) NOT NULL,
  `nome` VARCHAR(45) NOT NULL,
  `cognome` VARCHAR(45) NOT NULL,
  `data_nascita` DATETIME NULL,
  `mail` VARCHAR(45) NOT NULL,
  `password` VARCHAR(64) NOT NULL,
  `credito` DECIMAL(9,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`cf`),
  UNIQUE INDEX `cf_UNIQUE` (`cf` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SecondRead`.`stock`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SecondRead`.`stock` (
  `isbn_libro` BIGINT NOT NULL,
  `cf_venditore` VARCHAR(16) NOT NULL,
  `elementi` INT NOT NULL,
  `prezzo` DECIMAL(3,2) NOT NULL,
  `usato` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`isbn_libro`, `cf_venditore`),
  CONSTRAINT `cf_venditore`
    FOREIGN KEY ()
    REFERENCES `SecondRead`.`utente` ()
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `isbn_libro`
    FOREIGN KEY ()
    REFERENCES `SecondRead`.`libro` ()
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SecondRead`.`indirizzo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SecondRead`.`indirizzo` (
  `via` VARCHAR(30) NOT NULL,
  `cap` SMALLINT NOT NULL,
  `civico` VARCHAR(10) NOT NULL,
  `paese` VARCHAR(45) NOT NULL,
  `id` INT NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SecondRead`.`pubblicazione`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SecondRead`.`pubblicazione` (
  `libro_isbn` BIGINT UNSIGNED NOT NULL,
  `autore_id` INT NOT NULL,
  PRIMARY KEY (`libro_isbn`, `autore_id`),
  INDEX `fk_libro_has_autore_autore1_idx` (`autore_id` ASC),
  INDEX `fk_libro_has_autore_libro_idx` (`libro_isbn` ASC),
  CONSTRAINT `fk_libro_has_autore_libro`
    FOREIGN KEY (`libro_isbn`)
    REFERENCES `SecondRead`.`libro` (`isbn`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_libro_has_autore_autore1`
    FOREIGN KEY (`autore_id`)
    REFERENCES `SecondRead`.`autore` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SecondRead`.`ordine`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SecondRead`.`ordine` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `indirizzo_id` INT NOT NULL,
  `data` DATETIME NOT NULL,
  `utente_cf` VARCHAR(16) NOT NULL,
  PRIMARY KEY (`id`, `indirizzo_id`, `utente_cf`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `fk_ordine_indirizzo1_idx` (`indirizzo_id` ASC),
  INDEX `fk_ordine_utente1_idx` (`utente_cf` ASC),
  CONSTRAINT `fk_ordine_indirizzo1`
    FOREIGN KEY (`indirizzo_id`)
    REFERENCES `SecondRead`.`indirizzo` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ordine_utente1`
    FOREIGN KEY (`utente_cf`)
    REFERENCES `SecondRead`.`utente` (`cf`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SecondRead`.`composizione`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SecondRead`.`composizione` (
  `stock_isbn_libro` BIGINT NOT NULL,
  `stock_cf_venditore` VARCHAR(16) NOT NULL,
  `ordine_id` INT NOT NULL,
  `totale` INT NOT NULL,
  `quantita` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`stock_isbn_libro`, `stock_cf_venditore`, `ordine_id`),
  INDEX `fk_stock_has_ordine_ordine1_idx` (`ordine_id` ASC),
  INDEX `fk_stock_has_ordine_stock1_idx` (`stock_isbn_libro` ASC, `stock_cf_venditore` ASC),
  CONSTRAINT `fk_stock_has_ordine_stock1`
    FOREIGN KEY (`stock_isbn_libro` , `stock_cf_venditore`)
    REFERENCES `SecondRead`.`stock` (`isbn_libro` , `cf_venditore`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_stock_has_ordine_ordine1`
    FOREIGN KEY (`ordine_id`)
    REFERENCES `SecondRead`.`ordine` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SecondRead`.`consegna`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SecondRead`.`consegna` (
  `indirizzo_id` INT NOT NULL,
  `utente_cf` VARCHAR(16) NOT NULL,
  PRIMARY KEY (`indirizzo_id`, `utente_cf`),
  INDEX `fk_indirizzo_has_utente_indirizzo1_idx` (`indirizzo_id` ASC),
  INDEX `fk_domiciliazione_venditore1_idx` (`utente_cf` ASC),
  CONSTRAINT `fk_indirizzo_has_utente_indirizzo1`
    FOREIGN KEY (`indirizzo_id`)
    REFERENCES `SecondRead`.`indirizzo` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_domiciliazione_venditore1`
    FOREIGN KEY (`utente_cf`)
    REFERENCES `SecondRead`.`utente` (`cf`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SecondRead`.`ritiro`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SecondRead`.`ritiro` (
  `venditore_cf` VARCHAR(16) NOT NULL,
  `indirizzo_id` INT NOT NULL,
  PRIMARY KEY (`venditore_cf`, `indirizzo_id`),
  INDEX `fk_venditore_has_indirizzo_indirizzo1_idx` (`indirizzo_id` ASC),
  INDEX `fk_venditore_has_indirizzo_venditore1_idx` (`venditore_cf` ASC),
  CONSTRAINT `fk_venditore_has_indirizzo_venditore1`
    FOREIGN KEY (`venditore_cf`)
    REFERENCES `SecondRead`.`utente` (`cf`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_venditore_has_indirizzo_indirizzo1`
    FOREIGN KEY (`indirizzo_id`)
    REFERENCES `SecondRead`.`indirizzo` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SecondRead`.`recensione_libro`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SecondRead`.`recensione_libro` (
  `recensione_id` INT NOT NULL,
  `libro_isbn` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`recensione_id`, `libro_isbn`),
  INDEX `fk_recensione_has_libro_libro1_idx` (`libro_isbn` ASC),
  INDEX `fk_recensione_has_libro_recensione1_idx` (`recensione_id` ASC),
  CONSTRAINT `fk_recensione_has_libro_recensione1`
    FOREIGN KEY (`recensione_id`)
    REFERENCES `SecondRead`.`recensione` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_recensione_has_libro_libro1`
    FOREIGN KEY (`libro_isbn`)
    REFERENCES `SecondRead`.`libro` (`isbn`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SecondRead`.`recensione_venditore`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SecondRead`.`recensione_venditore` (
  `recensione_id` INT NOT NULL,
  `utente_cf` VARCHAR(16) NOT NULL,
  `descrizione_id` INT NOT NULL,
  PRIMARY KEY (`recensione_id`, `utente_cf`, `descrizione_id`),
  INDEX `fk_recensione_has_venditore_venditore1_idx` (`utente_cf` ASC),
  INDEX `fk_recensione_has_venditore_recensione1_idx` (`recensione_id` ASC),
  INDEX `fk_recensione_has_venditore_descrizione1_idx` (`descrizione_id` ASC),
  CONSTRAINT `fk_recensione_has_venditore_recensione1`
    FOREIGN KEY (`recensione_id`)
    REFERENCES `SecondRead`.`recensione` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_recensione_has_venditore_venditore1`
    FOREIGN KEY (`utente_cf`)
    REFERENCES `SecondRead`.`utente` (`cf`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_recensione_has_venditore_descrizione1`
    FOREIGN KEY (`descrizione_id`)
    REFERENCES `SecondRead`.`descrizione` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SecondRead`.`foto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SecondRead`.`foto` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `path` VARCHAR(50) NULL,
  `stock_isbn_libro` BIGINT NOT NULL,
  `stock_cf_venditore` VARCHAR(16) NOT NULL,
  PRIMARY KEY (`id`, `stock_isbn_libro`, `stock_cf_venditore`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `fk_foto_stock1_idx` (`stock_isbn_libro` ASC, `stock_cf_venditore` ASC),
  CONSTRAINT `fk_foto_stock1`
    FOREIGN KEY (`stock_isbn_libro` , `stock_cf_venditore`)
    REFERENCES `SecondRead`.`stock` (`isbn_libro` , `cf_venditore`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SecondRead`.`fatturazione`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SecondRead`.`fatturazione` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `totale` DECIMAL(9,2) NOT NULL,
  `data` DATETIME NOT NULL,
  `utente_cf` VARCHAR(16) NOT NULL,
  PRIMARY KEY (`id`, `utente_cf`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `fk_liquidazione_venditore1_idx` (`utente_cf` ASC),
  CONSTRAINT `fk_liquidazione_venditore1`
    FOREIGN KEY (`utente_cf`)
    REFERENCES `SecondRead`.`utente` (`cf`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SecondRead`.`tag`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SecondRead`.`tag` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tipo` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SecondRead`.`tipologia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SecondRead`.`tipologia` (
  `libro_isbn` BIGINT UNSIGNED NOT NULL,
  `tag_id` INT NOT NULL,
  PRIMARY KEY (`libro_isbn`, `tag_id`),
  INDEX `fk_libro_has_tag_tag1_idx` (`tag_id` ASC),
  INDEX `fk_libro_has_tag_libro1_idx` (`libro_isbn` ASC),
  CONSTRAINT `fk_libro_has_tag_libro1`
    FOREIGN KEY (`libro_isbn`)
    REFERENCES `SecondRead`.`libro` (`isbn`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_libro_has_tag_tag1`
    FOREIGN KEY (`tag_id`)
    REFERENCES `SecondRead`.`tag` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SecondRead`.`carta_pagamento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SecondRead`.`carta_pagamento` (
  `codice` INT NOT NULL,
  `scadenza` DATETIME NOT NULL,
  `nome` VARCHAR(45) NOT NULL,
  `cognome` VARCHAR(45) NOT NULL,
  `cvv` TINYINT NOT NULL,
  `id` INT NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SecondRead`.`metodo_pagameno`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SecondRead`.`metodo_pagameno` (
  `carta_pagamento_id` INT NOT NULL,
  `utente_cf` VARCHAR(16) NOT NULL,
  PRIMARY KEY (`carta_pagamento_id`, `utente_cf`),
  INDEX `fk_carta_pagamento_has_utente_utente1_idx` (`utente_cf` ASC),
  INDEX `fk_carta_pagamento_has_utente_carta_pagamento1_idx` (`carta_pagamento_id` ASC),
  CONSTRAINT `fk_carta_pagamento_has_utente_carta_pagamento1`
    FOREIGN KEY (`carta_pagamento_id`)
    REFERENCES `SecondRead`.`carta_pagamento` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_carta_pagamento_has_utente_utente1`
    FOREIGN KEY (`utente_cf`)
    REFERENCES `SecondRead`.`utente` (`cf`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
