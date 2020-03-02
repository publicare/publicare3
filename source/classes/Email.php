<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo
 * @copyright GPL © 2007
 * @package publicare
 *
 * Este arquivo é parte do programa Publicare
 * Publicare é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU 
 * como publicada pela Fundação do Software Livre (FSF); na versão 3 da Licença, ou (na sua opinião) qualquer versão.
 * Este programa é distribuído na esperança de que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita 
 * de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU junto com este programa, se não, veja <http://www.gnu.org/licenses/>.
*/
class Email
{

	public $_remetente = "";
	public $_destinatario = "";
	public $_assunto = "";
	public $_corpo = "";
	public $_headers = "";
        public $_anexos = array();
	
	function __construct($rem="", $des="", $ass="", $cor="")
	{
            $this->_remetente = $rem;
            $this->_destinatario = $des;
            $this->_assunto = $ass;
            $this->_corpo = $cor;
            
            if (defined("_mailsmtp") && _mailsmtp === true)
            {
                
            }
            else
            {
                $this->montaHeaders();
            }
	}
	
	function montaHeaders()
	{
            $this->_headers = "MIME-Version: 1.0".EmailNewLine; 
            $this->_headers .= "Content-type: text/html; charset=utf-8".EmailNewLine; 
            $this->_headers .= "From: $this->_remetente".EmailNewLine; 
            $this->_headers .= "Return-Path: $this->_remetente".EmailNewLine;
	}
	
	function Send()
	{
            return $this->envia();
	}
	
	function envia()
	{
            if (mail($this->_destinatario, $this->_assunto, $this->_corpo, $this->_headers))
                return true;
            else
                return false;
	}

}
?>