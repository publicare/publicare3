<?php
/**
* Publicare - O CMS Público Brasileiro
* @description funcoes.php é responsável por manter funcionalidades básicas da aplicação
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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

if (!function_exists("ofuscaEmail"))
{
    function ofuscaEmail($email)
    {
        $vemail = preg_split("[@]", $email);
        $tempmail = "";
        $tamanho = strlen($vemail[0]);
        $partes = floor($tamanho/3);
        for ($i=0; $i<strlen($vemail[0]); $i++)
        {
            if ($i<$partes || $i>=$tamanho-$partes)
            {
                $tempmail .= substr($vemail[0], $i, 1);
            }
            else
            {
                $tempmail .= "*";
            }
        }
        $tempmail .= "@".$vemail[1];
        return $tempmail;
    }
}

function ignoreErrorHandler()
{
	return true;
}
 


if (!function_exists("identificaCodigoObjeto"))
{
/**
 * Recebe string e verifica se tem codigo de objeto no meio
 * @param string $str
 * @return int
 */
    function identificaCodigoObjeto($str, $cod_root)
    {
        $cod_objeto = $cod_root;
        
        // url publicare com codigo e titulo
        if (is_numeric($str))
        {
            $cod_objeto = (int)$str;
        }
        else
        {
            $temp = preg_split("[\.]", $str);
            $cod_objeto = (int)$str;
        }

        // caso não identifique cod_objeto, atribui valor de root
        if ($cod_objeto==0) $cod_objeto = $cod_root;
        
        return $cod_objeto;
    }
}

if (!function_exists("cortaTexto"))
{
/**
 * Retorna texto cortado. Não corta as palavras ao meio.
 *
 * @param string $txt - Texto
 * @param integer $tam - Tamanho a ser mantido
 * @return unknown
 */
    function cortaTexto($txt, $tam){
        $vtxt = explode(" ", $txt);
        $tam_temp = 0;
        $txt_temp = "";
        for ($cont=0; $cont<sizeof($vtxt); $cont++)
        {
            $tam_temp += strlen($vtxt[$cont]);
            if ($tam_temp < $tam)
            {
                $txt_temp .= " ".$vtxt[$cont];
            }
            else
            {
                $txt_temp .= "...";
                break;
            }
        }
        return $txt_temp;
    }
}

if (!function_exists("array_push_associative"))
{
/**
 * 
 * @param type $arr
 */
    function array_push_associative(&$arr)
    {
    $args = func_get_args();
    foreach ($args as $arg) 
        {
            if (is_array($arg)) 
            {
                foreach ($arg as $key => $value) 
                {
                $arr[$key] = $value;
                //$ret++;
                }
            }
            else
            {
                $arr[$arg] = "";
            }
    }
    //return $ret;
    }
}

