--
-- PostgreSQL database dump
--

-- Dumped from database version 9.6.16
-- Dumped by pg_dump version 12.0

-- Started on 2019-11-23 20:30:22 UTC

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 2 (class 3079 OID 16385)
-- Name: unaccent; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS unaccent WITH SCHEMA public;


--
-- TOC entry 2526 (class 0 OID 0)
-- Dependencies: 2
-- Name: EXTENSION unaccent; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION unaccent IS 'text search dictionary that removes accents';


SET default_tablespace = '';

--
-- TOC entry 186 (class 1259 OID 16392)
-- Name: classe; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.classe (
    cod_classe integer NOT NULL,
    nome character varying(50),
    prefixo character varying(50),
    descricao character varying(255) DEFAULT ''::character varying NOT NULL,
    temfilhos smallint DEFAULT 0 NOT NULL,
    sistema smallint DEFAULT 0 NOT NULL,
    indexar smallint DEFAULT 0 NOT NULL
);


--
-- TOC entry 187 (class 1259 OID 16399)
-- Name: classe_cod_classe_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.classe_cod_classe_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2527 (class 0 OID 0)
-- Dependencies: 187
-- Name: classe_cod_classe_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.classe_cod_classe_seq OWNED BY public.classe.cod_classe;


--
-- TOC entry 188 (class 1259 OID 16401)
-- Name: classexfilhos; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.classexfilhos (
    cod_classe integer NOT NULL,
    cod_classe_filho integer NOT NULL
);


--
-- TOC entry 189 (class 1259 OID 16404)
-- Name: classexobjeto; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.classexobjeto (
    cod_classe integer NOT NULL,
    cod_objeto integer NOT NULL
);


--
-- TOC entry 190 (class 1259 OID 16420)
-- Name: infoperfil; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.infoperfil (
    cod_infoperfil integer NOT NULL,
    cod_perfil integer NOT NULL,
    acao character varying(200),
    script character varying(200),
    donooupublicado smallint DEFAULT 0,
    sopublicado smallint DEFAULT 0,
    sodono smallint DEFAULT 0,
    naomenu smallint DEFAULT 0,
    ordem smallint,
    icone character varying(30)
);


--
-- TOC entry 191 (class 1259 OID 16427)
-- Name: infoperfil_cod_infoperfil_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.infoperfil_cod_infoperfil_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2528 (class 0 OID 0)
-- Dependencies: 191
-- Name: infoperfil_cod_infoperfil_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.infoperfil_cod_infoperfil_seq OWNED BY public.infoperfil.cod_infoperfil;


--
-- TOC entry 192 (class 1259 OID 16429)
-- Name: logobjeto; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.logobjeto (
    cod_logobjeto integer NOT NULL,
    cod_objeto integer NOT NULL,
    estampa bigint NOT NULL,
    cod_usuario integer NOT NULL,
    cod_operacao smallint
);


--
-- TOC entry 193 (class 1259 OID 16432)
-- Name: logobjeto_cod_logobjeto_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.logobjeto_cod_logobjeto_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2529 (class 0 OID 0)
-- Dependencies: 193
-- Name: logobjeto_cod_logobjeto_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.logobjeto_cod_logobjeto_seq OWNED BY public.logobjeto.cod_logobjeto;


--
-- TOC entry 195 (class 1259 OID 16440)
-- Name: seq_logworkflow; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.seq_logworkflow
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 196 (class 1259 OID 16442)
-- Name: logworkflow; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.logworkflow (
    cod_logworkflow integer DEFAULT nextval('public.seq_logworkflow'::regclass) NOT NULL,
    cod_objeto integer,
    cod_usuario integer,
    mensagem text,
    cod_status integer,
    estampa bigint
);


--
-- TOC entry 197 (class 1259 OID 16449)
-- Name: objeto; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.objeto (
    cod_objeto integer NOT NULL,
    cod_pai integer,
    cod_classe integer NOT NULL,
    cod_usuario integer DEFAULT 1 NOT NULL,
    cod_pele integer,
    cod_status integer DEFAULT 1 NOT NULL,
    titulo character varying(400) NOT NULL,
    descricao text,
    data_publicacao bigint NOT NULL,
    data_validade bigint NOT NULL,
    script_exibir character varying(255),
    apagado smallint DEFAULT 0 NOT NULL,
    objetosistema smallint DEFAULT 0 NOT NULL,
    peso integer DEFAULT 0,
    data_exclusao bigint,
    url_amigavel character varying(500),
    versao integer DEFAULT 1 NOT NULL,
    versao_publicada integer
);


--
-- TOC entry 198 (class 1259 OID 16461)
-- Name: objeto_cod_objeto_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.objeto_cod_objeto_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2530 (class 0 OID 0)
-- Dependencies: 198
-- Name: objeto_cod_objeto_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.objeto_cod_objeto_seq OWNED BY public.objeto.cod_objeto;


--
-- TOC entry 199 (class 1259 OID 16463)
-- Name: parentesco; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.parentesco (
    cod_objeto integer NOT NULL,
    cod_pai integer NOT NULL,
    ordem integer NOT NULL
);


--
-- TOC entry 200 (class 1259 OID 16466)
-- Name: pele; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.pele (
    cod_pele integer NOT NULL,
    nome character varying(50),
    prefixo character varying(50),
    publica integer DEFAULT 0
);


--
-- TOC entry 201 (class 1259 OID 16470)
-- Name: pele_cod_pele_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.pele_cod_pele_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2531 (class 0 OID 0)
-- Dependencies: 201
-- Name: pele_cod_pele_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.pele_cod_pele_seq OWNED BY public.pele.cod_pele;


--
-- TOC entry 202 (class 1259 OID 16472)
-- Name: seq_pendencia; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.seq_pendencia
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 203 (class 1259 OID 16474)
-- Name: pendencia; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.pendencia (
    cod_pendencia integer DEFAULT nextval('public.seq_pendencia'::regclass) NOT NULL,
    cod_usuario integer DEFAULT 1 NOT NULL,
    cod_objeto integer DEFAULT 0 NOT NULL
);


--
-- TOC entry 204 (class 1259 OID 16480)
-- Name: perfil; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.perfil (
    cod_perfil integer NOT NULL,
    nome character varying(50) NOT NULL,
    cod_perfil_pai integer
);


--
-- TOC entry 205 (class 1259 OID 16483)
-- Name: perfil_cod_perfil_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.perfil_cod_perfil_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2532 (class 0 OID 0)
-- Dependencies: 205
-- Name: perfil_cod_perfil_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.perfil_cod_perfil_seq OWNED BY public.perfil.cod_perfil;


--
-- TOC entry 206 (class 1259 OID 16485)
-- Name: seq_pilha; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.seq_pilha
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 207 (class 1259 OID 16487)
-- Name: pilha; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.pilha (
    cod_pilha integer DEFAULT nextval('public.seq_pilha'::regclass) NOT NULL,
    cod_objeto integer,
    cod_usuario integer,
    cod_tipo integer,
    datahora double precision
);


