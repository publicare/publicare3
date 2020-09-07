<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @file Email.php
 * @description Classe responsável por gerenciar envio de emails pela aplicaçao
 * @copyright MIT © 2020
 * @package publicare
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

namespace Pbl\Core;

use Pbl\Core\Base;

class Email extends Base
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