<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @file 
 * @description 
 * @copyright MIT © 2020
 * @package Pbl/Core/Config
 *
 * Este arquivo é parte do programa Publicare
 * 
 * Copyright (c) 2020 Publicare
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
*/

namespace Pbl\Core\Config;

use Pbl\Core\Base;

/**
 * Classe para abstração de dados
 * Esta classe utiliza o ADODB.sf.net
 */
class Padrao extends Base
{
    private $dados = array(
        "bd" => array(
            "tabelas" => array(
                "classe" => array("nome" => "classe", "nick" => "t1", "colunas" => array("cod_classe" => "cod_classe", "nome" => "nome", "prefixo" => "prefixo", "descricao" => "descricao", "temfilhos" => "temfilhos", "sistema" => "sistema", "indexar" => "indexar")),
                "classexfilhos" => array("nome" => "classexfilhos", "nick" => "t2", "colunas" => array("cod_classe" => "cod_classe", "cod_classe_filho" => "cod_classe_filho")),
                "classexobjeto" => array("nome" => "classexobjeto", "nick" => "t3", "colunas" => array("cod_classe" => "cod_classe", "cod_objeto" => "cod_objeto")),
                "infoperfil" => array("nome" => "infoperfil", "nick" => "t4", "colunas" => array("cod_infoperfil" => "cod_infoperfil", "cod_perfil" => "cod_perfil", "acao" => "acao", "script" => "script", "donooupublicado" => "donooupublicado", "sopublicado" => "sopublicado", "sodono" => "sodono", "naomenu" => "naomenu", "ordem" => "ordem", "icone" => "icone")),
                "logobjeto" => array("nome" => "logobjeto", "nick" => "t5", "colunas" => array("cod_logobjeto" => "cod_logobjeto", "cod_objeto" => "cod_objeto", "estampa" => "estampa", "cod_usuario" => "cod_usuario", "cod_operacao" => "cod_operacao")),
                "logworkflow" => array("nome" => "logworkflow", "nick" => "t6", "colunas" => array("cod_logworkflow" => "cod_logworkflow", "cod_objeto" => "cod_objeto", "cod_usuario" => "cod_usuario", "mensagem" => "mensagem", "cod_status" => "cod_status", "estampa" => "estampa")),
                "objeto" => array("nome" => "objeto", "nick" => "t7", "colunas" => array("cod_objeto" => "cod_objeto", "cod_pai" => "cod_pai", "cod_classe" => "cod_classe", "cod_usuario" => "cod_usuario", "cod_pele" => "cod_pele", "cod_status" => "cod_status", "titulo" => "titulo", "descricao" => "descricao", "data_publicacao" => "data_publicacao", "data_validade" => "data_validade", "script_exibir" => "script_exibir", "apagado" => "apagado", "objetosistema" => "objetosistema", "peso" => "peso", "data_exclusao" => "data_exclusao", "url_amigavel" => "url_amigavel","versao" => "versao", "versao_publicada" => "versao_publicada")),
                "parentesco" => array("nome" => "parentesco", "nick" => "t8", "colunas" => array("cod_objeto" => "cod_objeto", "cod_pai" => "cod_pai", "ordem" => "ordem")),
                "pele" => array("nome" => "pele", "nick" => "t9", "colunas" => array("cod_pele" => "cod_pele", "nome" => "nome", "prefixo" => "prefixo", "publica" => "publica")), 
                "pendencia" => array("nome" => "pendencia", "nick" => "t10", "colunas" => array("cod_pendencia" => "cod_pendencia", "cod_usuario" => "cod_usuario", "cod_objeto" => "cod_objeto")), 
                "perfil" => array("nome" => "perfil", "nick" => "t11", "colunas" => array("cod_perfil" => "cod_perfil", "nome" => "nome", "cod_perfil_pai" => "cod_perfil_pai")),
                "pilha" => array("nome" => "pilha", "nick" => "t12", "colunas" => array("cod_pilha" => "cod_pilha", "cod_objeto" => "cod_objeto", "cod_usuario" => "cod_usuario", "cod_tipo" => "cod_tipo", "datahora" => "datahora")),
                "propriedade" => array("nome" => "propriedade", "nick" => "t13", "colunas" => array("cod_propriedade" => "cod_propriedade", "cod_classe" => "cod_classe", "cod_tipodado" => "cod_tipodado", "cod_referencia_classe" => "cod_referencia_classe", "campo_ref" => "campo_ref", "nome" => "nome", "posicao" => "posicao", "descricao" => "descricao", "rotulo" => "rotulo", "rot1booleano" => "rot1booleano", "rot2booleano" => "rot2booleano", "obrigatorio" => "obrigatorio", "seguranca" => "seguranca", "valorpadrao" => "valorpadrao")),
                "status" => array("nome" => "status", "nick" => "t14", "colunas" => array("cod_status" => "cod_status", "nome" => "nome")), 
                "tag" => array("nome" => "tag", "nick" => "t15", "colunas" => array("cod_tag" => "cod_tag", "nome_tag" => "nome_tag")),
                "tagxobjeto" => array("nome" => "tagxobjeto", "nick" => "t16", "colunas" => array("cod_tagxobjeto" => "cod_tagxobjeto", "cod_tag" => "cod_tag", "cod_objeto" => "cod_objeto")),
                "tbl_blob" => array("nome" => "tbl_blob", "nick" => "t17", "colunas" => array("cod_blob" => "cod_blob", "cod_objeto" => "cod_objeto", "cod_propriedade" => "cod_propriedade", "arquivo" => "arquivo", "tamanho" => "tamanho")), 
                "tbl_boolean" => array("nome" => "tbl_boolean", "nick" => "t18", "colunas" => array("cod_boolean" => "cod_boolean", "cod_objeto" => "cod_objeto", "cod_propriedade" => "cod_propriedade", "valor" => "valor")), 
                "tbl_date" => array("nome" => "tbl_date", "nick" => "t19", "colunas" => array("cod_date" => "cod_date", "cod_objeto" => "cod_objeto", "cod_propriedade" => "cod_propriedade", "valor" => "valor")), 
                "tbl_float" => array("nome" => "tbl_float", "nick" => "t20", "colunas" => array("cod_float" => "cod_float", "cod_objeto" => "cod_objeto", "cod_propriedade" => "cod_propriedade", "valor" => "valor")), 
                "tbl_integer" => array("nome" => "tbl_integer", "nick" => "t21", "colunas" => array("cod_integer" => "cod_integer", "cod_objeto" => "cod_objeto", "cod_propriedade" => "cod_propriedade", "valor" => "valor")),
                "tbl_objref" => array("nome" => "tbl_objref", "nick" => "t22", "colunas" => array("cod_objref" => "cod_objref", "cod_objeto" => "cod_objeto", "cod_propriedade" => "cod_propriedade", "valor" => "valor")), 
                "tbl_string" => array("nome" => "tbl_string", "nick" => "t23", "colunas" => array("cod_string" => "cod_string", "cod_objeto" => "cod_objeto", "cod_propriedade" => "cod_propriedade", "valor" => "valor")), 
                "tbl_text" => array("nome" => "tbl_text", "nick" => "t24", "colunas" => array("cod_text" => "cod_text", "cod_objeto" => "cod_objeto", "cod_propriedade" => "cod_propriedade", "valor" => "valor")), 
                "tipodado" => array("nome" => "tipodado", "nick" => "t25", "colunas" => array("cod_tipodado" => "cod_tipodado", "nome" => "nome", "tabela" => "tabela", "delimitador" => "delimitador")), 
                "usuario" => array("nome" => "usuario", "nick" => "t26", "colunas" => array("cod_usuario" => "cod_usuario", "secao" => "secao", "nome" => "nome", "login" => "login", "email" => "email", "ramal" => "ramal", "senha" => "senha", "chefia" => "chefia", "valido" => "valido", "data_atualizacao" => "data_atualizacao", "altera_senha" => "altera_senha", "ldap" => "ldap")), 
                "usuarioxobjetoxperfil" => array("nome" => "usuarioxobjetoxperfil", "nick" => "t27", "colunas" => array("cod_usuario" => "cod_usuario", "cod_objeto" => "cod_objeto", "cod_perfil" => "cod_perfil")),
                "versaoobjeto" => array("nome" => "versaoobjeto", "nick" => "t28", "colunas" => array("cod_versaoobjeto" => "cod_versaoobjeto", "cod_objeto" => "cod_objeto", "versao" => "versao", "conteudo" => "conteudo", "data_criacao" => "data_criacao", "cod_usuario" => "cod_usuario", "ip" => "ip"))
            )
        )
    );

    public function getDados()
    {
        return $this->dados;
    }

} 