--
-- TOC entry 208 (class 1259 OID 16491)
-- Name: propriedade; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.propriedade (
    cod_propriedade integer NOT NULL,
    cod_classe integer DEFAULT 0 NOT NULL,
    cod_tipodado integer DEFAULT 0 NOT NULL,
    cod_referencia_classe integer,
    campo_ref character varying(50),
    nome character varying(50),
    posicao smallint DEFAULT 0,
    descricao character varying(255),
    rotulo character varying(50),
    rot1booleano character varying(50),
    rot2booleano character varying(50),
    obrigatorio smallint,
    seguranca smallint,
    valorpadrao character varying(200)
);


--
-- TOC entry 209 (class 1259 OID 16500)
-- Name: propriedade_cod_propriedade_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.propriedade_cod_propriedade_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2533 (class 0 OID 0)
-- Dependencies: 209
-- Name: propriedade_cod_propriedade_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.propriedade_cod_propriedade_seq OWNED BY public.propriedade.cod_propriedade;


--
-- TOC entry 194 (class 1259 OID 16434)
-- Name: seq_logrobo; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.seq_logrobo
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 211 (class 1259 OID 16508)
-- Name: seq_perfil; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.seq_perfil
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 210 (class 1259 OID 16502)
-- Name: seq_robo; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.seq_robo
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 212 (class 1259 OID 16510)
-- Name: seq_unlock_table; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.seq_unlock_table
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 213 (class 1259 OID 16512)
-- Name: status; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.status (
    cod_status integer NOT NULL,
    nome character varying(50)
);


--
-- TOC entry 214 (class 1259 OID 16515)
-- Name: status_cod_status_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.status_cod_status_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2534 (class 0 OID 0)
-- Dependencies: 214
-- Name: status_cod_status_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.status_cod_status_seq OWNED BY public.status.cod_status;


--
-- TOC entry 215 (class 1259 OID 16517)
-- Name: tag; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tag (
    cod_tag integer NOT NULL,
    nome_tag character varying(200)
);


--
-- TOC entry 216 (class 1259 OID 16520)
-- Name: tag_cod_tag_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tag_cod_tag_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2535 (class 0 OID 0)
-- Dependencies: 216
-- Name: tag_cod_tag_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.tag_cod_tag_seq OWNED BY public.tag.cod_tag;


--
-- TOC entry 217 (class 1259 OID 16522)
-- Name: tagxobjeto; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tagxobjeto (
    cod_tag integer NOT NULL,
    cod_objeto integer NOT NULL,
    cod_tagxobjeto integer NOT NULL
);


--
-- TOC entry 218 (class 1259 OID 16525)
-- Name: tagxobjeto_cod_tagxobjeto_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tagxobjeto_cod_tagxobjeto_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2536 (class 0 OID 0)
-- Dependencies: 218
-- Name: tagxobjeto_cod_tagxobjeto_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.tagxobjeto_cod_tagxobjeto_seq OWNED BY public.tagxobjeto.cod_tagxobjeto;


--
-- TOC entry 219 (class 1259 OID 16527)
-- Name: tbl_blob; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tbl_blob (
    cod_blob integer NOT NULL,
    cod_objeto integer,
    cod_propriedade integer,
    arquivo character varying(255),
    tamanho integer
);


--
-- TOC entry 220 (class 1259 OID 16530)
-- Name: tbl_blob_cod_blob_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tbl_blob_cod_blob_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2537 (class 0 OID 0)
-- Dependencies: 220
-- Name: tbl_blob_cod_blob_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.tbl_blob_cod_blob_seq OWNED BY public.tbl_blob.cod_blob;


--
-- TOC entry 221 (class 1259 OID 16532)
-- Name: tbl_boolean; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tbl_boolean (
    cod_boolean integer NOT NULL,
    cod_objeto integer,
    cod_propriedade integer,
    valor smallint
);


--
-- TOC entry 222 (class 1259 OID 16535)
-- Name: tbl_boolean_cod_boolean_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tbl_boolean_cod_boolean_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2538 (class 0 OID 0)
-- Dependencies: 222
-- Name: tbl_boolean_cod_boolean_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.tbl_boolean_cod_boolean_seq OWNED BY public.tbl_boolean.cod_boolean;


--
-- TOC entry 223 (class 1259 OID 16537)
-- Name: tbl_date; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tbl_date (
    cod_date integer NOT NULL,
    cod_objeto integer,
    cod_propriedade integer,
    valor bigint
);


--
-- TOC entry 224 (class 1259 OID 16540)
-- Name: tbl_date_cod_date_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tbl_date_cod_date_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2539 (class 0 OID 0)
-- Dependencies: 224
-- Name: tbl_date_cod_date_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.tbl_date_cod_date_seq OWNED BY public.tbl_date.cod_date;


--
-- TOC entry 225 (class 1259 OID 16542)
-- Name: tbl_float; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tbl_float (
    cod_float integer NOT NULL,
    cod_objeto integer,
    cod_propriedade integer,
    valor double precision
);


--
-- TOC entry 226 (class 1259 OID 16545)
-- Name: tbl_float_cod_float_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tbl_float_cod_float_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2540 (class 0 OID 0)
-- Dependencies: 226
-- Name: tbl_float_cod_float_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.tbl_float_cod_float_seq OWNED BY public.tbl_float.cod_float;


--
-- TOC entry 227 (class 1259 OID 16547)
-- Name: tbl_integer; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tbl_integer (
    cod_integer integer NOT NULL,
    cod_objeto integer,
    cod_propriedade integer,
    valor integer
);


--
-- TOC entry 228 (class 1259 OID 16550)
-- Name: tbl_integer_cod_integer_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tbl_integer_cod_integer_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2541 (class 0 OID 0)
-- Dependencies: 228
-- Name: tbl_integer_cod_integer_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.tbl_integer_cod_integer_seq OWNED BY public.tbl_integer.cod_integer;


--
-- TOC entry 229 (class 1259 OID 16552)
-- Name: tbl_objref; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tbl_objref (
    cod_objref integer NOT NULL,
    cod_objeto integer,
    cod_propriedade integer,
    valor integer
);


--
-- TOC entry 230 (class 1259 OID 16555)
-- Name: tbl_objref_cod_objref_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tbl_objref_cod_objref_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2542 (class 0 OID 0)
-- Dependencies: 230
-- Name: tbl_objref_cod_objref_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.tbl_objref_cod_objref_seq OWNED BY public.tbl_objref.cod_objref;


--
-- TOC entry 231 (class 1259 OID 16557)
-- Name: tbl_string; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tbl_string (
    cod_string integer NOT NULL,
    cod_objeto integer,
    cod_propriedade integer,
    valor character varying(1000)
);


