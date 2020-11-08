<?php

namespace Pbl\Core\Banco\Schema;

use Pimple\Container;

class Pgsql {

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
        // tabela classe
        $sql = " CREATE SEQUENCE ".$tabs["classe"]["nome"]."_".$tabs["classe"]["colunas"]["cod_classe"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= " CREATE TABLE ".$tabs["classe"]["nome"]." ( "
            . " ".$tabs["classe"]["colunas"]["cod_classe"]." integer DEFAULT nextval('".$tabs["classe"]["nome"]."_".$tabs["classe"]["colunas"]["cod_classe"]."_seq'::regclass) NOT NULL, "
            . " ".$tabs["classe"]["colunas"]["nome"]." character varying(100), "
            . " ".$tabs["classe"]["colunas"]["prefixo"]." character varying(100), "
            . " ".$tabs["classe"]["colunas"]["descricao"]." character varying(255) DEFAULT ''::character varying NOT NULL, "
            . " ".$tabs["classe"]["colunas"]["temfilhos"]." smallint DEFAULT 0 NOT NULL, "
            . " ".$tabs["classe"]["colunas"]["sistema"]." smallint DEFAULT 0 NOT NULL, "
            . " ".$tabs["classe"]["colunas"]["indexar"]." smallint DEFAULT 0 NOT NULL, "
            . " CONSTRAINT pk_".$tabs["classe"]["nome"]." PRIMARY KEY (".$tabs["classe"]["colunas"]["cod_classe"].") "
            . " ); ".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["classe"]["nome"]."_".$tabs["classe"]["colunas"]["cod_classe"]."_seq OWNED BY ".$tabs["classe"]["nome"].".".$tabs["classe"]["colunas"]["cod_classe"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["classe"]["nome"]."_".$tabs["classe"]["colunas"]["indexar"]." ON ".$tabs["classe"]["nome"]." USING btree (".$tabs["classe"]["colunas"]["indexar"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["classe"]["nome"]."_".$tabs["classe"]["colunas"]["nome"]." ON ".$tabs["classe"]["nome"]." USING btree (".$tabs["classe"]["colunas"]["nome"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["classe"]["nome"]."_".$tabs["classe"]["colunas"]["prefixo"]." ON ".$tabs["classe"]["nome"]." USING btree (".$tabs["classe"]["colunas"]["prefixo"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["classe"]["nome"]."_".$tabs["classe"]["colunas"]["temfilhos"]." ON ".$tabs["classe"]["nome"]." USING btree (".$tabs["classe"]["colunas"]["temfilhos"]." ASC NULLS LAST); ".PHP_EOL;

        // tabela classexfilhos
        $sql .= " CREATE TABLE ".$tabs["classexfilhos"]["nome"]." "
            . " ( ".$tabs["classexfilhos"]["colunas"]["cod_classe"]." integer NOT NULL, "
            . " ".$tabs["classexfilhos"]["colunas"]["cod_classe_filho"]." integer NOT NULL, "
            . " CONSTRAINT pk_".$tabs["classexfilhos"]["nome"]." PRIMARY KEY (".$tabs["classexfilhos"]["colunas"]["cod_classe"].", ".$tabs["classexfilhos"]["colunas"]["cod_classe_filho"]."), "
            . " CONSTRAINT fk_".$tabs["classexfilhos"]["nome"]."_".$tabs["classe"]["nome"]." FOREIGN KEY (".$tabs["classexfilhos"]["colunas"]["cod_classe"].") "
            . " REFERENCES ".$tabs["classe"]["nome"]." (".$tabs["classe"]["colunas"]["cod_classe"].") MATCH SIMPLE "
            . " ON UPDATE CASCADE "
            ." ON DELETE CASCADE, "
            ." CONSTRAINT fk_".$tabs["classexfilhos"]["nome"]."_classe_filho FOREIGN KEY (".$tabs["classexfilhos"]["colunas"]["cod_classe_filho"].") "
            ." REFERENCES ".$tabs["classe"]["nome"]." (".$tabs["classe"]["colunas"]["cod_classe"].") MATCH SIMPLE "
            ." ON UPDATE CASCADE "
            ." ON DELETE CASCADE "
            ." ); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["classexfilhos"]["nome"]."_".$tabs["classexfilhos"]["colunas"]["cod_classe"]." ON ".$tabs["classexfilhos"]["nome"]." USING btree (".$tabs["classexfilhos"]["colunas"]["cod_classe"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["classexfilhos"]["nome"]."_".$tabs["classexfilhos"]["colunas"]["cod_classe_filho"]." ON ".$tabs["classexfilhos"]["nome"]." USING btree (".$tabs["classexfilhos"]["colunas"]["cod_classe_filho"]." ASC NULLS LAST); ".PHP_EOL;

        // tabela tipodado
        $sql .= " CREATE SEQUENCE ".$tabs["tipodado"]["nome"]."_".$tabs["tipodado"]["colunas"]["cod_tipodado"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= " CREATE TABLE ".$tabs["tipodado"]["nome"]." "
        . " ( ".$tabs["tipodado"]["colunas"]["cod_tipodado"]." integer DEFAULT nextval('".$tabs["tipodado"]["nome"]."_".$tabs["tipodado"]["colunas"]["cod_tipodado"]."_seq'::regclass)  NOT NULL  , "
        . " ".$tabs["tipodado"]["colunas"]["nome"]." character varying(50), "
        . " ".$tabs["tipodado"]["colunas"]["tabela"]." character varying(50), "
        . " ".$tabs["tipodado"]["colunas"]["delimitador"]." character varying(1), "
        . " CONSTRAINT pk_".$tabs["tipodado"]["nome"]." PRIMARY KEY (".$tabs["tipodado"]["colunas"]["cod_tipodado"].")); ".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["tipodado"]["nome"]."_".$tabs["tipodado"]["colunas"]["cod_tipodado"]."_seq OWNED BY ".$tabs["tipodado"]["nome"].".".$tabs["tipodado"]["colunas"]["cod_tipodado"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tipodado"]["nome"]."_".$tabs["tipodado"]["colunas"]["tabela"]." ON ".$tabs["tipodado"]["nome"]." USING btree (".$tabs["tipodado"]["colunas"]["tabela"]." ASC NULLS LAST); ".PHP_EOL;

        // tabela propriedade
        $sql .= " CREATE SEQUENCE ".$tabs["propriedade"]["nome"]."_".$tabs["propriedade"]["colunas"]["cod_propriedade"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= " CREATE TABLE ".$tabs["propriedade"]["nome"]." "
            . "( ".$tabs["propriedade"]["colunas"]["cod_propriedade"]." integer NOT NULL DEFAULT nextval('".$tabs["propriedade"]["nome"]."_".$tabs["propriedade"]["colunas"]["cod_propriedade"]."_seq'::regclass), "
            . " ".$tabs["propriedade"]["colunas"]["cod_classe"]." integer NOT NULL DEFAULT 0, "
            . " ".$tabs["propriedade"]["colunas"]["cod_tipodado"]." integer NOT NULL DEFAULT 0, "
            . " ".$tabs["propriedade"]["colunas"]["cod_referencia_classe"]." integer, "
            . " ".$tabs["propriedade"]["colunas"]["campo_ref"]." character varying(100), "
            . " ".$tabs["propriedade"]["colunas"]["nome"]." character varying(100), "
            . " ".$tabs["propriedade"]["colunas"]["posicao"]." smallint DEFAULT 0, "
            . " ".$tabs["propriedade"]["colunas"]["descricao"]." character varying(255), "
            . " ".$tabs["propriedade"]["colunas"]["rotulo"]." character varying(100), "
            . " ".$tabs["propriedade"]["colunas"]["rot1booleano"]." character varying(100), "
            . " ".$tabs["propriedade"]["colunas"]["rot2booleano"]." character varying(100), "
            . " ".$tabs["propriedade"]["colunas"]["obrigatorio"]." smallint, "
            . " ".$tabs["propriedade"]["colunas"]["seguranca"]." smallint, "
            . " ".$tabs["propriedade"]["colunas"]["valorpadrao"]." character varying(255), "
            . " CONSTRAINT pk_".$tabs["propriedade"]["nome"]." PRIMARY KEY (".$tabs["propriedade"]["colunas"]["cod_propriedade"]."), "
            . " CONSTRAINT fk_".$tabs["propriedade"]["nome"]."_".$tabs["classe"]["nome"]." FOREIGN KEY (".$tabs["propriedade"]["colunas"]["cod_classe"].") REFERENCES ".$tabs["classe"]["nome"]." (".$tabs["classe"]["colunas"]["cod_classe"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE, "
            . " CONSTRAINT fk_".$tabs["propriedade"]["nome"]."_".$tabs["tipodado"]["nome"]." FOREIGN KEY (".$tabs["propriedade"]["colunas"]["cod_tipodado"].") REFERENCES ".$tabs["tipodado"]["nome"]." (".$tabs["tipodado"]["colunas"]["cod_tipodado"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE); ".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["propriedade"]["nome"]."_".$tabs["propriedade"]["colunas"]["cod_propriedade"]."_seq OWNED BY ".$tabs["propriedade"]["nome"].".".$tabs["propriedade"]["colunas"]["cod_propriedade"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["propriedade"]["nome"]."_".$tabs["propriedade"]["colunas"]["cod_classe"]." ON ".$tabs["propriedade"]["nome"]." USING btree (".$tabs["propriedade"]["colunas"]["cod_classe"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["propriedade"]["nome"]."_".$tabs["propriedade"]["colunas"]["cod_tipodado"]." ON ".$tabs["propriedade"]["nome"]." USING btree (".$tabs["propriedade"]["colunas"]["cod_tipodado"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["propriedade"]["nome"]."_".$tabs["propriedade"]["colunas"]["cod_referencia_classe"]." ON ".$tabs["propriedade"]["nome"]." USING btree (".$tabs["propriedade"]["colunas"]["nome"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["propriedade"]["nome"]."_".$tabs["propriedade"]["colunas"]["nome"]." ON ".$tabs["propriedade"]["nome"]." USING btree (".$tabs["propriedade"]["colunas"]["cod_classe"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["propriedade"]["nome"]."_".$tabs["propriedade"]["colunas"]["posicao"]." ON ".$tabs["propriedade"]["nome"]." USING btree (".$tabs["propriedade"]["colunas"]["posicao"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["propriedade"]["nome"]."_".$tabs["propriedade"]["colunas"]["obrigatorio"]." ON ".$tabs["propriedade"]["nome"]." USING btree (".$tabs["propriedade"]["colunas"]["obrigatorio"]." ASC NULLS LAST); ".PHP_EOL;

        // tabela usuario
        $sql .= " CREATE SEQUENCE ".$tabs["usuario"]["nome"]."_".$tabs["usuario"]["colunas"]["cod_usuario"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= " CREATE TABLE ".$tabs["usuario"]["nome"]." "
            . " (".$tabs["usuario"]["colunas"]["cod_usuario"]." integer NOT NULL DEFAULT nextval('".$tabs["usuario"]["nome"]."_".$tabs["usuario"]["colunas"]["cod_usuario"]."_seq'::regclass), "
            . " ".$tabs["usuario"]["colunas"]["secao"]." character varying(255), "
            . " ".$tabs["usuario"]["colunas"]["nome"]." character varying(255), "
            . " ".$tabs["usuario"]["colunas"]["login"]." character varying(55), "
            . " ".$tabs["usuario"]["colunas"]["email"]." character varying(255), "
            . " ".$tabs["usuario"]["colunas"]["ramal"]." character varying(50), "
            . " ".$tabs["usuario"]["colunas"]["senha"]." character varying(32), "
            . " ".$tabs["usuario"]["colunas"]["chefia"]." integer, "
            . " ".$tabs["usuario"]["colunas"]["valido"]." smallint, "
            . " ".$tabs["usuario"]["colunas"]["data_atualizacao"]." bigint, "
            . " ".$tabs["usuario"]["colunas"]["altera_senha"]." smallint, "
            . " ".$tabs["usuario"]["colunas"]["ldap"]." smallint, "
            . " CONSTRAINT pk_".$tabs["usuario"]["nome"]." PRIMARY KEY (".$tabs["usuario"]["colunas"]["cod_usuario"].")); ".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["usuario"]["nome"]."_".$tabs["usuario"]["colunas"]["cod_usuario"]."_seq OWNED BY ".$tabs["usuario"]["nome"].".".$tabs["usuario"]["colunas"]["cod_usuario"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["usuario"]["nome"]."_".$tabs["usuario"]["colunas"]["chefia"]." ON ".$tabs["usuario"]["nome"]." USING btree (".$tabs["usuario"]["colunas"]["chefia"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["usuario"]["nome"]."_".$tabs["usuario"]["colunas"]["email"]." ON ".$tabs["usuario"]["nome"]." USING btree (".$tabs["usuario"]["colunas"]["email"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["usuario"]["nome"]."_".$tabs["usuario"]["colunas"]["login"]." ON ".$tabs["usuario"]["nome"]." USING btree (".$tabs["usuario"]["colunas"]["login"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["usuario"]["nome"]."_".$tabs["usuario"]["colunas"]["secao"]." ON ".$tabs["usuario"]["nome"]." USING btree (".$tabs["usuario"]["colunas"]["secao"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["usuario"]["nome"]."_".$tabs["usuario"]["colunas"]["valido"]." ON ".$tabs["usuario"]["nome"]." USING btree(".$tabs["usuario"]["colunas"]["valido"]." ASC NULLS LAST);  ".PHP_EOL;

        // pele
        $sql .= " CREATE SEQUENCE ".$tabs["pele"]["nome"]."_".$tabs["pele"]["colunas"]["cod_pele"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= " CREATE TABLE ".$tabs["pele"]["nome"]." "
            . " (".$tabs["pele"]["colunas"]["cod_pele"]." integer NOT NULL DEFAULT nextval('".$tabs["pele"]["nome"]."_".$tabs["pele"]["colunas"]["cod_pele"]."_seq'::regclass), "
            ." ".$tabs["pele"]["colunas"]["nome"]." character varying(50), "
            ." ".$tabs["pele"]["colunas"]["prefixo"]." character varying(50), "
            ." ".$tabs["pele"]["colunas"]["publica"]." integer DEFAULT 0, "
            ." CONSTRAINT pk_".$tabs["pele"]["nome"]." PRIMARY KEY (".$tabs["pele"]["colunas"]["cod_pele"].")); ".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["pele"]["nome"]."_".$tabs["pele"]["colunas"]["cod_pele"]."_seq OWNED BY ".$tabs["pele"]["nome"].".".$tabs["pele"]["colunas"]["cod_pele"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["pele"]["nome"]."_".$tabs["pele"]["colunas"]["nome"]." ON ".$tabs["pele"]["nome"]." USING btree (".$tabs["pele"]["colunas"]["nome"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["pele"]["nome"]."_".$tabs["pele"]["colunas"]["prefixo"]." ON ".$tabs["pele"]["nome"]." USING btree (".$tabs["pele"]["colunas"]["prefixo"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["pele"]["nome"]."_".$tabs["pele"]["colunas"]["publica"]." ON ".$tabs["pele"]["nome"]." USING btree (".$tabs["pele"]["colunas"]["publica"]." ASC NULLS LAST); ".PHP_EOL;
        
        // status
        $sql .= " CREATE SEQUENCE ".$tabs["status"]["nome"]."_".$tabs["status"]["colunas"]["cod_status"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= " CREATE TABLE ".$tabs["status"]["nome"]." "
        . " (".$tabs["status"]["colunas"]["cod_status"]." integer NOT NULL DEFAULT nextval('".$tabs["status"]["nome"]."_".$tabs["status"]["colunas"]["cod_status"]."_seq'::regclass), "
        . " ".$tabs["status"]["colunas"]["nome"]." character varying(50), "
        . " CONSTRAINT pk_".$tabs["status"]["nome"]." PRIMARY KEY (".$tabs["status"]["colunas"]["cod_status"].")); ".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["status"]["nome"]."_".$tabs["status"]["colunas"]["cod_status"]."_seq OWNED BY ".$tabs["status"]["nome"].".".$tabs["status"]["colunas"]["cod_status"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["status"]["nome"]."_".$tabs["status"]["colunas"]["nome"]." ON ".$tabs["status"]["nome"]." USING btree (".$tabs["status"]["colunas"]["nome"]." ASC NULLS LAST); ".PHP_EOL;

        // perfil
        $sql .= " CREATE SEQUENCE ".$tabs["perfil"]["nome"]."_".$tabs["perfil"]["colunas"]["cod_perfil"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= " CREATE TABLE ".$tabs["perfil"]["nome"]." "
            . " (".$tabs["perfil"]["colunas"]["cod_perfil"]." integer NOT NULL DEFAULT nextval('".$tabs["perfil"]["nome"]."_".$tabs["perfil"]["colunas"]["cod_perfil"]."_seq'::regclass), "
            . " ".$tabs["perfil"]["colunas"]["nome"]." character varying(50) NOT NULL, "
            . " ".$tabs["perfil"]["colunas"]["cod_perfil_pai"]." integer, "
            . " CONSTRAINT pk_".$tabs["perfil"]["nome"]." PRIMARY KEY (".$tabs["perfil"]["colunas"]["cod_perfil"]."));".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["perfil"]["nome"]."_".$tabs["perfil"]["colunas"]["cod_perfil"]."_seq OWNED BY ".$tabs["perfil"]["nome"].".".$tabs["perfil"]["colunas"]["cod_perfil"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["perfil"]["nome"]."_".$tabs["perfil"]["colunas"]["nome"]." ON ".$tabs["perfil"]["nome"]." USING btree (".$tabs["perfil"]["colunas"]["nome"]." ASC NULLS LAST); ".PHP_EOL;
        
        // objeto
        $sql .= " CREATE SEQUENCE ".$tabs["objeto"]["nome"]."_".$tabs["objeto"]["colunas"]["cod_objeto"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= " CREATE TABLE ".$tabs["objeto"]["nome"]." "
            . "(".$tabs["objeto"]["colunas"]["cod_objeto"]." integer NOT NULL DEFAULT nextval('".$tabs["objeto"]["nome"]."_".$tabs["objeto"]["colunas"]["cod_objeto"]."_seq'::regclass), "
            . " ".$tabs["objeto"]["colunas"]["cod_pai"]." integer, "
            . " ".$tabs["objeto"]["colunas"]["cod_classe"]." integer NOT NULL, "
            . " ".$tabs["objeto"]["colunas"]["cod_usuario"]." integer NOT NULL DEFAULT 1, "
            . " ".$tabs["objeto"]["colunas"]["cod_pele"]." integer, "
            . " ".$tabs["objeto"]["colunas"]["cod_status"]." integer NOT NULL DEFAULT 1, "
            . " ".$tabs["objeto"]["colunas"]["titulo"]." character varying(400) NOT NULL, "
            . " ".$tabs["objeto"]["colunas"]["descricao"]." text, "
            . " ".$tabs["objeto"]["colunas"]["data_publicacao"]." bigint NOT NULL, "
            . " ".$tabs["objeto"]["colunas"]["data_validade"]." bigint NOT NULL, "
            . " ".$tabs["objeto"]["colunas"]["script_exibir"]." character varying(255), "
            . " ".$tabs["objeto"]["colunas"]["apagado"]." smallint NOT NULL DEFAULT 0, "
            . " ".$tabs["objeto"]["colunas"]["objetosistema"]." smallint NOT NULL DEFAULT 0, "
            . " ".$tabs["objeto"]["colunas"]["peso"]." integer DEFAULT 0, "
            . " ".$tabs["objeto"]["colunas"]["data_exclusao"]." bigint, "
            . " ".$tabs["objeto"]["colunas"]["url_amigavel"]." character varying(500), "
            . " ".$tabs["objeto"]["colunas"]["versao"]." integer NOT NULL DEFAULT 1, "
            . " ".$tabs["objeto"]["colunas"]["versao_publicada"]." integer, "
            . " CONSTRAINT pk_".$tabs["objeto"]["nome"]." PRIMARY KEY (".$tabs["objeto"]["colunas"]["cod_objeto"]."), "
            . " CONSTRAINT fk_".$tabs["objeto"]["nome"]."_".$tabs["classe"]["nome"]." FOREIGN KEY (".$tabs["objeto"]["colunas"]["cod_classe"].") REFERENCES ".$tabs["classe"]["nome"]." (".$tabs["classe"]["colunas"]["cod_classe"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE, "
            . " CONSTRAINT fk_".$tabs["objeto"]["nome"]."_".$tabs["pele"]["nome"]." FOREIGN KEY (".$tabs["objeto"]["colunas"]["cod_pele"].") REFERENCES ".$tabs["pele"]["nome"]." (".$tabs["pele"]["colunas"]["cod_pele"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE, "
            . " CONSTRAINT fk_".$tabs["objeto"]["nome"]."_".$tabs["status"]["nome"]." FOREIGN KEY (".$tabs["objeto"]["colunas"]["cod_status"].") REFERENCES ".$tabs["status"]["nome"]." (".$tabs["status"]["colunas"]["cod_status"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE, "
            . " CONSTRAINT fk_".$tabs["objeto"]["nome"]."_".$tabs["usuario"]["nome"]." FOREIGN KEY (".$tabs["objeto"]["colunas"]["cod_usuario"].") REFERENCES ".$tabs["usuario"]["nome"]." (".$tabs["usuario"]["colunas"]["cod_usuario"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE); ".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["objeto"]["nome"]."_".$tabs["objeto"]["colunas"]["cod_objeto"]."_seq OWNED BY ".$tabs["objeto"]["nome"].".".$tabs["objeto"]["colunas"]["cod_objeto"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["objeto"]["nome"]."_".$tabs["objeto"]["colunas"]["apagado"]." ON ".$tabs["objeto"]["nome"]." USING btree (".$tabs["objeto"]["colunas"]["apagado"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["objeto"]["nome"]."_".$tabs["objeto"]["colunas"]["cod_classe"]." ON ".$tabs["objeto"]["nome"]." USING btree (".$tabs["objeto"]["colunas"]["cod_classe"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["objeto"]["nome"]."_".$tabs["objeto"]["colunas"]["cod_pai"]." ON ".$tabs["objeto"]["nome"]." USING btree (".$tabs["objeto"]["colunas"]["cod_pai"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["objeto"]["nome"]."_".$tabs["objeto"]["colunas"]["cod_pele"]." ON ".$tabs["objeto"]["nome"]." USING btree (".$tabs["objeto"]["colunas"]["cod_pele"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["objeto"]["nome"]."_".$tabs["objeto"]["colunas"]["cod_status"]." ON ".$tabs["objeto"]["nome"]." USING btree (".$tabs["objeto"]["colunas"]["cod_status"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["objeto"]["nome"]."_".$tabs["objeto"]["colunas"]["cod_usuario"]." ON ".$tabs["objeto"]["nome"]." USING btree (".$tabs["objeto"]["colunas"]["cod_usuario"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["objeto"]["nome"]."_".$tabs["objeto"]["colunas"]["data_publicacao"]." ON ".$tabs["objeto"]["nome"]." USING btree (".$tabs["objeto"]["colunas"]["data_publicacao"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["objeto"]["nome"]."_".$tabs["objeto"]["colunas"]["data_validade"]." ON ".$tabs["objeto"]["nome"]." USING btree (".$tabs["objeto"]["colunas"]["data_validade"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["objeto"]["nome"]."_".$tabs["objeto"]["colunas"]["peso"]." ON ".$tabs["objeto"]["nome"]." USING btree (".$tabs["objeto"]["colunas"]["peso"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["objeto"]["nome"]."_".$tabs["objeto"]["colunas"]["titulo"]." ON ".$tabs["objeto"]["nome"]." USING btree (".$tabs["objeto"]["colunas"]["titulo"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["objeto"]["nome"]."_".$tabs["objeto"]["colunas"]["url_amigavel"]." ON ".$tabs["objeto"]["nome"]." USING btree (".$tabs["objeto"]["colunas"]["url_amigavel"]." ASC NULLS LAST); ".PHP_EOL;

        // classexobjeto
        $sql .= " CREATE TABLE ".$tabs["classexobjeto"]["nome"]." "
            . " (".$tabs["classexobjeto"]["colunas"]["cod_classe"]." integer NOT NULL, "
            . " ".$tabs["classexobjeto"]["colunas"]["cod_objeto"]." integer NOT NULL, "
            . " CONSTRAINT pk_".$tabs["classexobjeto"]["nome"]." PRIMARY KEY (".$tabs["classexobjeto"]["colunas"]["cod_classe"].", ".$tabs["classexobjeto"]["colunas"]["cod_objeto"]."), "
            . " CONSTRAINT fk_".$tabs["classexobjeto"]["nome"]."_".$tabs["classe"]["nome"]." FOREIGN KEY (".$tabs["classexobjeto"]["colunas"]["cod_classe"].") REFERENCES ".$tabs["classe"]["nome"]." (".$tabs["classe"]["colunas"]["cod_classe"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE, "
            . " CONSTRAINT fk_".$tabs["classexobjeto"]["nome"]."_".$tabs["objeto"]["nome"]." FOREIGN KEY (".$tabs["classexobjeto"]["colunas"]["cod_objeto"].") REFERENCES ".$tabs["objeto"]["nome"]." (".$tabs["objeto"]["colunas"]["cod_objeto"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["classexobjeto"]["nome"]."_".$tabs["classexobjeto"]["colunas"]["cod_classe"]." ON ".$tabs["classexobjeto"]["nome"]." USING btree (".$tabs["classexobjeto"]["colunas"]["cod_classe"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["classexobjeto"]["nome"]."_".$tabs["classexobjeto"]["colunas"]["cod_objeto"]." ON ".$tabs["classexobjeto"]["nome"]." USING btree (".$tabs["classexobjeto"]["colunas"]["cod_objeto"]." ASC NULLS LAST); ".PHP_EOL;

        // infoperfil
        $sql .= " CREATE SEQUENCE ".$tabs["infoperfil"]["nome"]."_".$tabs["infoperfil"]["colunas"]["cod_infoperfil"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= " CREATE TABLE ".$tabs["infoperfil"]["nome"]." "
            . " (".$tabs["infoperfil"]["colunas"]["cod_infoperfil"]." integer NOT NULL DEFAULT nextval('".$tabs["infoperfil"]["nome"]."_".$tabs["infoperfil"]["colunas"]["cod_infoperfil"]."_seq'::regclass), "
            . " ".$tabs["infoperfil"]["colunas"]["cod_perfil"]." integer NOT NULL, "
            . " ".$tabs["infoperfil"]["colunas"]["acao"]." character varying(200), "
            . " ".$tabs["infoperfil"]["colunas"]["script"]." character varying(200), "
            . " ".$tabs["infoperfil"]["colunas"]["donooupublicado"]." smallint DEFAULT 0, "
            . " ".$tabs["infoperfil"]["colunas"]["sopublicado"]." smallint DEFAULT 0, "
            . " ".$tabs["infoperfil"]["colunas"]["sodono"]." smallint DEFAULT 0, "
            . " ".$tabs["infoperfil"]["colunas"]["naomenu"]." smallint DEFAULT 0, "
            . " ".$tabs["infoperfil"]["colunas"]["ordem"]." smallint, "
            . " ".$tabs["infoperfil"]["colunas"]["icone"]." character varying(30), "
            . " CONSTRAINT pk_".$tabs["infoperfil"]["nome"]." PRIMARY KEY (".$tabs["infoperfil"]["colunas"]["cod_infoperfil"]."), "
            . " CONSTRAINT fk_".$tabs["infoperfil"]["nome"]."_".$tabs["perfil"]["nome"]." FOREIGN KEY (".$tabs["infoperfil"]["colunas"]["cod_perfil"].") REFERENCES ".$tabs["perfil"]["nome"]." (".$tabs["perfil"]["colunas"]["cod_perfil"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE);".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["infoperfil"]["nome"]."_".$tabs["infoperfil"]["colunas"]["cod_infoperfil"]."_seq OWNED BY ".$tabs["infoperfil"]["nome"].".".$tabs["infoperfil"]["colunas"]["cod_infoperfil"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["infoperfil"]["nome"]."_".$tabs["infoperfil"]["colunas"]["acao"]." ON ".$tabs["infoperfil"]["nome"]." USING btree (".$tabs["infoperfil"]["colunas"]["acao"]." ASC NULLS LAST);".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["infoperfil"]["nome"]."_".$tabs["infoperfil"]["colunas"]["cod_perfil"]." ON ".$tabs["infoperfil"]["nome"]." USING btree (".$tabs["infoperfil"]["colunas"]["cod_perfil"]." ASC NULLS LAST);".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["infoperfil"]["nome"]."_".$tabs["infoperfil"]["colunas"]["ordem"]." ON ".$tabs["infoperfil"]["nome"]." USING btree (".$tabs["infoperfil"]["colunas"]["ordem"]." ASC NULLS LAST);".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["infoperfil"]["nome"]."_".$tabs["infoperfil"]["colunas"]["script"]." ON ".$tabs["infoperfil"]["nome"]." USING btree (".$tabs["infoperfil"]["colunas"]["script"]." ASC NULLS LAST);".PHP_EOL;

        // parentesco
        $sql .= " CREATE TABLE ".$tabs["parentesco"]["nome"]." "
            . " (".$tabs["parentesco"]["colunas"]["cod_objeto"]." integer NOT NULL, "
            . " ".$tabs["parentesco"]["colunas"]["cod_pai"]." integer NOT NULL, "
            . " ".$tabs["parentesco"]["colunas"]["ordem"]." integer NOT NULL, "
            . " CONSTRAINT pk_".$tabs["parentesco"]["nome"]." PRIMARY KEY (".$tabs["parentesco"]["colunas"]["cod_objeto"].", ".$tabs["parentesco"]["colunas"]["cod_pai"].", ".$tabs["parentesco"]["colunas"]["ordem"]."), "
            . " CONSTRAINT fk_".$tabs["parentesco"]["nome"]."_".$tabs["objeto"]["nome"]." FOREIGN KEY (".$tabs["parentesco"]["colunas"]["cod_objeto"].") REFERENCES ".$tabs["objeto"]["nome"]." (".$tabs["objeto"]["colunas"]["cod_objeto"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE, "
            . " CONSTRAINT fk_".$tabs["parentesco"]["nome"]."_".$tabs["objeto"]["nome"]."pai FOREIGN KEY (".$tabs["parentesco"]["colunas"]["cod_pai"].") REFERENCES ".$tabs["objeto"]["nome"]." (".$tabs["objeto"]["colunas"]["cod_objeto"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["parentesco"]["nome"]."_".$tabs["parentesco"]["colunas"]["cod_objeto"]." ON ".$tabs["parentesco"]["nome"]." USING btree (".$tabs["parentesco"]["colunas"]["cod_objeto"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["parentesco"]["nome"]."_".$tabs["parentesco"]["colunas"]["cod_pai"]." ON ".$tabs["parentesco"]["nome"]." USING btree (".$tabs["parentesco"]["colunas"]["cod_pai"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["parentesco"]["nome"]."_".$tabs["parentesco"]["colunas"]["ordem"]." ON ".$tabs["parentesco"]["nome"]." USING btree (".$tabs["parentesco"]["colunas"]["ordem"]." ASC NULLS LAST); ".PHP_EOL;

        // logobjeto
        $sql .= " CREATE SEQUENCE ".$tabs["logobjeto"]["nome"]."_".$tabs["logobjeto"]["colunas"]["cod_logobjeto"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= " CREATE TABLE ".$tabs["logobjeto"]["nome"]." "
            . " (".$tabs["logobjeto"]["colunas"]["cod_logobjeto"]." integer NOT NULL DEFAULT nextval('".$tabs["logobjeto"]["nome"]."_".$tabs["logobjeto"]["colunas"]["cod_logobjeto"]."_seq'::regclass), "
            . " ".$tabs["logobjeto"]["colunas"]["cod_objeto"]." integer NOT NULL, "
            . " ".$tabs["logobjeto"]["colunas"]["estampa"]." bigint NOT NULL, "
            . " ".$tabs["logobjeto"]["colunas"]["cod_usuario"]." integer NOT NULL, "
            . " ".$tabs["logobjeto"]["colunas"]["cod_operacao"]." smallint, "
            . " CONSTRAINT pk_".$tabs["logobjeto"]["nome"]." PRIMARY KEY (".$tabs["logobjeto"]["colunas"]["cod_logobjeto"]."), "
            . " CONSTRAINT fk_".$tabs["logobjeto"]["nome"]."_".$tabs["objeto"]["nome"]." FOREIGN KEY (".$tabs["logobjeto"]["colunas"]["cod_objeto"].") REFERENCES ".$tabs["objeto"]["nome"]." (".$tabs["objeto"]["colunas"]["cod_objeto"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE, "
            . " CONSTRAINT fk_".$tabs["logobjeto"]["nome"]."_".$tabs["usuario"]["nome"]." FOREIGN KEY (".$tabs["logobjeto"]["colunas"]["cod_usuario"].") REFERENCES ".$tabs["usuario"]["nome"]." (".$tabs["usuario"]["colunas"]["cod_usuario"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE); ".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["logobjeto"]["nome"]."_".$tabs["logobjeto"]["colunas"]["cod_logobjeto"]."_seq OWNED BY ".$tabs["logobjeto"]["nome"].".".$tabs["logobjeto"]["colunas"]["cod_logobjeto"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["logobjeto"]["nome"]."_".$tabs["logobjeto"]["colunas"]["cod_objeto"]." ON ".$tabs["logobjeto"]["nome"]." USING btree (".$tabs["logobjeto"]["colunas"]["cod_objeto"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["logobjeto"]["nome"]."_".$tabs["logobjeto"]["colunas"]["cod_operacao"]." ON ".$tabs["logobjeto"]["nome"]." USING btree (".$tabs["logobjeto"]["colunas"]["cod_operacao"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["logobjeto"]["nome"]."_".$tabs["logobjeto"]["colunas"]["cod_usuario"]." ON ".$tabs["logobjeto"]["nome"]." USING btree (".$tabs["logobjeto"]["colunas"]["cod_usuario"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["logobjeto"]["nome"]."_".$tabs["logobjeto"]["colunas"]["estampa"]." ON ".$tabs["logobjeto"]["nome"]." USING btree (".$tabs["logobjeto"]["colunas"]["estampa"]." ASC NULLS LAST); ".PHP_EOL;

        // logworkflow
        $sql .= " CREATE SEQUENCE ".$tabs["logworkflow"]["nome"]."_".$tabs["logworkflow"]["colunas"]["cod_logworkflow"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= " CREATE TABLE ".$tabs["logworkflow"]["nome"]." "
            . " (".$tabs["logworkflow"]["colunas"]["cod_logworkflow"]." integer NOT NULL DEFAULT nextval('".$tabs["logworkflow"]["nome"]."_".$tabs["logworkflow"]["colunas"]["cod_logworkflow"]."_seq'::regclass), "
            . " ".$tabs["logworkflow"]["colunas"]["cod_objeto"]." integer NOT NULL, "
            . " ".$tabs["logworkflow"]["colunas"]["estampa"]." bigint NOT NULL, "
            . " ".$tabs["logworkflow"]["colunas"]["cod_usuario"]." integer NOT NULL, "
            . " ".$tabs["logworkflow"]["colunas"]["cod_status"]." smallint, "
            . " ".$tabs["logworkflow"]["colunas"]["mensagem"]." text, "
            . " CONSTRAINT pk_".$tabs["logworkflow"]["nome"]." PRIMARY KEY (".$tabs["logworkflow"]["colunas"]["cod_logworkflow"]."), "
            . " CONSTRAINT fk_".$tabs["logworkflow"]["nome"]."_".$tabs["objeto"]["nome"]." FOREIGN KEY (".$tabs["logworkflow"]["colunas"]["cod_objeto"].") REFERENCES ".$tabs["objeto"]["nome"]." (".$tabs["objeto"]["colunas"]["cod_objeto"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE, "
            . " CONSTRAINT fk_".$tabs["logworkflow"]["nome"]."_".$tabs["usuario"]["nome"]." FOREIGN KEY (".$tabs["logworkflow"]["colunas"]["cod_usuario"].") REFERENCES ".$tabs["usuario"]["nome"]." (".$tabs["usuario"]["colunas"]["cod_usuario"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE); ".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["logworkflow"]["nome"]."_".$tabs["logworkflow"]["colunas"]["cod_logworkflow"]."_seq OWNED BY ".$tabs["logworkflow"]["nome"].".".$tabs["logworkflow"]["colunas"]["cod_logworkflow"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["logworkflow"]["nome"]."_".$tabs["logworkflow"]["colunas"]["cod_objeto"]." ON ".$tabs["logworkflow"]["nome"]." USING btree (".$tabs["logworkflow"]["colunas"]["cod_objeto"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["logworkflow"]["nome"]."_".$tabs["logworkflow"]["colunas"]["cod_status"]." ON ".$tabs["logworkflow"]["nome"]." USING btree (".$tabs["logworkflow"]["colunas"]["cod_status"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["logworkflow"]["nome"]."_".$tabs["logworkflow"]["colunas"]["cod_usuario"]." ON ".$tabs["logworkflow"]["nome"]." USING btree (".$tabs["logworkflow"]["colunas"]["cod_usuario"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["logworkflow"]["nome"]."_".$tabs["logworkflow"]["colunas"]["estampa"]." ON ".$tabs["logworkflow"]["nome"]." USING btree (".$tabs["logworkflow"]["colunas"]["estampa"]." ASC NULLS LAST); ".PHP_EOL;

        // pendencia
        $sql .= " CREATE SEQUENCE ".$tabs["pendencia"]["nome"]."_".$tabs["pendencia"]["colunas"]["cod_pendencia"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= " CREATE TABLE pendencia "
            . " (".$tabs["pendencia"]["colunas"]["cod_pendencia"]." integer NOT NULL DEFAULT nextval('".$tabs["pendencia"]["nome"]."_".$tabs["pendencia"]["colunas"]["cod_pendencia"]."_seq'::regclass), "
            . " ".$tabs["pendencia"]["colunas"]["cod_usuario"]." integer NOT NULL, "
            . " ".$tabs["pendencia"]["colunas"]["cod_objeto"]." integer NOT NULL, "
            . " CONSTRAINT pk_".$tabs["pendencia"]["nome"]." PRIMARY KEY (".$tabs["pendencia"]["colunas"]["cod_pendencia"]."), "
            . " CONSTRAINT fk_".$tabs["pendencia"]["nome"]."_".$tabs["objeto"]["nome"]." FOREIGN KEY (".$tabs["pendencia"]["colunas"]["cod_objeto"].") REFERENCES ".$tabs["objeto"]["nome"]." (".$tabs["objeto"]["colunas"]["cod_objeto"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE, "
            . " CONSTRAINT fk_".$tabs["pendencia"]["nome"]."_".$tabs["usuario"]["nome"]." FOREIGN KEY (".$tabs["pendencia"]["colunas"]["cod_usuario"].") REFERENCES ".$tabs["usuario"]["nome"]." (".$tabs["usuario"]["colunas"]["cod_usuario"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE); ".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["pendencia"]["nome"]."_".$tabs["pendencia"]["colunas"]["cod_pendencia"]."_seq OWNED BY ".$tabs["pendencia"]["nome"].".".$tabs["pendencia"]["colunas"]["cod_pendencia"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["pendencia"]["nome"]."_".$tabs["pendencia"]["colunas"]["cod_objeto"]." ON ".$tabs["pendencia"]["nome"]." USING btree (".$tabs["pendencia"]["colunas"]["cod_objeto"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["pendencia"]["nome"]."_".$tabs["pendencia"]["colunas"]["cod_usuario"]." ON ".$tabs["pendencia"]["nome"]." USING btree (".$tabs["pendencia"]["colunas"]["cod_usuario"]." ASC NULLS LAST); ".PHP_EOL;

        // pilha
        $sql .= " CREATE SEQUENCE ".$tabs["pilha"]["nome"]."_".$tabs["pilha"]["colunas"]["cod_pilha"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= " CREATE TABLE pilha "
            . " (".$tabs["pilha"]["colunas"]["cod_pilha"]." integer NOT NULL DEFAULT nextval('".$tabs["pilha"]["nome"]."_".$tabs["pilha"]["colunas"]["cod_pilha"]."_seq'::regclass), "
            . " ".$tabs["pilha"]["colunas"]["cod_usuario"]." integer NOT NULL, "
            . " ".$tabs["pilha"]["colunas"]["cod_objeto"]." integer NOT NULL, "
            . " ".$tabs["pilha"]["colunas"]["cod_tipo"]." integer, "
            . " ".$tabs["pilha"]["colunas"]["datahora"]." bigint, "
            . " CONSTRAINT pk_".$tabs["pilha"]["nome"]." PRIMARY KEY (".$tabs["pilha"]["colunas"]["cod_pilha"]."), "
            . " CONSTRAINT fk_".$tabs["pilha"]["nome"]."_".$tabs["objeto"]["nome"]." FOREIGN KEY (".$tabs["pilha"]["colunas"]["cod_objeto"].") REFERENCES ".$tabs["objeto"]["nome"]." (".$tabs["objeto"]["colunas"]["cod_objeto"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE, "
            . " CONSTRAINT fk_".$tabs["pilha"]["nome"]."_".$tabs["usuario"]["nome"]." FOREIGN KEY (".$tabs["pilha"]["colunas"]["cod_usuario"].") REFERENCES ".$tabs["usuario"]["nome"]." (".$tabs["usuario"]["colunas"]["cod_usuario"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE); ".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["pilha"]["nome"]."_".$tabs["pilha"]["colunas"]["cod_pilha"]."_seq OWNED BY ".$tabs["pilha"]["nome"].".".$tabs["pilha"]["colunas"]["cod_pilha"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["pilha"]["nome"]."_".$tabs["pilha"]["colunas"]["cod_objeto"]." ON ".$tabs["pilha"]["nome"]." USING btree (".$tabs["pilha"]["colunas"]["cod_objeto"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["pilha"]["nome"]."_".$tabs["pilha"]["colunas"]["cod_usuario"]." ON ".$tabs["pilha"]["nome"]." USING btree (".$tabs["pilha"]["colunas"]["cod_usuario"]." ASC NULLS LAST); ".PHP_EOL;

        // tag
        $sql .= " CREATE SEQUENCE ".$tabs["tag"]["nome"]."_".$tabs["tag"]["colunas"]["cod_tag"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= " CREATE TABLE ".$tabs["tag"]["nome"]." "
            . " (".$tabs["tag"]["colunas"]["cod_tag"]." integer NOT NULL DEFAULT nextval('".$tabs["tag"]["nome"]."_".$tabs["tag"]["colunas"]["cod_tag"]."_seq'::regclass), "
            . " ".$tabs["tag"]["colunas"]["nome_tag"]." character varying(200), "
            . " CONSTRAINT pk_".$tabs["tag"]["nome"]." PRIMARY KEY (".$tabs["tag"]["colunas"]["cod_tag"].")); ".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["tag"]["nome"]."_".$tabs["tag"]["colunas"]["cod_tag"]."_seq OWNED BY ".$tabs["tag"]["nome"].".".$tabs["tag"]["colunas"]["cod_tag"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tag"]["nome"]."_".$tabs["tag"]["colunas"]["nome_tag"]." ON ".$tabs["tag"]["nome"]." USING btree (".$tabs["tag"]["colunas"]["nome_tag"]." ASC NULLS LAST); ".PHP_EOL;

        // tagxobjeto
        $sql .= " CREATE SEQUENCE ".$tabs["tagxobjeto"]["nome"]."_".$tabs["tagxobjeto"]["colunas"]["cod_tagxobjeto"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= " CREATE TABLE ".$tabs["tagxobjeto"]["nome"]." "
            . " (".$tabs["tagxobjeto"]["colunas"]["cod_tag"]." integer NOT NULL, "
            . " ".$tabs["tagxobjeto"]["colunas"]["cod_objeto"]." integer NOT NULL, "
            . " ".$tabs["tagxobjeto"]["colunas"]["cod_tagxobjeto"]." integer NOT NULL DEFAULT nextval('".$tabs["tagxobjeto"]["nome"]."_".$tabs["tagxobjeto"]["colunas"]["cod_tagxobjeto"]."_seq'::regclass), "
            . " CONSTRAINT pk_".$tabs["tagxobjeto"]["nome"]." PRIMARY KEY (".$tabs["tagxobjeto"]["colunas"]["cod_tagxobjeto"]."), "
            . " CONSTRAINT fk_".$tabs["tagxobjeto"]["nome"]."_".$tabs["objeto"]["nome"]." FOREIGN KEY (".$tabs["tagxobjeto"]["colunas"]["cod_objeto"].") REFERENCES ".$tabs["objeto"]["nome"]." (".$tabs["objeto"]["colunas"]["cod_objeto"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE, "
            . " CONSTRAINT fk_".$tabs["tagxobjeto"]["nome"]."_".$tabs["tag"]["nome"]." FOREIGN KEY (".$tabs["tagxobjeto"]["colunas"]["cod_tag"].") REFERENCES ".$tabs["tag"]["nome"]." (".$tabs["tag"]["colunas"]["cod_tag"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE );".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["tagxobjeto"]["nome"]."_".$tabs["tagxobjeto"]["colunas"]["cod_tagxobjeto"]."_seq OWNED BY ".$tabs["tagxobjeto"]["nome"].".".$tabs["tagxobjeto"]["colunas"]["cod_tagxobjeto"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tagxobjeto"]["nome"]."_".$tabs["tagxobjeto"]["colunas"]["cod_objeto"]." ON ".$tabs["tagxobjeto"]["nome"]." USING btree (".$tabs["tagxobjeto"]["colunas"]["cod_objeto"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tagxobjeto"]["nome"]."_".$tabs["tagxobjeto"]["colunas"]["cod_tag"]." ON ".$tabs["tagxobjeto"]["nome"]." USING btree (".$tabs["tagxobjeto"]["colunas"]["cod_tag"]." ASC NULLS LAST);".PHP_EOL;

        // tbl_blob
        $sql .= " CREATE SEQUENCE ".$tabs["tbl_blob"]["nome"]."_".$tabs["tbl_blob"]["colunas"]["cod_blob"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= " CREATE TABLE ".$tabs["tbl_blob"]["nome"]." "
            . " (".$tabs["tbl_blob"]["colunas"]["cod_blob"]." integer NOT NULL DEFAULT nextval('".$tabs["tbl_blob"]["nome"]."_".$tabs["tbl_blob"]["colunas"]["cod_blob"]."_seq'::regclass), "
            . " ".$tabs["tbl_blob"]["colunas"]["cod_objeto"]." integer, "
            . " ".$tabs["tbl_blob"]["colunas"]["cod_propriedade"]." integer, "
            . " ".$tabs["tbl_blob"]["colunas"]["arquivo"]." character varying(255), "
            . " ".$tabs["tbl_blob"]["colunas"]["tamanho"]." integer, "
            . " CONSTRAINT pk_".$tabs["tbl_blob"]["nome"]." PRIMARY KEY (".$tabs["tbl_blob"]["colunas"]["cod_blob"]."), "
            . " CONSTRAINT fk_".$tabs["tbl_blob"]["nome"]."_".$tabs["objeto"]["nome"]." FOREIGN KEY (".$tabs["tbl_blob"]["colunas"]["cod_objeto"].") REFERENCES ".$tabs["objeto"]["nome"]." (".$tabs["objeto"]["colunas"]["cod_objeto"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE, "
            . " CONSTRAINT fk_".$tabs["tbl_blob"]["nome"]."_".$tabs["propriedade"]["nome"]." FOREIGN KEY (".$tabs["tbl_blob"]["colunas"]["cod_propriedade"].") REFERENCES ".$tabs["propriedade"]["nome"]." (".$tabs["propriedade"]["colunas"]["cod_propriedade"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE); ".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["tbl_blob"]["nome"]."_".$tabs["tbl_blob"]["colunas"]["cod_blob"]."_seq OWNED BY ".$tabs["tbl_blob"]["nome"].".".$tabs["tbl_blob"]["colunas"]["cod_blob"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tbl_blob"]["nome"]."_".$tabs["tbl_blob"]["colunas"]["cod_objeto"]." ON ".$tabs["tbl_blob"]["nome"]." USING btree (".$tabs["tbl_blob"]["colunas"]["cod_objeto"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tbl_blob"]["nome"]."_".$tabs["tbl_blob"]["colunas"]["cod_propriedade"]." ON ".$tabs["tbl_blob"]["nome"]." USING btree (".$tabs["tbl_blob"]["colunas"]["cod_propriedade"]." ASC NULLS LAST); ".PHP_EOL;

        // tbl_boolean
        $sql .= " CREATE SEQUENCE ".$tabs["tbl_boolean"]["nome"]."_".$tabs["tbl_boolean"]["colunas"]["cod_boolean"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= "CREATE TABLE ".$tabs["tbl_boolean"]["nome"]." "
            . " (".$tabs["tbl_boolean"]["colunas"]["cod_boolean"]." integer NOT NULL DEFAULT nextval('".$tabs["tbl_blob"]["nome"]."_".$tabs["tbl_blob"]["colunas"]["cod_blob"]."_seq'::regclass), "
            . " ".$tabs["tbl_boolean"]["colunas"]["cod_objeto"]." integer, "
            . " ".$tabs["tbl_boolean"]["colunas"]["cod_propriedade"]." integer, "
            . " ".$tabs["tbl_boolean"]["colunas"]["valor"]." smallint, "
            . " CONSTRAINT pk_".$tabs["tbl_boolean"]["nome"]." PRIMARY KEY (".$tabs["tbl_boolean"]["colunas"]["cod_boolean"]."), "
            . " CONSTRAINT fk_".$tabs["tbl_boolean"]["nome"]."_".$tabs["objeto"]["nome"]." FOREIGN KEY (".$tabs["tbl_boolean"]["colunas"]["cod_objeto"].") REFERENCES ".$tabs["objeto"]["nome"]." (".$tabs["objeto"]["colunas"]["cod_objeto"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE, "
            . " CONSTRAINT fk_".$tabs["tbl_boolean"]["nome"]."_".$tabs["propriedade"]["nome"]." FOREIGN KEY (".$tabs["tbl_boolean"]["colunas"]["cod_propriedade"].") REFERENCES ".$tabs["propriedade"]["nome"]." (".$tabs["propriedade"]["colunas"]["cod_propriedade"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE); ".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["tbl_boolean"]["nome"]."_".$tabs["tbl_boolean"]["colunas"]["cod_boolean"]."_seq OWNED BY ".$tabs["tbl_boolean"]["nome"].".".$tabs["tbl_boolean"]["colunas"]["cod_boolean"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tbl_boolean"]["nome"]."_".$tabs["tbl_boolean"]["colunas"]["cod_objeto"]." ON ".$tabs["tbl_boolean"]["nome"]." USING btree (".$tabs["tbl_boolean"]["colunas"]["cod_objeto"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tbl_boolean"]["nome"]."_".$tabs["tbl_boolean"]["colunas"]["cod_propriedade"]." ON ".$tabs["tbl_boolean"]["nome"]." USING btree (".$tabs["tbl_boolean"]["colunas"]["cod_propriedade"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tbl_boolean"]["nome"]."_".$tabs["tbl_boolean"]["colunas"]["valor"]." ON ".$tabs["tbl_boolean"]["nome"]." USING btree (".$tabs["tbl_boolean"]["colunas"]["valor"]." ASC NULLS LAST); ".PHP_EOL;

        // tbl_date
        $sql .= " CREATE SEQUENCE ".$tabs["tbl_date"]["nome"]."_".$tabs["tbl_date"]["colunas"]["cod_date"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= "CREATE TABLE ".$tabs["tbl_date"]["nome"]." "
            . " (".$tabs["tbl_date"]["colunas"]["cod_date"]." integer NOT NULL DEFAULT nextval('".$tabs["tbl_blob"]["nome"]."_".$tabs["tbl_blob"]["colunas"]["cod_blob"]."_seq'::regclass), "
            . " ".$tabs["tbl_date"]["colunas"]["cod_objeto"]." integer, "
            . " ".$tabs["tbl_date"]["colunas"]["cod_propriedade"]." integer, "
            . " ".$tabs["tbl_date"]["colunas"]["valor"]." bigint, "
            . " CONSTRAINT pk_".$tabs["tbl_date"]["nome"]." PRIMARY KEY (".$tabs["tbl_date"]["colunas"]["cod_date"]."), "
            . " CONSTRAINT fk_".$tabs["tbl_date"]["nome"]."_".$tabs["objeto"]["nome"]." FOREIGN KEY (".$tabs["tbl_date"]["colunas"]["cod_objeto"].") REFERENCES ".$tabs["objeto"]["nome"]." (".$tabs["objeto"]["colunas"]["cod_objeto"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE, "
            . " CONSTRAINT fk_".$tabs["tbl_date"]["nome"]."_".$tabs["propriedade"]["nome"]." FOREIGN KEY (".$tabs["tbl_date"]["colunas"]["cod_propriedade"].") REFERENCES ".$tabs["propriedade"]["nome"]." (".$tabs["propriedade"]["colunas"]["cod_propriedade"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE); ".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["tbl_date"]["nome"]."_".$tabs["tbl_date"]["colunas"]["cod_date"]."_seq OWNED BY ".$tabs["tbl_date"]["nome"].".".$tabs["tbl_date"]["colunas"]["cod_date"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tbl_date"]["nome"]."_".$tabs["tbl_date"]["colunas"]["cod_objeto"]." ON ".$tabs["tbl_date"]["nome"]." USING btree (".$tabs["tbl_date"]["colunas"]["cod_objeto"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tbl_date"]["nome"]."_".$tabs["tbl_date"]["colunas"]["cod_propriedade"]." ON ".$tabs["tbl_date"]["nome"]." USING btree (".$tabs["tbl_date"]["colunas"]["cod_propriedade"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tbl_date"]["nome"]."_".$tabs["tbl_date"]["colunas"]["valor"]." ON ".$tabs["tbl_date"]["nome"]." USING btree (".$tabs["tbl_date"]["colunas"]["valor"]." ASC NULLS LAST); ".PHP_EOL;

        // tbl_float
        $sql .= " CREATE SEQUENCE ".$tabs["tbl_float"]["nome"]."_".$tabs["tbl_float"]["colunas"]["cod_float"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= "CREATE TABLE ".$tabs["tbl_float"]["nome"]." "
            . " (".$tabs["tbl_float"]["colunas"]["cod_float"]." integer NOT NULL DEFAULT nextval('".$tabs["tbl_blob"]["nome"]."_".$tabs["tbl_blob"]["colunas"]["cod_blob"]."_seq'::regclass), "
            . " ".$tabs["tbl_float"]["colunas"]["cod_objeto"]." integer, "
            . " ".$tabs["tbl_float"]["colunas"]["cod_propriedade"]." integer, "
            . " ".$tabs["tbl_float"]["colunas"]["valor"]." double precision, "
            . " CONSTRAINT pk_".$tabs["tbl_float"]["nome"]." PRIMARY KEY (".$tabs["tbl_float"]["colunas"]["cod_float"]."), "
            . " CONSTRAINT fk_".$tabs["tbl_float"]["nome"]."_".$tabs["objeto"]["nome"]." FOREIGN KEY (".$tabs["tbl_float"]["colunas"]["cod_objeto"].") REFERENCES ".$tabs["objeto"]["nome"]." (".$tabs["objeto"]["colunas"]["cod_objeto"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE, "
            . " CONSTRAINT fk_".$tabs["tbl_float"]["nome"]."_".$tabs["propriedade"]["nome"]." FOREIGN KEY (".$tabs["tbl_float"]["colunas"]["cod_propriedade"].") REFERENCES ".$tabs["propriedade"]["nome"]." (".$tabs["propriedade"]["colunas"]["cod_propriedade"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE); ".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["tbl_float"]["nome"]."_".$tabs["tbl_float"]["colunas"]["cod_float"]."_seq OWNED BY ".$tabs["tbl_float"]["nome"].".".$tabs["tbl_float"]["colunas"]["cod_float"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tbl_float"]["nome"]."_".$tabs["tbl_float"]["colunas"]["cod_objeto"]." ON ".$tabs["tbl_float"]["nome"]." USING btree (".$tabs["tbl_float"]["colunas"]["cod_objeto"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tbl_float"]["nome"]."_".$tabs["tbl_float"]["colunas"]["cod_propriedade"]." ON ".$tabs["tbl_float"]["nome"]." USING btree (".$tabs["tbl_float"]["colunas"]["cod_propriedade"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tbl_float"]["nome"]."_".$tabs["tbl_float"]["colunas"]["valor"]." ON ".$tabs["tbl_float"]["nome"]." USING btree (".$tabs["tbl_float"]["colunas"]["valor"]." ASC NULLS LAST); ".PHP_EOL;

        // tbl_integer
        $sql .= " CREATE SEQUENCE ".$tabs["tbl_integer"]["nome"]."_".$tabs["tbl_integer"]["colunas"]["cod_integer"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= "CREATE TABLE ".$tabs["tbl_integer"]["nome"]." "
            . " (".$tabs["tbl_integer"]["colunas"]["cod_integer"]." integer NOT NULL DEFAULT nextval('".$tabs["tbl_blob"]["nome"]."_".$tabs["tbl_blob"]["colunas"]["cod_blob"]."_seq'::regclass), "
            . " ".$tabs["tbl_integer"]["colunas"]["cod_objeto"]." integer, "
            . " ".$tabs["tbl_integer"]["colunas"]["cod_propriedade"]." integer, "
            . " ".$tabs["tbl_integer"]["colunas"]["valor"]." integer, "
            . " CONSTRAINT pk_".$tabs["tbl_integer"]["nome"]." PRIMARY KEY (".$tabs["tbl_integer"]["colunas"]["cod_integer"]."), "
            . " CONSTRAINT fk_".$tabs["tbl_integer"]["nome"]."_".$tabs["objeto"]["nome"]." FOREIGN KEY (".$tabs["tbl_integer"]["colunas"]["cod_objeto"].") REFERENCES ".$tabs["objeto"]["nome"]." (".$tabs["objeto"]["colunas"]["cod_objeto"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE, "
            . " CONSTRAINT fk_".$tabs["tbl_integer"]["nome"]."_".$tabs["propriedade"]["nome"]." FOREIGN KEY (".$tabs["tbl_integer"]["colunas"]["cod_propriedade"].") REFERENCES ".$tabs["propriedade"]["nome"]." (".$tabs["propriedade"]["colunas"]["cod_propriedade"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE); ".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["tbl_integer"]["nome"]."_".$tabs["tbl_integer"]["colunas"]["cod_integer"]."_seq OWNED BY ".$tabs["tbl_integer"]["nome"].".".$tabs["tbl_integer"]["colunas"]["cod_integer"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tbl_integer"]["nome"]."_".$tabs["tbl_integer"]["colunas"]["cod_objeto"]." ON ".$tabs["tbl_integer"]["nome"]." USING btree (".$tabs["tbl_integer"]["colunas"]["cod_objeto"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tbl_integer"]["nome"]."_".$tabs["tbl_integer"]["colunas"]["cod_propriedade"]." ON ".$tabs["tbl_integer"]["nome"]." USING btree (".$tabs["tbl_integer"]["colunas"]["cod_propriedade"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tbl_integer"]["nome"]."_".$tabs["tbl_integer"]["colunas"]["valor"]." ON ".$tabs["tbl_integer"]["nome"]." USING btree (".$tabs["tbl_integer"]["colunas"]["valor"]." ASC NULLS LAST); ".PHP_EOL;

        // tbl_objref
        $sql .= " CREATE SEQUENCE ".$tabs["tbl_objref"]["nome"]."_".$tabs["tbl_objref"]["colunas"]["cod_objref"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= "CREATE TABLE ".$tabs["tbl_objref"]["nome"]." "
            . " (".$tabs["tbl_objref"]["colunas"]["cod_objref"]." integer NOT NULL DEFAULT nextval('".$tabs["tbl_blob"]["nome"]."_".$tabs["tbl_blob"]["colunas"]["cod_blob"]."_seq'::regclass), "
            . " ".$tabs["tbl_objref"]["colunas"]["cod_objeto"]." integer, "
            . " ".$tabs["tbl_objref"]["colunas"]["cod_propriedade"]." integer, "
            . " ".$tabs["tbl_objref"]["colunas"]["valor"]." integer, "
            . " CONSTRAINT pk_".$tabs["tbl_objref"]["nome"]." PRIMARY KEY (".$tabs["tbl_objref"]["colunas"]["cod_objref"]."), "
            . " CONSTRAINT fk_".$tabs["tbl_objref"]["nome"]."_".$tabs["objeto"]["nome"]." FOREIGN KEY (".$tabs["tbl_objref"]["colunas"]["cod_objeto"].") REFERENCES ".$tabs["objeto"]["nome"]." (".$tabs["objeto"]["colunas"]["cod_objeto"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE, "
            . " CONSTRAINT fk_".$tabs["tbl_objref"]["nome"]."_".$tabs["propriedade"]["nome"]." FOREIGN KEY (".$tabs["tbl_objref"]["colunas"]["cod_propriedade"].") REFERENCES ".$tabs["propriedade"]["nome"]." (".$tabs["propriedade"]["colunas"]["cod_propriedade"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE); ".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["tbl_objref"]["nome"]."_".$tabs["tbl_objref"]["colunas"]["cod_objref"]."_seq OWNED BY ".$tabs["tbl_objref"]["nome"].".".$tabs["tbl_objref"]["colunas"]["cod_objref"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tbl_objref"]["nome"]."_".$tabs["tbl_objref"]["colunas"]["cod_objeto"]." ON ".$tabs["tbl_objref"]["nome"]." USING btree (".$tabs["tbl_objref"]["colunas"]["cod_objeto"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tbl_objref"]["nome"]."_".$tabs["tbl_objref"]["colunas"]["cod_propriedade"]." ON ".$tabs["tbl_objref"]["nome"]." USING btree (".$tabs["tbl_objref"]["colunas"]["cod_propriedade"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tbl_objref"]["nome"]."_".$tabs["tbl_objref"]["colunas"]["valor"]." ON ".$tabs["tbl_objref"]["nome"]." USING btree (".$tabs["tbl_objref"]["colunas"]["valor"]." ASC NULLS LAST); ".PHP_EOL;

        // tbl_string
        $sql .= " CREATE SEQUENCE ".$tabs["tbl_string"]["nome"]."_".$tabs["tbl_string"]["colunas"]["cod_string"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= "CREATE TABLE ".$tabs["tbl_string"]["nome"]." "
            . " (".$tabs["tbl_string"]["colunas"]["cod_string"]." integer NOT NULL DEFAULT nextval('".$tabs["tbl_blob"]["nome"]."_".$tabs["tbl_blob"]["colunas"]["cod_blob"]."_seq'::regclass), "
            . " ".$tabs["tbl_string"]["colunas"]["cod_objeto"]." integer, "
            . " ".$tabs["tbl_string"]["colunas"]["cod_propriedade"]." integer, "
            . " ".$tabs["tbl_string"]["colunas"]["valor"]." character varying(1000), "
            . " CONSTRAINT pk_".$tabs["tbl_string"]["nome"]." PRIMARY KEY (".$tabs["tbl_string"]["colunas"]["cod_string"]."), "
            . " CONSTRAINT fk_".$tabs["tbl_string"]["nome"]."_".$tabs["objeto"]["nome"]." FOREIGN KEY (".$tabs["tbl_string"]["colunas"]["cod_objeto"].") REFERENCES ".$tabs["objeto"]["nome"]." (".$tabs["objeto"]["colunas"]["cod_objeto"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE, "
            . " CONSTRAINT fk_".$tabs["tbl_string"]["nome"]."_".$tabs["propriedade"]["nome"]." FOREIGN KEY (".$tabs["tbl_string"]["colunas"]["cod_propriedade"].") REFERENCES ".$tabs["propriedade"]["nome"]." (".$tabs["propriedade"]["colunas"]["cod_propriedade"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE); ".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["tbl_string"]["nome"]."_".$tabs["tbl_string"]["colunas"]["cod_string"]."_seq OWNED BY ".$tabs["tbl_string"]["nome"].".".$tabs["tbl_string"]["colunas"]["cod_string"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tbl_string"]["nome"]."_".$tabs["tbl_string"]["colunas"]["cod_objeto"]." ON ".$tabs["tbl_string"]["nome"]." USING btree (".$tabs["tbl_string"]["colunas"]["cod_objeto"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tbl_string"]["nome"]."_".$tabs["tbl_string"]["colunas"]["cod_propriedade"]." ON ".$tabs["tbl_string"]["nome"]." USING btree (".$tabs["tbl_string"]["colunas"]["cod_propriedade"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tbl_string"]["nome"]."_".$tabs["tbl_string"]["colunas"]["valor"]." ON ".$tabs["tbl_string"]["nome"]." USING btree (".$tabs["tbl_objref"]["colunas"]["valor"]." ASC NULLS LAST); ".PHP_EOL;

        // tbl_text
        $sql .= " CREATE SEQUENCE ".$tabs["tbl_text"]["nome"]."_".$tabs["tbl_text"]["colunas"]["cod_text"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= "CREATE TABLE ".$tabs["tbl_text"]["nome"]." "
        . " (".$tabs["tbl_text"]["colunas"]["cod_text"]." integer NOT NULL DEFAULT nextval('".$tabs["tbl_blob"]["nome"]."_".$tabs["tbl_blob"]["colunas"]["cod_blob"]."_seq'::regclass), "
        . " ".$tabs["tbl_text"]["colunas"]["cod_objeto"]." integer, "
        . " ".$tabs["tbl_text"]["colunas"]["cod_propriedade"]." integer, "
        . " ".$tabs["tbl_text"]["colunas"]["valor"]." text, "
        . " CONSTRAINT pk_".$tabs["tbl_text"]["nome"]." PRIMARY KEY (".$tabs["tbl_text"]["colunas"]["cod_text"]."), "
        . " CONSTRAINT fk_".$tabs["tbl_text"]["nome"]."_".$tabs["objeto"]["nome"]." FOREIGN KEY (".$tabs["tbl_text"]["colunas"]["cod_objeto"].") REFERENCES ".$tabs["objeto"]["nome"]." (".$tabs["objeto"]["colunas"]["cod_objeto"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE, "
        . " CONSTRAINT fk_".$tabs["tbl_text"]["nome"]."_".$tabs["propriedade"]["nome"]." FOREIGN KEY (".$tabs["tbl_text"]["colunas"]["cod_propriedade"].") REFERENCES ".$tabs["propriedade"]["nome"]." (".$tabs["propriedade"]["colunas"]["cod_propriedade"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE); ".PHP_EOL;
        $sql .= " ALTER SEQUENCE ".$tabs["tbl_text"]["nome"]."_".$tabs["tbl_text"]["colunas"]["cod_text"]."_seq OWNED BY ".$tabs["tbl_text"]["nome"].".".$tabs["tbl_text"]["colunas"]["cod_text"]."; ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tbl_text"]["nome"]."_".$tabs["tbl_text"]["colunas"]["cod_objeto"]." ON ".$tabs["tbl_text"]["nome"]." USING btree (".$tabs["tbl_text"]["colunas"]["cod_objeto"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["tbl_text"]["nome"]."_".$tabs["tbl_text"]["colunas"]["cod_propriedade"]." ON ".$tabs["tbl_text"]["nome"]." USING btree (".$tabs["tbl_text"]["colunas"]["cod_propriedade"]." ASC NULLS LAST); ".PHP_EOL;
        
        // usuarioxobjetoxperfil
        $sql .= " CREATE TABLE ".$tabs["usuarioxobjetoxperfil"]["nome"]." "
        . " (".$tabs["usuarioxobjetoxperfil"]["colunas"]["cod_usuario"]." integer NOT NULL, "
        . " ".$tabs["usuarioxobjetoxperfil"]["colunas"]["cod_objeto"]." integer NOT NULL, "
        . " ".$tabs["usuarioxobjetoxperfil"]["colunas"]["cod_perfil"]." integer NOT NULL, "
        . " CONSTRAINT pk_".$tabs["usuarioxobjetoxperfil"]["nome"]." PRIMARY KEY (".$tabs["usuarioxobjetoxperfil"]["colunas"]["cod_usuario"].", ".$tabs["usuarioxobjetoxperfil"]["colunas"]["cod_objeto"].", ".$tabs["usuarioxobjetoxperfil"]["colunas"]["cod_perfil"]."), "
        . " CONSTRAINT fk_".$tabs["usuarioxobjetoxperfil"]["nome"]."_".$tabs["usuario"]["nome"]." FOREIGN KEY (".$tabs["usuarioxobjetoxperfil"]["colunas"]["cod_usuario"].") REFERENCES ".$tabs["usuario"]["nome"]." (".$tabs["usuario"]["colunas"]["cod_usuario"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE, "
        . " CONSTRAINT fk_".$tabs["usuarioxobjetoxperfil"]["nome"]."_".$tabs["objeto"]["nome"]." FOREIGN KEY (".$tabs["usuarioxobjetoxperfil"]["colunas"]["cod_objeto"].") REFERENCES ".$tabs["objeto"]["nome"]." (".$tabs["objeto"]["colunas"]["cod_objeto"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE, "
        . " CONSTRAINT fk_".$tabs["usuarioxobjetoxperfil"]["nome"]."_".$tabs["perfil"]["nome"]." FOREIGN KEY (".$tabs["usuarioxobjetoxperfil"]["colunas"]["cod_perfil"].") REFERENCES ".$tabs["perfil"]["nome"]." (".$tabs["perfil"]["colunas"]["cod_perfil"].") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["usuarioxobjetoxperfil"]["nome"]."_".$tabs["usuarioxobjetoxperfil"]["colunas"]["cod_usuario"]." ON ".$tabs["usuarioxobjetoxperfil"]["nome"]." USING btree (".$tabs["usuarioxobjetoxperfil"]["colunas"]["cod_usuario"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["usuarioxobjetoxperfil"]["nome"]."_".$tabs["usuarioxobjetoxperfil"]["colunas"]["cod_objeto"]." ON ".$tabs["usuarioxobjetoxperfil"]["nome"]." USING btree (".$tabs["usuarioxobjetoxperfil"]["colunas"]["cod_objeto"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["usuarioxobjetoxperfil"]["nome"]."_".$tabs["usuarioxobjetoxperfil"]["colunas"]["cod_perfil"]." ON ".$tabs["usuarioxobjetoxperfil"]["nome"]." USING btree (".$tabs["usuarioxobjetoxperfil"]["colunas"]["cod_perfil"]." ASC NULLS LAST); ".PHP_EOL;
        
        // versaoobjeto
        $sql .= " CREATE SEQUENCE ".$tabs["versaoobjeto"]["nome"]."_".$tabs["versaoobjeto"]["colunas"]["cod_versaoobjeto"]."_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; ".PHP_EOL;
        $sql .= " CREATE TABLE ".$tabs["versaoobjeto"]["nome"]." "
            . " (".$tabs["versaoobjeto"]["colunas"]["cod_versaoobjeto"]." integer NOT NULL DEFAULT nextval('".$tabs["versaoobjeto"]["nome"]."_".$tabs["versaoobjeto"]["colunas"]["cod_versaoobjeto"]."_seq'::regclass) "
            . " ".$tabs["versaoobjeto"]["colunas"]["cod_objeto"]." integer NOT NULL, "
            . " ".$tabs["versaoobjeto"]["colunas"]["versao"]." integer NOT NULL, "
            . " ".$tabs["versaoobjeto"]["colunas"]["conteudo"]." text, "
            . " ".$tabs["versaoobjeto"]["colunas"]["data_criacao"]." timestamp without time zone, "
            . " ".$tabs["versaoobjeto"]["colunas"]["cod_usuario"]." integer, "
            . " ".$tabs["versaoobjeto"]["colunas"]["ip"]." character varying(30), "
            . " CONSTRAINT pk_".$tabs["versaoobjeto"]["nome"]." PRIMARY KEY (".$tabs["versaoobjeto"]["colunas"]["cod_versaoobjeto"].");".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["versaoobjeto"]["nome"]."_".$tabs["versaoobjeto"]["colunas"]["cod_objeto"]." ON ".$tabs["versaoobjeto"]["nome"]." USING btree (".$tabs["versaoobjeto"]["colunas"]["cod_objeto"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["versaoobjeto"]["nome"]."_".$tabs["versaoobjeto"]["colunas"]["cod_usuario"]." ON ".$tabs["versaoobjeto"]["nome"]." USING btree (".$tabs["versaoobjeto"]["colunas"]["cod_usuario"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["versaoobjeto"]["nome"]."_".$tabs["versaoobjeto"]["colunas"]["data_criacao"]." ON ".$tabs["versaoobjeto"]["nome"]." USING btree (".$tabs["versaoobjeto"]["colunas"]["data_criacao"]." ASC NULLS LAST); ".PHP_EOL;
        $sql .= " CREATE INDEX ix_".$tabs["versaoobjeto"]["nome"]."_".$tabs["versaoobjeto"]["colunas"]["versao"]." ON ".$tabs["versaoobjeto"]["nome"]." USING btree (".$tabs["versaoobjeto"]["colunas"]["versao"]." ASC NULLS LAST); ".PHP_EOL;

        $this->container["db_con"]->getCon()->Execute($sql);
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