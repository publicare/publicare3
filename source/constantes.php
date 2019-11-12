<?php
/**
* Publicare - O CMS Público Brasileiro
* @description constantes.php - Contém definições de constantes da aplicação
* @copyright GPL © 2007
* @package publicare
*
* MCTI - Ministério da Ciência, Tecnologia e Inovação - www.mcti.gov.br
* ANTT - Agência Nacional de Transportes Terrestres - www.antt.gov.br
* EPL - Empresa de Planejamento e Logística - www.epl.gov.br
* LogicBSB - LogicBSB Sistemas Inteligentes - www.logicbsb.com.br
*
* Este arquivo é parte do programa Publicare
* Publicare é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU 
* como publicada pela Fundação do Software Livre (FSF); na versão 3 da Licença, ou (na sua opinião) qualquer versão.
* Este programa é distribuído na esperança de que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita 
* de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU para maiores detalhes.
* Você deve ter recebido uma cópia da Licença Pública Geral GNU junto com este programa, se não, veja <http://www.gnu.org/licenses/>.
*/

/**
 * Constantes para funcionalidades de envio de emails
 */
define("EmailTextCharset", "utf-8");
define("EmailHtmlCharset", "us-ascii");
define("EmailNewLine", "\r\n");

/**
 * Constantes para operações com objetos
 */
define('_OPERACAO_OBJETO_RECUPERAR', 4);
define('_OPERACAO_OBJETO_REMOVER', 3);
define('_OPERACAO_OBJETO_EDITAR', 2);
define('_OPERACAO_OBJETO_CRIAR', 1);

/**
 * Constantes status dos objetos
 */
define('_STATUS_PRIVADO', 1);
define('_STATUS_PUBLICADO', 2);
define('_STATUS_REJEITADO', 3); 
define('_STATUS_SUBMETIDO', 4);

/**
 * Constantes perfis de usuários
 */
define('_PERFIL_ADMINISTRADOR', 1);
define('_PERFIL_EDITOR', 2);
define('_PERFIL_AUTOR', 3);
define('_PERFIL_RESTRITO', 4);
define('_PERFIL_MILITARIZADO', 5);
define('_PERFIL_DEFAULT', 6);

/**
 * Versao
 */
define('_VERSIONPROG','Publicare 3.1.1 - 05/11/2019');