--
-- TOC entry 232 (class 1259 OID 16563)
-- Name: tbl_string_cod_string_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tbl_string_cod_string_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2543 (class 0 OID 0)
-- Dependencies: 232
-- Name: tbl_string_cod_string_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.tbl_string_cod_string_seq OWNED BY public.tbl_string.cod_string;


--
-- TOC entry 233 (class 1259 OID 16565)
-- Name: tbl_text; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tbl_text (
    cod_text integer NOT NULL,
    cod_objeto integer,
    cod_propriedade integer,
    valor text
);


--
-- TOC entry 234 (class 1259 OID 16571)
-- Name: tbl_text_cod_text_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tbl_text_cod_text_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2544 (class 0 OID 0)
-- Dependencies: 234
-- Name: tbl_text_cod_text_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.tbl_text_cod_text_seq OWNED BY public.tbl_text.cod_text;


--
-- TOC entry 235 (class 1259 OID 16577)
-- Name: tipodado; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tipodado (
    cod_tipodado integer NOT NULL,
    nome character varying(50),
    tabela character varying(50),
    delimitador character varying(1)
);


--
-- TOC entry 236 (class 1259 OID 16580)
-- Name: tipodado_cod_tipodado_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tipodado_cod_tipodado_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2545 (class 0 OID 0)
-- Dependencies: 236
-- Name: tipodado_cod_tipodado_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.tipodado_cod_tipodado_seq OWNED BY public.tipodado.cod_tipodado;


--
-- TOC entry 237 (class 1259 OID 16586)
-- Name: usuario; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.usuario (
    cod_usuario integer NOT NULL,
    secao character varying(255),
    nome character varying(255),
    login character varying(55),
    email character varying(255),
    ramal character varying(50),
    senha character varying(32),
    chefia integer,
    valido smallint,
    data_atualizacao bigint,
    altera_senha smallint,
    ldap smallint
);


--
-- TOC entry 238 (class 1259 OID 16592)
-- Name: usuario_cod_usuario_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.usuario_cod_usuario_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2546 (class 0 OID 0)
-- Dependencies: 238
-- Name: usuario_cod_usuario_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.usuario_cod_usuario_seq OWNED BY public.usuario.cod_usuario;


--
-- TOC entry 239 (class 1259 OID 16594)
-- Name: usuarioxobjetoxperfil; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.usuarioxobjetoxperfil (
    cod_usuario integer DEFAULT 0 NOT NULL,
    cod_objeto integer DEFAULT 0 NOT NULL,
    cod_perfil integer DEFAULT 0 NOT NULL
);


--
-- TOC entry 240 (class 1259 OID 16600)
-- Name: versaoobjeto; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.versaoobjeto (
    cod_versaoobjeto integer NOT NULL,
    cod_objeto integer NOT NULL,
    versao integer NOT NULL,
    conteudo text,
    data_criacao timestamp without time zone,
    cod_usuario integer,
    ip character varying(30)
);


--
-- TOC entry 241 (class 1259 OID 16606)
-- Name: versaoobjeto_cod_versaoobjeto_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.versaoobjeto_cod_versaoobjeto_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2547 (class 0 OID 0)
-- Dependencies: 241
-- Name: versaoobjeto_cod_versaoobjeto_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.versaoobjeto_cod_versaoobjeto_seq OWNED BY public.versaoobjeto.cod_versaoobjeto;


--
-- TOC entry 2185 (class 2604 OID 16608)
-- Name: classe cod_classe; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.classe ALTER COLUMN cod_classe SET DEFAULT nextval('public.classe_cod_classe_seq'::regclass);


--
-- TOC entry 2190 (class 2604 OID 16611)
-- Name: infoperfil cod_infoperfil; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.infoperfil ALTER COLUMN cod_infoperfil SET DEFAULT nextval('public.infoperfil_cod_infoperfil_seq'::regclass);


--
-- TOC entry 2191 (class 2604 OID 16612)
-- Name: logobjeto cod_logobjeto; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.logobjeto ALTER COLUMN cod_logobjeto SET DEFAULT nextval('public.logobjeto_cod_logobjeto_seq'::regclass);


--
-- TOC entry 2199 (class 2604 OID 16613)
-- Name: objeto cod_objeto; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.objeto ALTER COLUMN cod_objeto SET DEFAULT nextval('public.objeto_cod_objeto_seq'::regclass);


--
-- TOC entry 2201 (class 2604 OID 16614)
-- Name: pele cod_pele; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pele ALTER COLUMN cod_pele SET DEFAULT nextval('public.pele_cod_pele_seq'::regclass);


--
-- TOC entry 2205 (class 2604 OID 16615)
-- Name: perfil cod_perfil; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.perfil ALTER COLUMN cod_perfil SET DEFAULT nextval('public.perfil_cod_perfil_seq'::regclass);


--
-- TOC entry 2210 (class 2604 OID 16616)
-- Name: propriedade cod_propriedade; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.propriedade ALTER COLUMN cod_propriedade SET DEFAULT nextval('public.propriedade_cod_propriedade_seq'::regclass);


--
-- TOC entry 2211 (class 2604 OID 16617)
-- Name: status cod_status; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.status ALTER COLUMN cod_status SET DEFAULT nextval('public.status_cod_status_seq'::regclass);


--
-- TOC entry 2212 (class 2604 OID 16618)
-- Name: tag cod_tag; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tag ALTER COLUMN cod_tag SET DEFAULT nextval('public.tag_cod_tag_seq'::regclass);


--
-- TOC entry 2213 (class 2604 OID 16619)
-- Name: tagxobjeto cod_tagxobjeto; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tagxobjeto ALTER COLUMN cod_tagxobjeto SET DEFAULT nextval('public.tagxobjeto_cod_tagxobjeto_seq'::regclass);


--
-- TOC entry 2214 (class 2604 OID 16620)
-- Name: tbl_blob cod_blob; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_blob ALTER COLUMN cod_blob SET DEFAULT nextval('public.tbl_blob_cod_blob_seq'::regclass);


--
-- TOC entry 2215 (class 2604 OID 16621)
-- Name: tbl_boolean cod_boolean; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_boolean ALTER COLUMN cod_boolean SET DEFAULT nextval('public.tbl_boolean_cod_boolean_seq'::regclass);


--
-- TOC entry 2216 (class 2604 OID 16622)
-- Name: tbl_date cod_date; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_date ALTER COLUMN cod_date SET DEFAULT nextval('public.tbl_date_cod_date_seq'::regclass);


--
-- TOC entry 2217 (class 2604 OID 16623)
-- Name: tbl_float cod_float; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_float ALTER COLUMN cod_float SET DEFAULT nextval('public.tbl_float_cod_float_seq'::regclass);


--
-- TOC entry 2218 (class 2604 OID 16624)
-- Name: tbl_integer cod_integer; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_integer ALTER COLUMN cod_integer SET DEFAULT nextval('public.tbl_integer_cod_integer_seq'::regclass);