if (!function_exists("EnviaEmail"))
{
/**
 * Envia email utilizando a classe phpmailer
 * @param type $remetente_nome
 * @param type $remetente_email
 * @param type $destinatario_nome
 * @param type $destinatario_email
 * @param type $assunto
 * @param type $texConteudo
 * @param type $altConteudo
 * @param type $arrArquivoAnexado
 * @return boolean
 */
    function EnviaEmail(&$page, $destinatario_nome, $destinatario_email, $remetente_nome=-1, $remetente_email=-1, $assunto="", $texConteudo="", $altConteudo="", $arrArquivoAnexado=array())
    {

        $mail = new PHPMailer($page->config["email"]["debug"]);
    //    $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';
        if ($page->config["email"]["debug"] === true)
        {
            // Ativa verbose debug
            $mail->SMTPDebug = $page->config["email"]["debugnivel"];
        }
        try {
            
            // Envio via smtp
            if (isset($page->config["email"]["smtp"]) && $page->config["email"]["smtp"]===true)
            {
                $mail->isSMTP();
                $mail->Host = $page->config["email"]["host"];
                $mail->Port = $page->config["email"]["porta"];
                
                if (isset($page->config["email"]["auth"]) && $page->config["email"]["auth"] === true)
                {
                    $mail->SMTPAuth = true;
                    $mail->Username = $page->config["email"]["usuario"];
                    $mail->Password = $page->config["email"]["senha"];
                    
                    
                    
                    if (isset($page->config["email"]["enc"]) && ($page->config["email"]["enc"] === 'tls' ||  $page->config["email"]["enc"] === 'ssl'))
                    {
                        $mail->SMTPOptions = array(
                            'ssl' => array(
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            )
                        );
                        
                        if ($page->config["email"]["enc"] === 'tls')
                        {
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        }
                        if ($page->config["email"]["enc"] === 'ssl')
                        {
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                        }
                    }
                }
            }
                
            $mail->setFrom(($remetente_email!=-1?$remetente_email:$page->config["email"]["from"]), ($remetente_nome!=-1?$remetente_nome:$page->config["email"]["fromnome"]));
            $mail->addReplyTo(($remetente_email!=-1?$remetente_email:$page->config["email"]["from"]), ($remetente_nome!=-1?$remetente_nome:$page->config["email"]["fromnome"]));
            $mail->addAddress($destinatario_email, $destinatario_nome);
    //        $mail->addAddress('ellen@example.com');
    //        $mail->addReplyTo('info@example.com', 'Information');
    //        $mail->addCC('cc@example.com');
    //        $mail->addBCC('bcc@example.com');

            // Attachments
    //        $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    //        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            // Conteudo
            $mail->isHTML(true);
            $mail->Subject = $assunto;
            $mail->Body = $texConteudo;
            if ($altConteudo!=="")
            {
                $mail->AltBody = $altConteudo;
            }
            else
            {
                $mail->AltBody = strip_tags(br2nl2($texConteudo));
            }

            $mail->send();
            return(array("status"=>true, "mensagem"=>"Enviado com sucesso"));
        } catch (Exception $e) {
            return(array("status"=>false, "mensagem"=>"Erro ao enviar: ".$mail->ErrorInfo));
        }
        
        exit();
    }
}

if (!function_exists("br2nl2"))
{
	function br2nl2($string)
    {
        return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
    }
}
 
if (!function_exists("xd"))
{
	/*FUNÇÃO ÚTIL PARA DEBUG*/
	function xd($obj)
	{
		echo "<div style='background-color:#DFDFDF; border:1px #666666 solid'>";
			echo "<pre>";
				var_dump($obj);
			echo "</pre>";
		echo "</div>";
		die();
	}
}
 
if (!function_exists("x"))
{
	/*FUNÇÃO ÚTIL PARA DEBUG SEM  DIE*/
	function x($obj)
	{
		echo "<div style='background-color:#DFDFDF; border:1px #666666 solid'>";
			echo "<pre>";
				var_dump($obj);
			echo "</pre>";
		echo "</div>";
	}
}	

if (!function_exists("limpaString"))
{
	/**
    * Retira acentos, espaços e caracteres especiais da string
    * @param  string $str - string que ira ser tratada
    * @return string
    */
    function limpaString($str, $caracterTraco="-")
	{
		
		$acentos = array(
					'A' => '/&Agrave;|&Aacute;|&Acirc;|&Atilde;|&Auml;|&Aring;/',
					'a' => '/&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;/',
					'C' => '/&Ccedil;/',
					'c' => '/&ccedil;/',
					'E' => '/&Egrave;|&Eacute;|&Ecirc;|&Euml;/',
					'e' => '/&egrave;|&eacute;|&ecirc;|&euml;/',
					'I' => '/&Igrave;|&Iacute;|&Icirc;|&Iuml;/',
					'i' => '/&igrave;|&iacute;|&icirc;|&iuml;/',
					'N' => '/&Ntilde;/',
					'n' => '/&ntilde;/',
					'O' => '/&Ograve;|&Oacute;|&Ocirc;|&Otilde;|&Ouml;/',
					'o' => '/&ograve;|&oacute;|&ocirc;|&otilde;|&ouml;/',
					'U' => '/&Ugrave;|&Uacute;|&Ucirc;|&Uuml;/',
					'u' => '/&ugrave;|&uacute;|&ucirc;|&uuml;/',
					'Y' => '/&Yacute;/',
					'y' => '/&yacute;|&yuml;/',
					$caracterTraco => '/ |&amp;|&uml;|&ordf;|&ordm;|&deg;|&ndash;|&mdash;|&gt;|&lt;|&nbsp;|&sup1;|&sup2;|&sup3;|&quot;|\/|\–|_/',
					'' => '/\.|,|\$|\?|\"|\'|\*|\:|\!|\“|\”|\(|\)|\||\+|\¹|\?|&ldquo;|&rdquo;/');


		$palavra =  preg_replace($acentos, array_keys($acentos), htmlentities($str, ENT_QUOTES, "UTF-8"));
		$palavra = str_replace("--", "-", $palavra);
		$palavra = str_replace("--", "-", $palavra);
		$palavra = str_replace("--", "-", $palavra);
	
		return $palavra;

	}
}

