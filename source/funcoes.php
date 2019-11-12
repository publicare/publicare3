<?php
/**
* Publicare - O CMS Público Brasileiro
* @description funcoes.php é responsável por manter funcionalidades básicas da aplicação
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
 * Função para realizar autoload das classes do Publicare
 */
spl_autoload_register(function ($class_name) {
    include "classes/" . $class_name . '.php';
});

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
function EnviarEmail($remetente_nome, $remetente_email, $destinatario_nome, $destinatario_email, $assunto=-1, $texConteudo=-1, $altConteudo="", $arrArquivoAnexado=array())
{
        include_once("lib/phpmailer/class.phpmailer.php");

        // para views antes da versao 2.8.9
        $flag = 0;
        if ($assunto==-1 && $texConteudo==-1)
        {
                $tempRem = $remetente_nome;
                $tempDes = $remetente_email;
                $tempAss = $destinatario_nome;
                $tempMsg = $destinatario_email;

                // arrumando campo remetente
                if (strpos($remetente_nome, "<")!==false)
                {
                        $remetente_nome = substr($tempRem, 0, strpos($tempRem, "<"));
                        $remetente_email = substr($tempRem, strpos($tempRem, "<")+1, strpos($tempRem, ">")-strpos($tempRem, "<")-1);
                }
                else
                {
                        $remetente_nome = $remetente_email = $tempRem;
                }

                // arrumando campo destinatario
                $arDes = preg_split("[,|;]", $tempDes);

                $destin = array();
                for ($i=0; $i<count($arDes); $i++)
                {
                        $temp = $arDes[$i];
                        $destin[] = array("nome"=>"", "email"=>$temp);
                }

                $assunto = $tempAss;
                $texConteudo = $tempMsg;

                $flag = 1;
        }
        else
        {
                $destin = array(array("nome"=>$destinatario_nome, "email"=>$destinatario_email));
        }

        $mail = new PHPMailer(true);
        $mail->Charset = 'UTF-8';

        $retorno = false;

        foreach ($arrArquivoAnexado as $arq)
        {
                $mail->AddAttachment($arq[0], $arq[1]);
        }

        if (_mailsmtp)
        {
                // envio smtp
                try {
                        $mail->SetFrom($remetente_email, $remetente_nome);
                        $mail->AddReplyTo($remetente_email, $remetente_nome);
                        $mail->IsHTML(true);
                        $mail->Subject = $assunto;
                        $mail->Body     = $texConteudo;
                        $mail->AltBody = $altConteudo;
                        $mail->IsSMTP();
                        $mail->Host     = _mailhost;
                        $mail->Port     = _mailport;
                        if (_mailuser!="")
                        {
                                $mail->SMTPAuth = true;
                                $mail->Username = _mailuser;
                                $mail->Password = _mailpass;		
                        }
                        foreach($destin as $dest)
                        {
                                $mail->AddAddress($dest["email"], $dest["nome"]);
                        }
                        $mail->Send();
                        $retorno = true;
                } catch (phpmailerException $e) {
                        echo $e->errorMessage();
                } catch (Exception $e) {
                        echo $e->getMessage();
                }
        } 
        else 
        {
                // envio simples
                try
                {
                        $mail->SetFrom($remetente_email, $remetente_nome);
                        $mail->AddReplyTo($remetente_email, $remetente_nome);
                        foreach($destin as $dest)
                        {
                                $mail->AddAddress($dest["email"], $dest["nome"]);
                        }
                        $mail->Subject    = $assunto;
                        $mail->AltBody    = $altConteudo;
                        $mail->MsgHTML($texConteudo);
                        $mail->Send();
                        $retorno = true;
                } catch (phpmailerException $e) {
                        echo $e->errorMessage(); 
                } catch (Exception $e) {
                        echo $e->getMessage();
                }
        }

        return $retorno;
}
	
	
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
	
	/*FUNÇÃO ÚTIL PARA DEBUG SEM  DIE*/
	function x($obj)
	{
		echo "<div style='background-color:#DFDFDF; border:1px #666666 solid'>";
			echo "<pre>";
				var_dump($obj);
			echo "</pre>";
		echo "</div>";
	}
	
	
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
        
        /**
         * Gera log no log de erros
         * @param type $string
         */
        function logPbl($string, $nivel=1)
{
    error_log(udate('Y-m-d H:i:s.u')." - ".$string);
}

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
    return substr(str_shuffle($senha),0,$tamanho);
}