--
-- TOC entry 2219 (class 2604 OID 16625)
-- Name: tbl_objref cod_objref; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_objref ALTER COLUMN cod_objref SET DEFAULT nextval('public.tbl_objref_cod_objref_seq'::regclass);


--
-- TOC entry 2220 (class 2604 OID 16626)
-- Name: tbl_string cod_string; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_string ALTER COLUMN cod_string SET DEFAULT nextval('public.tbl_string_cod_string_seq'::regclass);


--
-- TOC entry 2221 (class 2604 OID 16627)
-- Name: tbl_text cod_text; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_text ALTER COLUMN cod_text SET DEFAULT nextval('public.tbl_text_cod_text_seq'::regclass);


--
-- TOC entry 2222 (class 2604 OID 16628)
-- Name: tipodado cod_tipodado; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tipodado ALTER COLUMN cod_tipodado SET DEFAULT nextval('public.tipodado_cod_tipodado_seq'::regclass);


--
-- TOC entry 2223 (class 2604 OID 16629)
-- Name: usuario cod_usuario; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario ALTER COLUMN cod_usuario SET DEFAULT nextval('public.usuario_cod_usuario_seq'::regclass);


--
-- TOC entry 2227 (class 2604 OID 16630)
-- Name: versaoobjeto cod_versaoobjeto; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.versaoobjeto ALTER COLUMN cod_versaoobjeto SET DEFAULT nextval('public.versaoobjeto_cod_versaoobjeto_seq'::regclass);


--
-- TOC entry 2283 (class 2606 OID 16649)
-- Name: pendencia pk__pendencia__540c7b00; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pendencia
    ADD CONSTRAINT pk__pendencia__540c7b00 PRIMARY KEY (cod_objeto, cod_usuario);


--
-- TOC entry 2290 (class 2606 OID 16651)
-- Name: pilha pk__pilha__5d95e53a; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pilha
    ADD CONSTRAINT pk__pilha__5d95e53a PRIMARY KEY (cod_pilha);


--
-- TOC entry 2357 (class 2606 OID 16657)
-- Name: usuarioxobjetoxperfil pk__usuarioxobjetoxp__2610a626; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuarioxobjetoxperfil
    ADD CONSTRAINT pk__usuarioxobjetoxp__2610a626 PRIMARY KEY (cod_objeto, cod_perfil, cod_usuario);


--
-- TOC entry 2312 (class 2606 OID 16659)
-- Name: tbl_blob pk_blob; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_blob
    ADD CONSTRAINT pk_blob PRIMARY KEY (cod_blob);


--
-- TOC entry 2317 (class 2606 OID 16661)
-- Name: tbl_boolean pk_boolean; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_boolean
    ADD CONSTRAINT pk_boolean PRIMARY KEY (cod_boolean);


--
-- TOC entry 2233 (class 2606 OID 16663)
-- Name: classe pk_classe; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.classe
    ADD CONSTRAINT pk_classe PRIMARY KEY (cod_classe);


--
-- TOC entry 2237 (class 2606 OID 16665)
-- Name: classexfilhos pk_classexfilhos; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.classexfilhos
    ADD CONSTRAINT pk_classexfilhos PRIMARY KEY (cod_classe, cod_classe_filho);


--
-- TOC entry 2241 (class 2606 OID 16667)
-- Name: classexobjeto pk_classexobjeto; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.classexobjeto
    ADD CONSTRAINT pk_classexobjeto PRIMARY KEY (cod_classe, cod_objeto);


--
-- TOC entry 2322 (class 2606 OID 16671)
-- Name: tbl_date pk_date; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_date
    ADD CONSTRAINT pk_date PRIMARY KEY (cod_date);


--
-- TOC entry 2327 (class 2606 OID 16673)
-- Name: tbl_float pk_float; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_float
    ADD CONSTRAINT pk_float PRIMARY KEY (cod_float);


--
-- TOC entry 2244 (class 2606 OID 16677)
-- Name: infoperfil pk_infoperfil; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.infoperfil
    ADD CONSTRAINT pk_infoperfil PRIMARY KEY (cod_infoperfil);


--
-- TOC entry 2332 (class 2606 OID 16679)
-- Name: tbl_integer pk_integer; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_integer
    ADD CONSTRAINT pk_integer PRIMARY KEY (cod_integer);


--
-- TOC entry 2250 (class 2606 OID 16681)
-- Name: logobjeto pk_logobjeto; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.logobjeto
    ADD CONSTRAINT pk_logobjeto PRIMARY KEY (cod_logobjeto);


--
-- TOC entry 2256 (class 2606 OID 16683)
-- Name: logworkflow pk_logworkflow; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.logworkflow
    ADD CONSTRAINT pk_logworkflow PRIMARY KEY (cod_logworkflow);


--
-- TOC entry 2270 (class 2606 OID 16685)
-- Name: objeto pk_objeto; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.objeto
    ADD CONSTRAINT pk_objeto PRIMARY KEY (cod_objeto);


--
-- TOC entry 2337 (class 2606 OID 16687)
-- Name: tbl_objref pk_objref; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_objref
    ADD CONSTRAINT pk_objref PRIMARY KEY (cod_objref);


--
-- TOC entry 2275 (class 2606 OID 16689)
-- Name: parentesco pk_parentesco; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.parentesco
    ADD CONSTRAINT pk_parentesco PRIMARY KEY (cod_objeto, cod_pai);


--
-- TOC entry 2279 (class 2606 OID 16691)
-- Name: pele pk_pele; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pele
    ADD CONSTRAINT pk_pele PRIMARY KEY (cod_pele);


--
-- TOC entry 2285 (class 2606 OID 16693)
-- Name: perfil pk_perfil; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.perfil
    ADD CONSTRAINT pk_perfil PRIMARY KEY (cod_perfil);


--
-- TOC entry 2298 (class 2606 OID 16695)
-- Name: propriedade pk_propriedade; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.propriedade
    ADD CONSTRAINT pk_propriedade PRIMARY KEY (cod_propriedade);


--
-- TOC entry 2300 (class 2606 OID 16697)
-- Name: status pk_status; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.status
    ADD CONSTRAINT pk_status PRIMARY KEY (cod_status);


--
-- TOC entry 2342 (class 2606 OID 16699)
-- Name: tbl_string pk_string; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_string
    ADD CONSTRAINT pk_string PRIMARY KEY (cod_string);


--
-- TOC entry 2303 (class 2606 OID 16701)
-- Name: tag pk_tag; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tag
    ADD CONSTRAINT pk_tag PRIMARY KEY (cod_tag);


--
-- TOC entry 2346 (class 2606 OID 16703)
-- Name: tbl_text pk_text; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_text
    ADD CONSTRAINT pk_text PRIMARY KEY (cod_text);


--
-- TOC entry 2349 (class 2606 OID 16705)
-- Name: tipodado pk_tipodado; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tipodado
    ADD CONSTRAINT pk_tipodado PRIMARY KEY (cod_tipodado);