if (!function_exists("limpaStringEspaco"))
{
	function limpaStringEspaco($str)
	{
		
		$acentos = array(
					'A' => '/&Agrave;|&Aacute;|&Acirc;|&Atilde;|&Auml;|&Aring;/',
					'a' => '/&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;/',
					'C' => '/&Ccedil;/',
					'c' => '/&ccedil;/',
					'E' => '/&Egrave;|&Eacute;|&Ecirc;|&Euml;/',
					'e' => '/&egrave;|&eacute;|&ecirc;|&euml;/',
					'I' => '/&Igrave;|&Iacute;|&Icirc;|&Iuml;/',
					'i' => '/&igrave;|&iacute;|&icirc;|&iuml;/',
					'N' => '/&Ntilde;/',
					'n' => '/&ntilde;/',
					'O' => '/&Ograve;|&Oacute;|&Ocirc;|&Otilde;|&Ouml;/',
					'o' => '/&ograve;|&oacute;|&ocirc;|&otilde;|&ouml;/',
					'U' => '/&Ugrave;|&Uacute;|&Ucirc;|&Uuml;/',
					'u' => '/&ugrave;|&uacute;|&ucirc;|&uuml;/',
					'Y' => '/&Yacute;/',
					'y' => '/&yacute;|&yuml;/');

		$palavra =  preg_replace($acentos, array_keys($acentos), htmlentities($str, ENT_QUOTES, "UTF-8"));
		$palavra = str_replace("--", "-", $palavra);
		$palavra = str_replace("--", "-", $palavra);
		$palavra = str_replace("--", "-", $palavra);
	
		return $palavra;

	}
}       
        /**
         * Gera log no log de erros
         * @param type $string
         */
        function logPbl($string, $nivel=1)
{
    error_log(udate('Y-m-d H:i:s.u')." - ".$string);
}

if (!function_exists("udate"))
{
/**
 * Datetime com milisegundos
 * @param type $format
 * @param type $utimestamp
 * @return type
 */
    function udate($format = 'u', $utimestamp = null) {
        if (is_null($utimestamp))
            $utimestamp = microtime(true);

        $timestamp = floor($utimestamp);
        $milliseconds = round(($utimestamp - $timestamp) * 1000000);

        return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
    }
}

if (!function_exists("gerar_senha"))
{
/**
 * Gera string aleatória, podendo misturar caracteres maiusculos, minusculos, numeros e simbolos
 * @param int $tamanho - Tamanho da string
 * @param bool $maiusculas - Adicionar letras maiusculas
 * @param bool $minusculas - Adicionar letras minusculas
 * @param bool $numeros - Adicionar números
 * @param bool $simbolos - adicionar simbolos
 * @return string
 */
    function gerar_senha($tamanho, $maiusculas=true, $minusculas=true, $numeros=true, $simbolos=true){
        $ma = "ABCDEFGHIJKLMNOPQRSTUVYXWZ"; // $ma contem as letras maiúsculas
        $mi = "abcdefghijklmnopqrstuvyxwz"; // $mi contem as letras minusculas
        $nu = "0123456789"; // $nu contem os números
        $si = "!@?-=%#$"; // $si contem os símbolos
        $senha = "";
    
        if ($maiusculas){
            // se $maiusculas for "true", a variável $ma é embaralhada e adicionada para a variável $senha
            $senha .= str_shuffle($ma);
        }
    
        if ($minusculas){
            // se $minusculas for "true", a variável $mi é embaralhada e adicionada para a variável $senha
            $senha .= str_shuffle($mi);
        }
    
        if ($numeros){
            // se $numeros for "true", a variável $nu é embaralhada e adicionada para a variável $senha
            $senha .= str_shuffle($nu);
        }
    
        if ($simbolos){
            // se $simbolos for "true", a variável $si é embaralhada e adicionada para a variável $senha
            $senha .= str_shuffle($si);
        }
    
        // retorna a senha embaralhada com "str_shuffle" com o tamanho definido pela variável $tamanho
        return substr(str_shuffle($senha), 0, $tamanho);
    }
}