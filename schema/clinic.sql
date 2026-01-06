-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema clinic
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema clinic
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `clinic` DEFAULT CHARACTER SET utf8 ;
USE `clinic` ;

-- -----------------------------------------------------
-- Table `clinic`.`patient`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `clinic`.`patient` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(45) NOT NULL,
  `first_name` VARCHAR(45) NOT NULL,
  `father_name` VARCHAR(45) NULL,
  `last_name` VARCHAR(45) NULL,
  `age` INT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  UNIQUE INDEX `code_UNIQUE` (`code` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `clinic`.`prescription`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `clinic`.`prescription` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `patient_cc` VARCHAR(355) NULL,
  `patient_past_history` VARCHAR(345) NULL,
  `patient_pb` VARCHAR(45) NULL,
  `patient_pr` VARCHAR(45) NULL,
  `patient_rr` VARCHAR(45) NULL,
  `patient_weight` VARCHAR(45) NULL,
  `doctor_diagnose` VARCHAR(345) NULL,
  `doctor_clinical_note` VARCHAR(350) NULL,
  `patient_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  INDEX `fk_prescription_patient1_idx` (`patient_id` ASC) ,
  CONSTRAINT `fk_prescription_patient1`
    FOREIGN KEY (`patient_id`)
    REFERENCES `clinic`.`patient` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `clinic`.`medicine_type`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `clinic`.`medicine_type` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `is_deleted` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `clinic`.`medicine_category`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `clinic`.`medicine_category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `is_deleted` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `clinic`.`medicine`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `clinic`.`medicine` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `generic_name` VARCHAR(45) NOT NULL,
  `company_name` VARCHAR(45) NULL,
  `dose` VARCHAR(45) NULL,
  `usage_description` VARCHAR(255) NULL,
  `is_deleted` TINYINT NULL DEFAULT 0,
  `medicine_type_id` INT UNSIGNED NOT NULL,
  `medicine_category_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  INDEX `fk_medicine_medicine_type_idx` (`medicine_type_id` ASC) ,
  INDEX `fk_medicine_medicine_category1_idx` (`medicine_category_id` ASC) ,
  CONSTRAINT `fk_medicine_medicine_type`
    FOREIGN KEY (`medicine_type_id`)
    REFERENCES `clinic`.`medicine_type` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_medicine_medicine_category1`
    FOREIGN KEY (`medicine_category_id`)
    REFERENCES `clinic`.`medicine_category` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `clinic`.`prescribed_medicine`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `clinic`.`prescribed_medicine` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `medicine_total_usage` TINYINT NOT NULL,
  `medicine_usage_frequency` VARCHAR(45) NOT NULL,
  `medicine_usage_form` VARCHAR(45) NOT NULL,
  `medicine_doctor_note` VARCHAR(345) NOT NULL,
  `prescription_id` INT UNSIGNED NOT NULL,
  `medicine_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  INDEX `fk_prescribed_medicine_prescription1_idx` (`prescription_id` ASC) ,
  INDEX `fk_prescribed_medicine_medicine1_idx` (`medicine_id` ASC) ,
  CONSTRAINT `fk_prescribed_medicine_prescription1`
    FOREIGN KEY (`prescription_id`)
    REFERENCES `clinic`.`prescription` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_prescribed_medicine_medicine1`
    FOREIGN KEY (`medicine_id`)
    REFERENCES `clinic`.`medicine` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