--
-- TOC entry 2352 (class 2606 OID 16707)
-- Name: usuario pk_usuario; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT pk_usuario PRIMARY KEY (cod_usuario);


--
-- TOC entry 2307 (class 2606 OID 16709)
-- Name: tagxobjeto tagxobjeto_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tagxobjeto
    ADD CONSTRAINT tagxobjeto_pkey PRIMARY KEY (cod_tagxobjeto);


--
-- TOC entry 2360 (class 2606 OID 16711)
-- Name: versaoobjeto versaoobjeto_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.versaoobjeto
    ADD CONSTRAINT versaoobjeto_pkey PRIMARY KEY (cod_versaoobjeto);


--
-- TOC entry 2358 (class 1259 OID 16712)
-- Name: fki_fk_versaoobjeto_objeto; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX fki_fk_versaoobjeto_objeto ON public.versaoobjeto USING btree (cod_objeto);


--
-- TOC entry 2228 (class 1259 OID 16713)
-- Name: ix_classe_indexar; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_classe_indexar ON public.classe USING btree (indexar);


--
-- TOC entry 2229 (class 1259 OID 16714)
-- Name: ix_classe_nome; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_classe_nome ON public.classe USING btree (nome);


--
-- TOC entry 2230 (class 1259 OID 16715)
-- Name: ix_classe_prefixo; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_classe_prefixo ON public.classe USING btree (prefixo);


--
-- TOC entry 2231 (class 1259 OID 16716)
-- Name: ix_classe_temfilhos; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_classe_temfilhos ON public.classe USING btree (temfilhos);


--
-- TOC entry 2234 (class 1259 OID 16717)
-- Name: ix_classexfilhos_cod_classe; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_classexfilhos_cod_classe ON public.classexfilhos USING btree (cod_classe);


--
-- TOC entry 2235 (class 1259 OID 16718)
-- Name: ix_classexfilhos_cod_classe_filho; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_classexfilhos_cod_classe_filho ON public.classexfilhos USING btree (cod_classe_filho);


--
-- TOC entry 2238 (class 1259 OID 16719)
-- Name: ix_classexobjeto_cod_classe; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_classexobjeto_cod_classe ON public.classexobjeto USING btree (cod_classe);


--
-- TOC entry 2239 (class 1259 OID 16720)
-- Name: ix_classexobjeto_cod_objeto; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_classexobjeto_cod_objeto ON public.classexobjeto USING btree (cod_objeto);


--
-- TOC entry 2242 (class 1259 OID 16721)
-- Name: ix_infoperfil_cod_perfil; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_infoperfil_cod_perfil ON public.infoperfil USING btree (cod_perfil);


--
-- TOC entry 2245 (class 1259 OID 16722)
-- Name: ix_logobjeto_cod_objeto; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_logobjeto_cod_objeto ON public.logobjeto USING btree (cod_objeto);


--
-- TOC entry 2246 (class 1259 OID 16723)
-- Name: ix_logobjeto_cod_operacao; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_logobjeto_cod_operacao ON public.logobjeto USING btree (cod_operacao);


--
-- TOC entry 2247 (class 1259 OID 16724)
-- Name: ix_logobjeto_cod_usuario; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_logobjeto_cod_usuario ON public.logobjeto USING btree (cod_usuario);


--
-- TOC entry 2248 (class 1259 OID 16725)
-- Name: ix_logobjeto_estampa; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_logobjeto_estampa ON public.logobjeto USING btree (estampa);


--
-- TOC entry 2251 (class 1259 OID 16726)
-- Name: ix_logworkflow_cod_objeto; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_logworkflow_cod_objeto ON public.logworkflow USING btree (cod_objeto);


--
-- TOC entry 2252 (class 1259 OID 16727)
-- Name: ix_logworkflow_cod_status; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_logworkflow_cod_status ON public.logworkflow USING btree (cod_status);


--
-- TOC entry 2253 (class 1259 OID 16728)
-- Name: ix_logworkflow_cod_usuario; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_logworkflow_cod_usuario ON public.logworkflow USING btree (cod_usuario);


--
-- TOC entry 2254 (class 1259 OID 16729)
-- Name: ix_logworkflow_estampa; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_logworkflow_estampa ON public.logworkflow USING btree (estampa);


--
-- TOC entry 2257 (class 1259 OID 16730)
-- Name: ix_objeto_apagado; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_objeto_apagado ON public.objeto USING btree (apagado);


--
-- TOC entry 2258 (class 1259 OID 16731)
-- Name: ix_objeto_cod_classe; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_objeto_cod_classe ON public.objeto USING btree (cod_classe);


--
-- TOC entry 2259 (class 1259 OID 16732)
-- Name: ix_objeto_cod_pai; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_objeto_cod_pai ON public.objeto USING btree (cod_pai);


--
-- TOC entry 2260 (class 1259 OID 16733)
-- Name: ix_objeto_cod_pele; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_objeto_cod_pele ON public.objeto USING btree (cod_pele);


--
-- TOC entry 2261 (class 1259 OID 16734)
-- Name: ix_objeto_cod_status; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_objeto_cod_status ON public.objeto USING btree (cod_status);


--
-- TOC entry 2262 (class 1259 OID 16735)
-- Name: ix_objeto_cod_usuario; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_objeto_cod_usuario ON public.objeto USING btree (cod_usuario);


--
-- TOC entry 2263 (class 1259 OID 16736)
-- Name: ix_objeto_data_publicacao; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_objeto_data_publicacao ON public.objeto USING btree (data_publicacao);


--
-- TOC entry 2264 (class 1259 OID 16737)
-- Name: ix_objeto_data_validade; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_objeto_data_validade ON public.objeto USING btree (data_validade);


--
-- TOC entry 2265 (class 1259 OID 16738)
-- Name: ix_objeto_descricao; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_objeto_descricao ON public.objeto USING btree (descricao);


--
-- TOC entry 2266 (class 1259 OID 16739)
-- Name: ix_objeto_peso; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_objeto_peso ON public.objeto USING btree (peso);


--
-- TOC entry 2267 (class 1259 OID 17009)
-- Name: ix_objeto_titulo; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_objeto_titulo ON public.objeto USING btree (titulo);


--
-- TOC entry 2268 (class 1259 OID 16741)
-- Name: ix_objeto_url_amigavel; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_objeto_url_amigavel ON public.objeto USING btree (url_amigavel);


--
-- TOC entry 2271 (class 1259 OID 16742)
-- Name: ix_parentesco_cod_objeto; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_parentesco_cod_objeto ON public.parentesco USING btree (cod_objeto);


--
-- TOC entry 2272 (class 1259 OID 16743)
-- Name: ix_parentesco_cod_pai; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_parentesco_cod_pai ON public.parentesco USING btree (cod_pai);


--
-- TOC entry 2273 (class 1259 OID 16744)
-- Name: ix_parentesco_ordem; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_parentesco_ordem ON public.parentesco USING btree (ordem);


