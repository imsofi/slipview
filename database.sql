-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema slipview
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema slipview
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `slipview` DEFAULT CHARACTER SET utf8 ;
USE `slipview` ;

-- -----------------------------------------------------
-- Table `slipview`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `slipview`.`user` (
  `userid` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(64) NOT NULL,
  `password` VARCHAR(250) NOT NULL,
  `last_login` DATETIME NULL,
  `is_logged_in` TINYINT(1) NULL,
  PRIMARY KEY (`userid`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `slipview`.`series`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `slipview`.`series` (
  `seriesid` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(80) NOT NULL,
  `desc` TEXT(200) NOT NULL,
  `creator` INT NOT NULL,
  `popularity` INT(3) NOT NULL,
  PRIMARY KEY (`seriesid`),
  INDEX `fk_series_user1_idx` (`creator` ASC),
  CONSTRAINT `fk_series_user1`
    FOREIGN KEY (`creator`)
    REFERENCES `slipview`.`user` (`userid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `slipview`.`video`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `slipview`.`video` (
  `videoid` INT NOT NULL AUTO_INCREMENT,
  `episodename` VARCHAR(50) NOT NULL,
  `episodedesc` TEXT(200) NOT NULL,
  `episode` INT(2) NOT NULL,
  `season` INT(2) NOT NULL,
  `views` INT NOT NULL,
  `inseries` INT NOT NULL,
  PRIMARY KEY (`videoid`),
  INDEX `fk_video_playlist_idx` (`inseries` ASC),
  CONSTRAINT `fk_video_playlist`
    FOREIGN KEY (`inseries`)
    REFERENCES `slipview`.`series` (`seriesid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `slipview`.`categorylist`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `slipview`.`categorylist` (
  `category` CHAR(2) NOT NULL,
  `categoryname` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`category`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `slipview`.`categories`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `slipview`.`categories` (
  `categoryid` INT NOT NULL AUTO_INCREMENT,
  `inseries` INT NOT NULL,
  `categoryid` CHAR(2) NOT NULL,
  PRIMARY KEY (`categoryid`),
  INDEX `fk_category_playlist1_idx` (`inseries` ASC),
  INDEX `fk_category_table11_idx` (`categoryid` ASC),
  CONSTRAINT `fk_category_playlist1`
    FOREIGN KEY (`inseries`)
    REFERENCES `slipview`.`series` (`seriesid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_category_table11`
    FOREIGN KEY (`categoryid`)
    REFERENCES `slipview`.`categorylist` (`category`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `slipview`.`comment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `slipview`.`comment` (
  `idcomment` INT NOT NULL AUTO_INCREMENT,
  `videoid` INT NOT NULL,
  `userid` INT NOT NULL,
  `date` DATETIME NOT NULL,
  `text` TEXT(200) NOT NULL,
  PRIMARY KEY (`idcomment`),
  INDEX `fk_comment_video1_idx` (`videoid` ASC),
  INDEX `fk_comment_user1_idx` (`userid` ASC),
  CONSTRAINT `fk_comment_video1`
    FOREIGN KEY (`videoid`)
    REFERENCES `slipview`.`video` (`videoid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_comment_user1`
    FOREIGN KEY (`userid`)
    REFERENCES `slipview`.`user` (`userid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

