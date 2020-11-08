<?php

namespace Pbl\Core\Banco\Schema;

use Pimple\Container;

class Mysql {

    public const versao = 1;
    private $container;
    
    public function __construct($container)
    {
        $this->container = $container;
    }

    private function versao1()
    {
        $tabs = $this->container["config"]->bd["tabelas"];

        // $this->container["db_con"]->getCon()->beginTrans();
        // classe
        $sql = " CREATE TABLE IF NOT EXISTS ".$tabs["classe"]["nome"]."2 ( "
            . " ".$tabs["classe"]["colunas"]["cod_classe"]." INT NOT NULL AUTO_INCREMENT, "
            . " ".$tabs["classe"]["colunas"]["nome"]." VARCHAR(100) NOT NULL, "
            . " ".$tabs["classe"]["colunas"]["prefixo"]." VARCHAR(100) NOT NULL, "
            . " ".$tabs["classe"]["colunas"]["descricao"]." VARCHAR(255) NULL, "
            . " ".$tabs["classe"]["colunas"]["temfilhos"]." SMALLINT NOT NULL DEFAULT 0, "
            . " ".$tabs["classe"]["colunas"]["sistema"]." SMALLINT NOT NULL DEFAULT 0, "
            . " ".$tabs["classe"]["colunas"]["indexar"]." SMALLINT NOT NULL , "
            . " PRIMARY KEY (".$tabs["classe"]["colunas"]["cod_classe"].")) "
            . " ENGINE = InnoDB; ".PHP_EOL;
        
        // status
        $sql = " CREATE TABLE IF NOT EXISTS ".$tabs["status"]["nome"]." ( "
            . " ".$tabs["status"]["colunas"]["cod_status"]." INT NOT NULL AUTO_INCREMENT, "
            . " ".$tabs["status"]["colunas"]["nome"]." VARCHAR(50) NOT NULL, "
            . " PRIMARY KEY (".$tabs["status"]["colunas"]["cod_status"].")) "
            . " ENGINE = InnoDB; ".PHP_EOL;

        // usuario
        $sql = " CREATE TABLE IF NOT EXISTS ".$tabs["usuario"]["nome"]." ( "
            . " ".$tabs["usuario"]["colunas"]["cod_usuario"]." INT NOT NULL AUTO_INCREMENT, "
            . " ".$tabs["usuario"]["colunas"]["secao"]." VARCHAR(255) NULL, "
            . " ".$tabs["usuario"]["colunas"]["nome"]." VARCHAR(255) NULL, "
            . " ".$tabs["usuario"]["colunas"]["login"]." VARCHAR(50) NOT NULL, "
            . " ".$tabs["usuario"]["colunas"]["email"]." VARCHAR(255) NOT NULL, "
            . " ".$tabs["usuario"]["colunas"]["ramal"]." VARCHAR(50) NULL, "
            . " ".$tabs["usuario"]["colunas"]["senha"]." VARCHAR(50) NOT NULL, "
            . " ".$tabs["usuario"]["colunas"]["chefia"]." INT NULL, "
            . " ".$tabs["usuario"]["colunas"]["valido"]." SMALLINT NOT NULL DEFAULT 0, "
            . " ".$tabs["usuario"]["colunas"]["data_atualizacao"]." BIGINT NULL, "
            . " ".$tabs["usuario"]["colunas"]["altera_senha"]." SMALLINT NOT NULL DEFAULT 0, "
            . " ".$tabs["usuario"]["colunas"]["ldap"]." SMALLINT NOT NULL DEFAULT 0, "
            . " PRIMARY KEY (".$tabs["usuario"]["colunas"]["cod_usuario"].")) "
            . " ENGINE = InnoDB; ".PHP_EOL;

        $this->container["db_con"]->getCon()->Execute($sql);
        xd($sql);

        /*



-- -----------------------------------------------------
-- Table `pele`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pele` (
  `cod_pele` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(50) NOT NULL,
  `prefixo` VARCHAR(50) NULL,
  `publica` SMALLINT NULL,
  PRIMARY KEY (`cod_pele`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `objeto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `objeto` (
  `cod_objeto` INT NOT NULL AUTO_INCREMENT,
  `cod_pai` INT NULL,
  `cod_classe` INT NOT NULL,
  `cod_usuario` INT NOT NULL,
  `cod_pele` INT NULL,
  `cod_status` INT NOT NULL,
  `titulo` VARCHAR(255) NOT NULL,
  `descricao` TEXT NULL,
  `data_publicacao` BIGINT NOT NULL,
  `data_validade` BIGINT NOT NULL,
  `script_exibir` VARCHAR(255) NULL,
  `apagado` SMALLINT NOT NULL DEFAULT 1,
  `peso` INT NOT NULL DEFAULT 0,
  `data_exclusao` BIGINT NULL,
  `url_amigavel` VARCHAR(255) NULL,
  `versao` INT NOT NULL,
  `versao_publicada` INT NULL,
  PRIMARY KEY (`cod_objeto`),
  CONSTRAINT `fk_objeto_classe`
    FOREIGN KEY (`cod_classe`)
    REFERENCES `classe` (`cod_classe`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_objeto_status`
    FOREIGN KEY (`cod_status`)
    REFERENCES `status` (`cod_status`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_objeto_usuario`
    FOREIGN KEY (`cod_usuario`)
    REFERENCES `usuario` (`cod_usuario`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_objeto_pele`
    FOREIGN KEY (`cod_pele`)
    REFERENCES `pele` (`cod_pele`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_objeto_classe_idx` ON `objeto` (`cod_classe` ASC) VISIBLE;

CREATE INDEX `fk_objeto_status_idx` ON `objeto` (`cod_status` ASC) VISIBLE;

CREATE INDEX `fk_objeto_usuario_idx` ON `objeto` (`cod_usuario` ASC) VISIBLE;

CREATE INDEX `fk_objeto_pele_idx` ON `objeto` (`cod_pele` ASC) VISIBLE;


-- -----------------------------------------------------
-- Table `classexfilhos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `classexfilhos` (
  `cod_classe` INT NOT NULL,
  `cod_classe_filho` INT NOT NULL,
  PRIMARY KEY (`cod_classe`, `cod_classe_filho`),
  CONSTRAINT `fk_classexfilhos_classe`
    FOREIGN KEY (`cod_classe`)
    REFERENCES `classe` (`cod_classe`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_classexfilhos_classe_filho`
    FOREIGN KEY (`cod_classe_filho`)
    REFERENCES `classe` (`cod_classe`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `fk_classexfilhos_classe_filho_idx` ON `classexfilhos` (`cod_classe_filho` ASC) VISIBLE;


-- -----------------------------------------------------
-- Table `tipodado`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tipodado` (
  `cod_tipodado` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(50) NOT NULL,
  `tabela` VARCHAR(50) NOT NULL,
  `delimitador` VARCHAR(1) NULL,
  PRIMARY KEY (`cod_tipodado`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `propriedade`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `propriedade` (
  `cod_propriedade` INT NOT NULL AUTO_INCREMENT,
  `cod_classe` INT NOT NULL,
  `cod_tipodado` INT NOT NULL,
  `cod_referencia_classe` INT NULL,
  `campo_ref` VARCHAR(50) NULL,
  `nome` VARCHAR(50) NULL,
  `posicao` INT NULL,
  `descricao` VARCHAR(255) NULL,
  `rotulo` VARCHAR(50) NULL,
  `rot1booleano` VARCHAR(50) NULL,
  `rot2booleano` VARCHAR(50) NULL,
  `obrigatorio` SMALLINT NULL,
  `seguranca` INT NULL,
  `valorpadrao` VARCHAR(255) NULL,
  PRIMARY KEY (`cod_propriedade`),
  CONSTRAINT `fk_propriedade_classe`
    FOREIGN KEY (`cod_classe`)
    REFERENCES `classe` (`cod_classe`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_propriedade_tipodado`
    FOREIGN KEY (`cod_tipodado`)
    REFERENCES `tipodado` (`cod_tipodado`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `fk_propriedade_classe_idx` ON `propriedade` (`cod_classe` ASC) VISIBLE;

CREATE INDEX `fk_propriedade_tipodado_idx` ON `propriedade` (`cod_tipodado` ASC) VISIBLE;


-- -----------------------------------------------------
-- Table `tag`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tag` (
  `cod_tag` INT NOT NULL AUTO_INCREMENT,
  `nome_tag` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`cod_tag`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tagxobjeto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tagxobjeto` (
  `cod_tagxobjeto` INT NOT NULL AUTO_INCREMENT,
  `cod_tag` INT NOT NULL,
  `cod_objeto` INT NOT NULL,
  PRIMARY KEY (`cod_tagxobjeto`),
  CONSTRAINT `fk_tagxobjeto_tag`
    FOREIGN KEY (`cod_tag`)
    REFERENCES `tag` (`cod_tag`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tagxobjeto_objeto`
    FOREIGN KEY (`cod_objeto`)
    REFERENCES `objeto` (`cod_objeto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_tagxobjeto_tag_idx` ON `tagxobjeto` (`cod_tag` ASC) VISIBLE;

CREATE INDEX `fk_tagxobjeto_objeto_idx` ON `tagxobjeto` (`cod_objeto` ASC) VISIBLE;


-- -----------------------------------------------------
-- Table `tbl_blob`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_blob` (
  `cod_blob` INT NOT NULL AUTO_INCREMENT,
  `cod_obeto` INT NOT NULL,
  `cod_propriedade` INT NOT NULL,
  `arquivo` VARCHAR(255) NOT NULL,
  `tamanho` BIGINT NOT NULL,
  PRIMARY KEY (`cod_blob`),
  CONSTRAINT `fk_blob_objeto`
    FOREIGN KEY (`cod_obeto`)
    REFERENCES `objeto` (`cod_objeto`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_blob_propriedade`
    FOREIGN KEY (`cod_propriedade`)
    REFERENCES `propriedade` (`cod_propriedade`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `fk_blob_objeto_idx` ON `tbl_blob` (`cod_obeto` ASC) VISIBLE;

CREATE INDEX `fk_blob_propriedade_idx` ON `tbl_blob` (`cod_propriedade` ASC) VISIBLE;


-- -----------------------------------------------------
-- Table `tbl_boolean`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_boolean` (
  `cod_boolean` INT NOT NULL AUTO_INCREMENT,
  `cod_objeto` INT NOT NULL,
  `cod_propriedade` INT NOT NULL,
  `valor` SMALLINT NULL,
  PRIMARY KEY (`cod_boolean`),
  CONSTRAINT `fk_boolean_objeto`
    FOREIGN KEY (`cod_objeto`)
    REFERENCES `objeto` (`cod_objeto`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_boolean_propriedade`
    FOREIGN KEY (`cod_propriedade`)
    REFERENCES `propriedade` (`cod_propriedade`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `fk_boolean_objeto_idx` ON `tbl_boolean` (`cod_objeto` ASC) VISIBLE;

CREATE INDEX `fk_boolean_propriedade_idx` ON `tbl_boolean` (`cod_propriedade` ASC) VISIBLE;


-- -----------------------------------------------------
-- Table `tbl_date`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_date` (
  `cod_date` INT NOT NULL AUTO_INCREMENT,
  `cod_objeto` INT NOT NULL,
  `cod_propriedade` INT NOT NULL,
  `valor` BIGINT NULL,
  PRIMARY KEY (`cod_date`),
  CONSTRAINT `fk_date_objeto`
    FOREIGN KEY (`cod_objeto`)
    REFERENCES `objeto` (`cod_objeto`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_date_propriedade`
    FOREIGN KEY (`cod_propriedade`)
    REFERENCES `propriedade` (`cod_propriedade`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `fk_date_objeto_idx` ON `tbl_date` (`cod_objeto` ASC) VISIBLE;

CREATE INDEX `fk_date_propriedade_idx` ON `tbl_date` (`cod_propriedade` ASC) VISIBLE;


-- -----------------------------------------------------
-- Table `tbl_float`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_float` (
  `cod_float` INT NOT NULL AUTO_INCREMENT,
  `cod_objeto` INT NOT NULL,
  `cod_propriedade` INT NOT NULL,
  `valor` DOUBLE NULL,
  PRIMARY KEY (`cod_float`),
  CONSTRAINT `fk_float_objeto`
    FOREIGN KEY (`cod_objeto`)
    REFERENCES `objeto` (`cod_objeto`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_float_propriedade`
    FOREIGN KEY (`cod_propriedade`)
    REFERENCES `propriedade` (`cod_propriedade`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `fk_float_objeto_idx` ON `tbl_float` (`cod_objeto` ASC) VISIBLE;

CREATE INDEX `fk_float_propriedade_idx` ON `tbl_float` (`cod_propriedade` ASC) VISIBLE;


-- -----------------------------------------------------
-- Table `tbl_integer`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_integer` (
  `cod_integer` INT NOT NULL AUTO_INCREMENT,
  `cod_objeto` INT NOT NULL,
  `cod_propriedade` INT NOT NULL,
  `valor` INT NULL,
  PRIMARY KEY (`cod_integer`),
  CONSTRAINT `fk_integer_objeto`
    FOREIGN KEY (`cod_objeto`)
    REFERENCES `objeto` (`cod_objeto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_integer_propriedade`
    FOREIGN KEY (`cod_propriedade`)
    REFERENCES `propriedade` (`cod_propriedade`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_integer_objeto_idx` ON `tbl_integer` (`cod_objeto` ASC) VISIBLE;

CREATE INDEX `fk_integer_propriedade_idx` ON `tbl_integer` (`cod_propriedade` ASC) VISIBLE;


-- -----------------------------------------------------
-- Table `tbl_objref`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_objref` (
  `cod_objref` INT NOT NULL AUTO_INCREMENT,
  `cod_objeto` INT NOT NULL,
  `cod_propriedade` INT NOT NULL,
  `valor` INT NULL,
  PRIMARY KEY (`cod_objref`),
  CONSTRAINT `fk_objref_objeto`
    FOREIGN KEY (`cod_objeto`)
    REFERENCES `objeto` (`cod_objeto`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_objref_propriedade`
    FOREIGN KEY (`cod_propriedade`)
    REFERENCES `propriedade` (`cod_propriedade`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `fk_objref_objeto_idx` ON `tbl_objref` (`cod_objeto` ASC) VISIBLE;

CREATE INDEX `fk_objref_propriedade_idx` ON `tbl_objref` (`cod_propriedade` ASC) VISIBLE;


-- -----------------------------------------------------
-- Table `tbl_string`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_string` (
  `cod_string` INT NOT NULL AUTO_INCREMENT,
  `cod_objeto` INT NOT NULL,
  `cod_propriedade` INT NOT NULL,
  `valor` VARCHAR(1000) NULL,
  PRIMARY KEY (`cod_string`),
  CONSTRAINT `fk_string_objeto`
    FOREIGN KEY (`cod_objeto`)
    REFERENCES `objeto` (`cod_objeto`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_string_propriedade`
    FOREIGN KEY (`cod_propriedade`)
    REFERENCES `propriedade` (`cod_propriedade`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `fk_string_objeto_idx` ON `tbl_string` (`cod_objeto` ASC) VISIBLE;

CREATE INDEX `fk_string_propriedade_idx` ON `tbl_string` (`cod_propriedade` ASC) VISIBLE;


-- -----------------------------------------------------
-- Table `tbl_text`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_text` (
  `cod_text` INT NOT NULL AUTO_INCREMENT,
  `cod_objeto` INT NOT NULL,
  `cod_propriedade` INT NOT NULL,
  `valor` TEXT NULL,
  PRIMARY KEY (`cod_text`),
  CONSTRAINT `fk_text_objeto`
    FOREIGN KEY (`cod_objeto`)
    REFERENCES `objeto` (`cod_objeto`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_text_propriedaed`
    FOREIGN KEY (`cod_propriedade`)
    REFERENCES `propriedade` (`cod_propriedade`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `fk_text_objeto_idx` ON `tbl_text` (`cod_objeto` ASC) VISIBLE;

CREATE INDEX `fk_text_propriedaed_idx` ON `tbl_text` (`cod_propriedade` ASC) VISIBLE;


-- -----------------------------------------------------
-- Table `perfil`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `perfil` (
  `cod_perfil` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`cod_perfil`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `objetoxusuarioxperfil`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `objetoxusuarioxperfil` (
  `cod_usuario` INT NOT NULL,
  `cod_objeto` INT NOT NULL,
  `cod_perfil` INT NOT NULL,
  PRIMARY KEY (`cod_usuario`, `cod_objeto`, `cod_perfil`),
  CONSTRAINT `fk_objetoxusuarioxperfil_objeto`
    FOREIGN KEY (`cod_objeto`)
    REFERENCES `objeto` (`cod_objeto`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_objetoxusuarioxperfil_usuario`
    FOREIGN KEY (`cod_usuario`)
    REFERENCES `usuario` (`cod_usuario`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_objetoxusuarioxperfil_perfil`
    FOREIGN KEY (`cod_perfil`)
    REFERENCES `perfil` (`cod_perfil`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `fk_objetoxusuarioxperfil_objeto_idx` ON `objetoxusuarioxperfil` (`cod_objeto` ASC) VISIBLE;

CREATE INDEX `fk_objetoxusuarioxperfil_perfil_idx` ON `objetoxusuarioxperfil` (`cod_perfil` ASC) VISIBLE;


-- -----------------------------------------------------
-- Table `pilha`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pilha` (
  `cod_pilha` INT NOT NULL AUTO_INCREMENT,
  `cod_objeto` INT NOT NULL,
  `cod_usuario` INT NOT NULL,
  `cod_tipo` INT NULL,
  `datahora` BIGINT NULL,
  PRIMARY KEY (`cod_pilha`),
  CONSTRAINT `fk_pilha_objeto`
    FOREIGN KEY (`cod_objeto`)
    REFERENCES `objeto` (`cod_objeto`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_pilha_usuario`
    FOREIGN KEY (`cod_usuario`)
    REFERENCES `usuario` (`cod_usuario`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `fk_pilha_objeto_idx` ON `pilha` (`cod_objeto` ASC) VISIBLE;

CREATE INDEX `fk_pilha_usuario_idx` ON `pilha` (`cod_usuario` ASC) VISIBLE;


-- -----------------------------------------------------
-- Table `classexobjeto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `classexobjeto` (
  `cod_classe` INT NOT NULL,
  `cod_objeto` INT NOT NULL,
  PRIMARY KEY (`cod_classe`, `cod_objeto`),
  CONSTRAINT `fk_classexobjeto_classe`
    FOREIGN KEY (`cod_classe`)
    REFERENCES `classe` (`cod_classe`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_classexobjeto_objeto`
    FOREIGN KEY (`cod_objeto`)
    REFERENCES `objeto` (`cod_objeto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_classexobjeto_objeto_idx` ON `classexobjeto` (`cod_objeto` ASC) VISIBLE;


-- -----------------------------------------------------
-- Table `infoperfil`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `infoperfil` (
  `cod_infoperfil` INT NOT NULL AUTO_INCREMENT,
  `cod_perfil` INT NOT NULL,
  `acao` VARCHAR(255) NULL,
  `script` VARCHAR(255) NULL,
  `donooupublicado` SMALLINT NULL,
  `sopublicado` SMALLINT NULL,
  `sodono` SMALLINT NULL,
  `naomenu` SMALLINT NULL,
  `ordem` INT NULL,
  `icone` VARCHAR(50) NULL,
  PRIMARY KEY (`cod_infoperfil`),
  CONSTRAINT `fk_infoperfil_perfil`
    FOREIGN KEY (`cod_perfil`)
    REFERENCES `perfil` (`cod_perfil`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `fk_infoperfil_perfil_idx` ON `infoperfil` (`cod_perfil` ASC) VISIBLE;


-- -----------------------------------------------------
-- Table `logobjeto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `logobjeto` (
  `cod_logobjeto` INT NOT NULL AUTO_INCREMENT,
  `cod_objeto` INT NOT NULL,
  `cod_usuario` INT NOT NULL,
  `estampa` BIGINT NOT NULL,
  `cod_operacao` INT NULL,
  `cod_status` INT NULL,
  `mensagem` VARCHAR(255) NULL,
  `ip` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`cod_logobjeto`),
  CONSTRAINT `fk_logobjeto_objeto`
    FOREIGN KEY (`cod_objeto`)
    REFERENCES `objeto` (`cod_objeto`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_logobjeto_usuario`
    FOREIGN KEY (`cod_usuario`)
    REFERENCES `usuario` (`cod_usuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_logobjeto_status`
    FOREIGN KEY (`cod_status`)
    REFERENCES `status` (`cod_status`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_logobjeto_objeto_idx` ON `logobjeto` (`cod_objeto` ASC) VISIBLE;

CREATE INDEX `fk_logobjeto_usuario_idx` ON `logobjeto` (`cod_usuario` ASC) VISIBLE;

CREATE INDEX `fk_logobjeto_status_idx` ON `logobjeto` (`cod_status` ASC) VISIBLE;


-- -----------------------------------------------------
-- Table `parentesco`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `parentesco` (
  `cod_objeto` INT NOT NULL,
  `cod_pai` INT NOT NULL,
  `ordem` INT NOT NULL,
  PRIMARY KEY (`cod_objeto`, `cod_pai`, `ordem`),
  CONSTRAINT `fk_parentesco_objeto`
    FOREIGN KEY (`cod_objeto`)
    REFERENCES `objeto` (`cod_objeto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_parentesco_objeto_pai`
    FOREIGN KEY (`cod_pai`)
    REFERENCES `objeto` (`cod_objeto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_parentesco_objeto_pai_idx` ON `parentesco` (`cod_pai` ASC) VISIBLE;


-- -----------------------------------------------------
-- Table `pendencia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pendencia` (
  `cod_pendencia` INT NOT NULL AUTO_INCREMENT,
  `cod_usuario` INT NOT NULL,
  `cod_objeto` INT NOT NULL,
  PRIMARY KEY (`cod_pendencia`),
  CONSTRAINT `fk_pendencia_objeto`
    FOREIGN KEY (`cod_objeto`)
    REFERENCES `objeto` (`cod_objeto`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_pendencia_usuario`
    FOREIGN KEY (`cod_usuario`)
    REFERENCES `usuario` (`cod_usuario`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `fk_pendencia_objeto_idx` ON `pendencia` (`cod_objeto` ASC) VISIBLE;

CREATE INDEX `fk_pendencia_usuario_idx` ON `pendencia` (`cod_usuario` ASC) VISIBLE;


-- -----------------------------------------------------
-- Table `usuarioxobjetoxperfil`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarioxobjetoxperfil` (
  `cod_usuario` INT NOT NULL,
  `cod_objeto` INT NOT NULL,
  `cod_perfil` INT NOT NULL,
  PRIMARY KEY (`cod_usuario`, `cod_objeto`, `cod_perfil`),
  CONSTRAINT `fk_usuarioxobjetoxperfil_usuario`
    FOREIGN KEY (`cod_usuario`)
    REFERENCES `usuario` (`cod_usuario`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_usuarioxobjetoxperfil_objeto`
    FOREIGN KEY (`cod_objeto`)
    REFERENCES `objeto` (`cod_objeto`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_usuarioxobjetoxperfil_perfil`
    FOREIGN KEY (`cod_perfil`)
    REFERENCES `perfil` (`cod_perfil`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `fk_usuarioxobjetoxperfil_objeto_idx` ON `usuarioxobjetoxperfil` (`cod_objeto` ASC) VISIBLE;

CREATE INDEX `fk_usuarioxobjetoxperfil_perfil_idx` ON `usuarioxobjetoxperfil` (`cod_perfil` ASC) VISIBLE;


-- -----------------------------------------------------
-- Table `versaoobjeto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `versaoobjeto` (
  `cod_versaoobjeto` INT NOT NULL AUTO_INCREMENT,
  `cod_objeto` INT NOT NULL,
  `versao` INT NOT NULL,
  `conteudo` TEXT NULL,
  `data_criacao` DATETIME NULL,
  `cod_usuario` INT NULL,
  `ip` VARCHAR(30) NULL,
  PRIMARY KEY (`cod_versaoobjeto`))
ENGINE = InnoDB;
        */
        
    }

    public function iniciar()
    {
        $versao = Pgsql::versao;

        for ($i=1; $i<=$versao; $i++)
        {
            $nomeMetodo = "versao".$i;
            $this->$nomeMetodo();
        }
    }



}