--
-- TOC entry 2276 (class 1259 OID 16745)
-- Name: ix_pele_prefixo; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_pele_prefixo ON public.pele USING btree (prefixo);


--
-- TOC entry 2277 (class 1259 OID 16746)
-- Name: ix_pele_publica; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_pele_publica ON public.pele USING btree (publica);


--
-- TOC entry 2280 (class 1259 OID 16747)
-- Name: ix_pendencia_cod_objeto; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_pendencia_cod_objeto ON public.pendencia USING btree (cod_objeto);


--
-- TOC entry 2281 (class 1259 OID 16748)
-- Name: ix_pendencia_cod_usuario; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_pendencia_cod_usuario ON public.pendencia USING btree (cod_usuario);


--
-- TOC entry 2286 (class 1259 OID 16749)
-- Name: ix_pilha_cod_objeto; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_pilha_cod_objeto ON public.pilha USING btree (cod_objeto);


--
-- TOC entry 2287 (class 1259 OID 16750)
-- Name: ix_pilha_cod_usuario; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_pilha_cod_usuario ON public.pilha USING btree (cod_usuario);


--
-- TOC entry 2288 (class 1259 OID 16751)
-- Name: ix_pilha_datahora; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_pilha_datahora ON public.pilha USING btree (datahora);


--
-- TOC entry 2291 (class 1259 OID 16752)
-- Name: ix_propriedade_cod_classe; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_propriedade_cod_classe ON public.propriedade USING btree (cod_classe);


--
-- TOC entry 2292 (class 1259 OID 16753)
-- Name: ix_propriedade_cod_referencia_classe; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_propriedade_cod_referencia_classe ON public.propriedade USING btree (cod_referencia_classe);


--
-- TOC entry 2293 (class 1259 OID 16754)
-- Name: ix_propriedade_cod_tipodado; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_propriedade_cod_tipodado ON public.propriedade USING btree (cod_tipodado);


--
-- TOC entry 2294 (class 1259 OID 16755)
-- Name: ix_propriedade_nome; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_propriedade_nome ON public.propriedade USING btree (nome);


--
-- TOC entry 2295 (class 1259 OID 16756)
-- Name: ix_propriedade_posicao; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_propriedade_posicao ON public.propriedade USING btree (posicao);


--
-- TOC entry 2296 (class 1259 OID 16757)
-- Name: ix_propriedade_rotulo; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_propriedade_rotulo ON public.propriedade USING btree (rotulo);


--
-- TOC entry 2301 (class 1259 OID 16758)
-- Name: ix_tag_nome_tag; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tag_nome_tag ON public.tag USING btree (nome_tag);


--
-- TOC entry 2304 (class 1259 OID 16759)
-- Name: ix_tagxobjeto_cod_objeto; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tagxobjeto_cod_objeto ON public.tagxobjeto USING btree (cod_objeto);


--
-- TOC entry 2305 (class 1259 OID 16760)
-- Name: ix_tagxobjeto_cod_tag; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tagxobjeto_cod_tag ON public.tagxobjeto USING btree (cod_tag);


--
-- TOC entry 2308 (class 1259 OID 16761)
-- Name: ix_tbl_blob_arquivo; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_blob_arquivo ON public.tbl_blob USING btree (arquivo);


--
-- TOC entry 2309 (class 1259 OID 16762)
-- Name: ix_tbl_blob_cod_objeto; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_blob_cod_objeto ON public.tbl_blob USING btree (cod_objeto);


--
-- TOC entry 2310 (class 1259 OID 16763)
-- Name: ix_tbl_blob_cod_propriedade; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_blob_cod_propriedade ON public.tbl_blob USING btree (cod_propriedade);


--
-- TOC entry 2313 (class 1259 OID 16764)
-- Name: ix_tbl_boolean_cod_objeto; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_boolean_cod_objeto ON public.tbl_boolean USING btree (cod_objeto);


--
-- TOC entry 2314 (class 1259 OID 16765)
-- Name: ix_tbl_boolean_cod_propriedade; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_boolean_cod_propriedade ON public.tbl_boolean USING btree (cod_propriedade);


--
-- TOC entry 2315 (class 1259 OID 16766)
-- Name: ix_tbl_boolean_valor; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_boolean_valor ON public.tbl_boolean USING btree (valor);


--
-- TOC entry 2318 (class 1259 OID 16767)
-- Name: ix_tbl_date_cod_objeto; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_date_cod_objeto ON public.tbl_date USING btree (cod_objeto);


--
-- TOC entry 2319 (class 1259 OID 16768)
-- Name: ix_tbl_date_cod_propriedade; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_date_cod_propriedade ON public.tbl_date USING btree (cod_propriedade);


--
-- TOC entry 2320 (class 1259 OID 16769)
-- Name: ix_tbl_date_valor; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_date_valor ON public.tbl_date USING btree (valor);


--
-- TOC entry 2323 (class 1259 OID 16770)
-- Name: ix_tbl_float_cod_objeto; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_float_cod_objeto ON public.tbl_float USING btree (cod_objeto);


--
-- TOC entry 2324 (class 1259 OID 16771)
-- Name: ix_tbl_float_cod_propriedade; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_float_cod_propriedade ON public.tbl_float USING btree (cod_propriedade);


--
-- TOC entry 2325 (class 1259 OID 16772)
-- Name: ix_tbl_float_valor; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_float_valor ON public.tbl_float USING btree (valor);


--
-- TOC entry 2328 (class 1259 OID 16773)
-- Name: ix_tbl_integer_cod_objeto; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_integer_cod_objeto ON public.tbl_integer USING btree (cod_objeto);


--
-- TOC entry 2329 (class 1259 OID 16774)
-- Name: ix_tbl_integer_cod_propriedade; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_integer_cod_propriedade ON public.tbl_integer USING btree (cod_propriedade);


--
-- TOC entry 2330 (class 1259 OID 16775)
-- Name: ix_tbl_integer_valor; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_integer_valor ON public.tbl_integer USING btree (valor);


--
-- TOC entry 2333 (class 1259 OID 16776)
-- Name: ix_tbl_objref_cod_objeto; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_objref_cod_objeto ON public.tbl_objref USING btree (cod_objeto);


--
-- TOC entry 2334 (class 1259 OID 16777)
-- Name: ix_tbl_objref_cod_propriedade; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_objref_cod_propriedade ON public.tbl_objref USING btree (cod_propriedade);


--
-- TOC entry 2335 (class 1259 OID 16778)
-- Name: ix_tbl_objref_valor; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_objref_valor ON public.tbl_objref USING btree (valor);


--
-- TOC entry 2338 (class 1259 OID 16779)
-- Name: ix_tbl_string_cod_objeto; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_string_cod_objeto ON public.tbl_string USING btree (cod_objeto);


--
-- TOC entry 2339 (class 1259 OID 16780)
-- Name: ix_tbl_string_cod_propriedade; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_string_cod_propriedade ON public.tbl_string USING btree (cod_propriedade);


--
-- TOC entry 2340 (class 1259 OID 16781)
-- Name: ix_tbl_string_valor; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_string_valor ON public.tbl_string USING btree (valor);


--
-- TOC entry 2343 (class 1259 OID 16782)
-- Name: ix_tbl_text_cod_objeto; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_text_cod_objeto ON public.tbl_text USING btree (cod_objeto);


--
-- TOC entry 2344 (class 1259 OID 16783)
-- Name: ix_tbl_text_cod_propriedade; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tbl_text_cod_propriedade ON public.tbl_text USING btree (cod_propriedade);


--
-- TOC entry 2347 (class 1259 OID 16784)
-- Name: ix_tipodado_tabela; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_tipodado_tabela ON public.tipodado USING btree (tabela);


--
-- TOC entry 2350 (class 1259 OID 16785)
-- Name: ix_usuario_secao; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_usuario_secao ON public.usuario USING btree (secao);


--
-- TOC entry 2353 (class 1259 OID 16786)
-- Name: ix_usuarioxobjetoxperfil_cod_objeto; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_usuarioxobjetoxperfil_cod_objeto ON public.usuarioxobjetoxperfil USING btree (cod_objeto);


--
-- TOC entry 2354 (class 1259 OID 16787)
-- Name: ix_usuarioxobjetoxperfil_cod_perfil; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_usuarioxobjetoxperfil_cod_perfil ON public.usuarioxobjetoxperfil USING btree (cod_perfil);


--
-- TOC entry 2355 (class 1259 OID 16788)
-- Name: ix_usuarioxobjetoxperfil_cod_usuario; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ix_usuarioxobjetoxperfil_cod_usuario ON public.usuarioxobjetoxperfil USING btree (cod_usuario);


--
-- TOC entry 2384 (class 2606 OID 16789)
-- Name: tbl_blob fk_blob_objeto; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_blob
    ADD CONSTRAINT fk_blob_objeto FOREIGN KEY (cod_objeto) REFERENCES public.objeto(cod_objeto) ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2385 (class 2606 OID 16794)
-- Name: tbl_blob fk_blob_propriedade; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_blob
    ADD CONSTRAINT fk_blob_propriedade FOREIGN KEY (cod_propriedade) REFERENCES public.propriedade(cod_propriedade) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2386 (class 2606 OID 16799)
-- Name: tbl_boolean fk_booblean_propriedade; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_boolean
    ADD CONSTRAINT fk_booblean_propriedade FOREIGN KEY (cod_propriedade) REFERENCES public.propriedade(cod_propriedade) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2387 (class 2606 OID 16804)
-- Name: tbl_boolean fk_boolean_objeto; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_boolean
    ADD CONSTRAINT fk_boolean_objeto FOREIGN KEY (cod_objeto) REFERENCES public.objeto(cod_objeto) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2361 (class 2606 OID 16809)
-- Name: classexfilhos fk_classexfilhos_classe; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.classexfilhos
    ADD CONSTRAINT fk_classexfilhos_classe FOREIGN KEY (cod_classe) REFERENCES public.classe(cod_classe) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2362 (class 2606 OID 16814)
-- Name: classexfilhos fk_classexfilhos_classe_filho; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.classexfilhos
    ADD CONSTRAINT fk_classexfilhos_classe_filho FOREIGN KEY (cod_classe_filho) REFERENCES public.classe(cod_classe) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2363 (class 2606 OID 16819)
-- Name: classexobjeto fk_classexobjeto_classe; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.classexobjeto
    ADD CONSTRAINT fk_classexobjeto_classe FOREIGN KEY (cod_classe) REFERENCES public.classe(cod_classe) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2364 (class 2606 OID 16824)
-- Name: classexobjeto fk_classexobjeto_objeto; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.classexobjeto
    ADD CONSTRAINT fk_classexobjeto_objeto FOREIGN KEY (cod_objeto) REFERENCES public.objeto(cod_objeto) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2388 (class 2606 OID 16834)
-- Name: tbl_date fk_date_objeto; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_date
    ADD CONSTRAINT fk_date_objeto FOREIGN KEY (cod_objeto) REFERENCES public.objeto(cod_objeto) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2389 (class 2606 OID 16839)
-- Name: tbl_date fk_date_propriedade; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_date
    ADD CONSTRAINT fk_date_propriedade FOREIGN KEY (cod_propriedade) REFERENCES public.propriedade(cod_propriedade) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2390 (class 2606 OID 16844)
-- Name: tbl_float fk_float_objeto; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_float
    ADD CONSTRAINT fk_float_objeto FOREIGN KEY (cod_objeto) REFERENCES public.objeto(cod_objeto) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2391 (class 2606 OID 16849)
-- Name: tbl_float fk_float_propriedade; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_float
    ADD CONSTRAINT fk_float_propriedade FOREIGN KEY (cod_propriedade) REFERENCES public.propriedade(cod_propriedade) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2365 (class 2606 OID 16854)
-- Name: infoperfil fk_infoperfil_perfil; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.infoperfil
    ADD CONSTRAINT fk_infoperfil_perfil FOREIGN KEY (cod_perfil) REFERENCES public.perfil(cod_perfil);


--
-- TOC entry 2392 (class 2606 OID 16859)
-- Name: tbl_integer fk_integer_objeto; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_integer
    ADD CONSTRAINT fk_integer_objeto FOREIGN KEY (cod_objeto) REFERENCES public.objeto(cod_objeto) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2393 (class 2606 OID 16864)
-- Name: tbl_integer fk_integer_propriedade; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_integer
    ADD CONSTRAINT fk_integer_propriedade FOREIGN KEY (cod_propriedade) REFERENCES public.propriedade(cod_propriedade) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2366 (class 2606 OID 16869)
-- Name: logobjeto fk_logobjeto_objeto; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.logobjeto
    ADD CONSTRAINT fk_logobjeto_objeto FOREIGN KEY (cod_objeto) REFERENCES public.objeto(cod_objeto) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2367 (class 2606 OID 16874)
-- Name: logobjeto fk_logobjeto_usuario; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.logobjeto
    ADD CONSTRAINT fk_logobjeto_usuario FOREIGN KEY (cod_usuario) REFERENCES public.usuario(cod_usuario) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2368 (class 2606 OID 16879)
-- Name: logworkflow fk_logworkflow_objeto; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.logworkflow
    ADD CONSTRAINT fk_logworkflow_objeto FOREIGN KEY (cod_objeto) REFERENCES public.objeto(cod_objeto) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2369 (class 2606 OID 16884)
-- Name: logworkflow fk_logworkflow_usuario; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.logworkflow
    ADD CONSTRAINT fk_logworkflow_usuario FOREIGN KEY (cod_usuario) REFERENCES public.usuario(cod_usuario) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2370 (class 2606 OID 16889)
-- Name: objeto fk_objeto_classe; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.objeto
    ADD CONSTRAINT fk_objeto_classe FOREIGN KEY (cod_classe) REFERENCES public.classe(cod_classe) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2371 (class 2606 OID 16894)
-- Name: objeto fk_objeto_pele; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.objeto
    ADD CONSTRAINT fk_objeto_pele FOREIGN KEY (cod_pele) REFERENCES public.pele(cod_pele) ON UPDATE CASCADE ON DELETE SET NULL NOT VALID;


--
-- TOC entry 2372 (class 2606 OID 16899)
-- Name: objeto fk_objeto_status; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.objeto
    ADD CONSTRAINT fk_objeto_status FOREIGN KEY (cod_status) REFERENCES public.status(cod_status) ON UPDATE CASCADE ON DELETE SET DEFAULT NOT VALID;


--
-- TOC entry 2373 (class 2606 OID 16904)
-- Name: objeto fk_objeto_usuario; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.objeto
    ADD CONSTRAINT fk_objeto_usuario FOREIGN KEY (cod_usuario) REFERENCES public.usuario(cod_usuario) ON UPDATE CASCADE ON DELETE SET DEFAULT NOT VALID;


--
-- TOC entry 2394 (class 2606 OID 16909)
-- Name: tbl_objref fk_objref_objeto; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_objref
    ADD CONSTRAINT fk_objref_objeto FOREIGN KEY (cod_objeto) REFERENCES public.objeto(cod_objeto) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2395 (class 2606 OID 16914)
-- Name: tbl_objref fk_objref_propriedade; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_objref
    ADD CONSTRAINT fk_objref_propriedade FOREIGN KEY (cod_propriedade) REFERENCES public.propriedade(cod_propriedade) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2374 (class 2606 OID 16919)
-- Name: parentesco fk_parentesco_objeto; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.parentesco
    ADD CONSTRAINT fk_parentesco_objeto FOREIGN KEY (cod_objeto) REFERENCES public.objeto(cod_objeto) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2375 (class 2606 OID 16924)
-- Name: parentesco fk_parentesco_objeto_pai; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.parentesco
    ADD CONSTRAINT fk_parentesco_objeto_pai FOREIGN KEY (cod_pai) REFERENCES public.objeto(cod_objeto) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2376 (class 2606 OID 16929)
-- Name: pendencia fk_pendencia_objeto; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pendencia
    ADD CONSTRAINT fk_pendencia_objeto FOREIGN KEY (cod_objeto) REFERENCES public.objeto(cod_objeto) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2377 (class 2606 OID 16934)
-- Name: pendencia fk_pendencia_usuario; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pendencia
    ADD CONSTRAINT fk_pendencia_usuario FOREIGN KEY (cod_usuario) REFERENCES public.usuario(cod_usuario) ON UPDATE CASCADE ON DELETE SET DEFAULT NOT VALID;


--
-- TOC entry 2378 (class 2606 OID 16939)
-- Name: pilha fk_pilha_objeto; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pilha
    ADD CONSTRAINT fk_pilha_objeto FOREIGN KEY (cod_objeto) REFERENCES public.objeto(cod_objeto) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2379 (class 2606 OID 16944)
-- Name: pilha fk_pilha_usuario; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pilha
    ADD CONSTRAINT fk_pilha_usuario FOREIGN KEY (cod_usuario) REFERENCES public.usuario(cod_usuario) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2380 (class 2606 OID 16949)
-- Name: propriedade fk_propriedade_classe; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.propriedade
    ADD CONSTRAINT fk_propriedade_classe FOREIGN KEY (cod_classe) REFERENCES public.classe(cod_classe) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2381 (class 2606 OID 16954)
-- Name: propriedade fk_propriedade_tipodado; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.propriedade
    ADD CONSTRAINT fk_propriedade_tipodado FOREIGN KEY (cod_tipodado) REFERENCES public.tipodado(cod_tipodado);


--
-- TOC entry 2396 (class 2606 OID 16959)
-- Name: tbl_string fk_string_objeto; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_string
    ADD CONSTRAINT fk_string_objeto FOREIGN KEY (cod_objeto) REFERENCES public.objeto(cod_objeto) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2397 (class 2606 OID 16964)
-- Name: tbl_string fk_string_propriedade; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_string
    ADD CONSTRAINT fk_string_propriedade FOREIGN KEY (cod_propriedade) REFERENCES public.propriedade(cod_propriedade) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2382 (class 2606 OID 16969)
-- Name: tagxobjeto fk_tagxobjeto_objeto; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tagxobjeto
    ADD CONSTRAINT fk_tagxobjeto_objeto FOREIGN KEY (cod_objeto) REFERENCES public.objeto(cod_objeto) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2383 (class 2606 OID 16974)
-- Name: tagxobjeto fk_tagxobjeto_tag; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tagxobjeto
    ADD CONSTRAINT fk_tagxobjeto_tag FOREIGN KEY (cod_tag) REFERENCES public.tag(cod_tag) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2398 (class 2606 OID 16979)
-- Name: tbl_text fk_text_objeto; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_text
    ADD CONSTRAINT fk_text_objeto FOREIGN KEY (cod_objeto) REFERENCES public.objeto(cod_objeto) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2399 (class 2606 OID 16984)
-- Name: tbl_text fk_text_propriedade; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tbl_text
    ADD CONSTRAINT fk_text_propriedade FOREIGN KEY (cod_propriedade) REFERENCES public.propriedade(cod_propriedade) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2400 (class 2606 OID 16989)
-- Name: usuarioxobjetoxperfil fk_usuarioxobjetoxperfil_objeto; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuarioxobjetoxperfil
    ADD CONSTRAINT fk_usuarioxobjetoxperfil_objeto FOREIGN KEY (cod_objeto) REFERENCES public.objeto(cod_objeto) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2401 (class 2606 OID 16994)
-- Name: usuarioxobjetoxperfil fk_usuarioxobjetoxperfil_perfil; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuarioxobjetoxperfil
    ADD CONSTRAINT fk_usuarioxobjetoxperfil_perfil FOREIGN KEY (cod_perfil) REFERENCES public.perfil(cod_perfil) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2402 (class 2606 OID 16999)
-- Name: usuarioxobjetoxperfil fk_usuarioxobjetoxperfil_usuario; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuarioxobjetoxperfil
    ADD CONSTRAINT fk_usuarioxobjetoxperfil_usuario FOREIGN KEY (cod_usuario) REFERENCES public.usuario(cod_usuario) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 2403 (class 2606 OID 17004)
-- Name: versaoobjeto fk_versaoobjeto_objeto; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.versaoobjeto
    ADD CONSTRAINT fk_versaoobjeto_objeto FOREIGN KEY (cod_objeto) REFERENCES public.objeto(cod_objeto) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


-- Completed on 2019-11-23 20:30:22 UTC

--
-- PostgreSQL database dump complete
--

