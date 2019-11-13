<?php
/**
* Publicare - O CMS Público Brasileiro
* @description Classe Blob é responsável pela manipulação dos BLOBS
* @copyright GPL © 2007
* @package publicare
*
* MCTI - Ministério da Ciência, Tecnologia e Inovação - www.mcti.gov.br
* ANTT - Agência Nacional de Transportes Terrestres - www.antt.gov.br
* EPL - Empresa de Planejamento e Logística - www.epl.gov.br
* *
*
* Este arquivo é parte do programa Publicare
* Publicare é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU 
* como publicada pela Fundação do Software Livre (FSF); na versão 3 da Licença, ou (na sua opinião) qualquer versão.
* Este programa é distribuído na esperança de que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita 
* de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU para maiores detalhes.
* Você deve ter recebido uma cópia da Licença Pública Geral GNU junto com este programa, se não, veja <http://www.gnu.org/licenses/>.
*/

/**
 * blob.class.php
 * Classe responsável por gerenciar os BLOBs
 */
class Blob
{
    public $subacao;
    public $cod_blob;
    public $tipos_ver;
    public $tipos_baixar;
    public $tipos_classe;
    public $pasta_classes;
	
    /**
     * Método construtor, inicia as variaveis comuns para a classe
     */
    function __construct()
    {
        $this->pasta_classes = _BLOBDIR . "classes/";
        
        $this->tipos_classe = array("gif" => "image/gif",
            "png" => "image/png",
            "svg" => "image/svg+xml");
        
        // Extensoes dos arquivos que podem ser visualizados
        $this->tipos_ver = array(
            "jpg" => array("image/jpeg", true), 
            "jpeg" => array("image/jpeg", true),
            "png" => array("image/png", true), 
            "gif" => array("image/gif", true), 
            "svg" => array("image/svg+xml", false), 
            "svgz" => array("image/svg+xml", false), 
            "mp4" => array("video/mp4", false));
        
        // extensoes dos arquivos que podem ser baixados
        $this->tipos_baixar = array(
            "3gp" => "video/3gpp",
            "3g2" => "video/3gpp2",
            "7z" => "application/x-7z-compressed",
            "aac" => "audio/aac",
            "abw" => "application/x-abiword",
            "ai" => "application/postscript",
            "aif" => "audio/x-aiff",
            "aifc" => "audio/x-aiff",
            "aiff" => "audio/x-aiff",
            "arc" => "application/octet-stream",
            "asc" => "text/plain",
            "au" => "audio/basic",
            "avi" => "video/x-msvideo",
            "azw" => "application/vnd.amazon.ebook",
            "bin" => "application/octet-stream",
            "bmp" => "image/bmp",
            "bz" => "application/x-bzip",
            "bz2" => "application/x-bzip2",
            "cdr" => "application/cdr",
            "css" => "text/css",
            "csv" => "text/csv",
            "doc" => "application/msword",
            "dot" => "application/msword",
            "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            "dotx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.template",
            "docm" => "application/vnd.ms-word.document.macroEnabled.12",
            "dotm" => "application/vnd.ms-word.template.macroEnabled.12",
            "eot" => "application/vnd.ms-fontobject", 
            "eps" => "application/postscript",
            "epub" => "application/epub+zip",
            "gif" => "image/gif",
            "htm" => "text/html",
            "html" => "text/html",
            "ico" => "image/x-icon",
            "ics" => "text/calendar",
            "ief" => "image/ief",
            "jar" => "application/java-archive",
            "jpe" => "image/jpeg",
            "jpg" => "image/jpeg",
            "jpeg" => "image/jpeg",
            "js" => "application/javascript",
            "json" => "application/json",
            "kar" => "audio/midi",
            "m3u" => "audio/x-mpegurl",
            "mdb" => "application/vnd.ms-access",
            "mid" => "audio/midi",
            "midi" => "audio/midi",
            "mov" => "video/quicktime",
            "mp2" => "audio/mpeg",
            "mp3" => "audio/mpeg",
            "mpe" => "video/mpeg",
            "mpeg" => "video/mpeg",
            "mpg" => "video/mpeg",
            "mpga" => "audio/mpeg",
            "mpkg" => "application/vnd.apple.installer+xml",
            "odp" => "application/vnd.oasis.opendocument.presentation",
            "ods" => "application/vnd.oasis.opendocument.spreadsheet",
            "odt" => "application/vnd.oasis.opendocument.text",
            "oga" => "audio/ogg",
            "ogv" => "video/ogg",
            "ogx" => "application/ogg",
            "otf" => "font/otf",
            "png" => "image/png",
            "pdf" => "application/pdf",
            "pot" => "application/vnd.ms-powerpoint",
            "potm" => "application/vnd.ms-powerpoint.template.macroEnabled.12",
            "potx" => "application/vnd.openxmlformats-officedocument.presentationml.template",
            "ppa" => "application/vnd.ms-powerpoint",
            "ppam" => "application/vnd.ms-powerpoint.addin.macroEnabled.12",
            "pps" => "application/vnd.ms-powerpoint",
            "ppsm" => "application/vnd.ms-powerpoint.slideshow.macroEnabled.12",
            "ppsx" => "application/vnd.openxmlformats-officedocument.presentationml.slideshow",
            "ppt" => "application/vnd.ms-powerpoint",
            "pptm" => "application/vnd.ms-powerpoint.presentation.macroEnabled.12",
            "pptx" => "application/vnd.openxmlformats-officedocument.presentationml.presentation",
            "ps" => "application/postscript",
            "qt" => "video/quicktime",
            "ra" => "audio/x-realaudio",
            "ram" => "audio/x-pn-realaudio",
            "rar" => "application/x-rar-compressed",
            "rm" => "audio/x-pn-realaudio",
            "rpm" => "audio/x-pn-realaudio-plugin",
            "rtf" => "application/rtf",
            "rtx" => "text/richtext",
            "sh" => "application/x-sh",
            "snd" => "audio/basic",
            "svg" => "image/svg+xml",
            "swf" => "application/x-shockwave-flash",
            "tar" => "application/x-tar",
            "tif" => "image/tiff",
            "tiff" => "image/tiff",
            "ts" => "application/typescript",
            "ttf" => "font/ttf",
            "txt" => "text/plain",
            "vsd" => "application/vnd.visio",
            "wav" => "audio/x-wav",
            "wbmp" => "image/vnd.wap.wbmp",
            "wbxml" => "application/vnd.wap.wbxml",
            "weba" => "audio/webm",
            "webm" => "video/webm",
            "webp" => "image/webp",
            "wmlc" => "application/vnd.wap.wmlc",
            "wmlsc" => "application/vnd.wap.wmlscriptc",
            "woff" => "font/woff",
            "woff2" => "font/woff2",
            "xhtml" => "application/xhtml+xml",
            "xht" => "application/xhtml+xml",
            "xla" => "application/vnd.ms-excel",
            "xlam" => "application/vnd.ms-excel.addin.macroEnabled.12",
            "xls" => "application/vnd.ms-excel",
            "xlt" => "application/vnd.ms-excel",
            "xlsb" => "application/vnd.ms-excel.sheet.binary.macroEnabled.12",
            "xlsm" => "application/vnd.ms-excel.sheet.macroEnabled.12",
            "xltm" => "application/vnd.ms-excel.template.macroEnabled.12",
            "xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "xltx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.template",
            "xml" => "application/xml",
            "xul" => "application/vnd.mozilla.xul+xml",
            "zip" => "application/zip");
    }
    
    /**
     * Exibe blobs imagens internos do PBL
     * @param object $_page - Referência de objeto da classe Pagina
     * @param integer $cod_blob - Codigo do blob
     */
    function VerBlobInterno(&$_page, $cod_blob)
    {   
        $filetype = NULL;
        
        $largura = isset($_GET["w"])?(is_numeric($_GET["w"])?$_GET["w"]:"0"):"0";
        $altura = isset($_GET["h"])?(is_numeric($_GET["h"])?$_GET["h"]:"0"):"0";
        
        foreach ($this->tipos_ver as $ext=>$type)
        {
            if (file_exists(_PUBLICAREPATH . '/includes/imagens/' . $cod_blob . '.' . $ext))
            {
                $filetype = $ext;
                break;
            }
        }

        if (is_null($filetype)) $_page->headerHtml(404, "Arquivo não encontrado");

        $arquivo = _PUBLICAREPATH . '/includes/imagens/' . $cod_blob . '.' . $filetype;
        
//        xd(_PUBLICAREPATH . '/includes/imagens/' . $cod_blob . '.' . $filetype);
        
        if (!file_exists($arquivo)) $_page->headerHtml(404, "Arquivo não encontrado");

//        $conteudo = file_get_contents($arquivo);
        $tamanho = filesize($arquivo);
        $crc = crc32($arquivo);
        $etag = "pbl-blob-".$crc.".".$tamanho;
        
        $etagEncontrada = isset($_SERVER["HTTP_IF_NONE_MATCH"]) ? stripslashes($_SERVER["HTTP_IF_NONE_MATCH"]) : "";
        if (strstr($etagEncontrada, $etag)) 
        {
            http_response_code(304);
            header('Cache-Control: public');
            header("ETag: ".$etag);
//            header('Cache-Control: max-age='.(60*60*24*7));
        }
        else
        {
            header("ETag: ".$etag);
            header('Cache-Control: public');
            header('Cache-Control: max-age='.(60*60*24*7));
            header('Content-Type: '.$this->tipos_ver[$filetype][0]);
            print file_get_contents($arquivo);
        }

        exit(0);
    }
    
    /**
     * Exibe blobs imagens
     * @param object $_page - Referência de objeto da classe Pagina
     * @param integer $cod_blob - Codigo do blob
     */
    function VerBlob(&$_page, $cod_blob)
    {   
        $filetype = NULL;
        
        $largura = isset($_GET["w"])?(is_numeric($_GET["w"])?$_GET["w"]:"0"):"0";
        $altura = isset($_GET["h"])?(is_numeric($_GET["h"])?$_GET["h"]:"0"):"0";
        
        foreach ($this->tipos_ver as $ext=>$type)
        {
//        xd(_BLOBDIR);
            if (file_exists(_BLOBDIR . '/' . Blob::identificaPasta($cod_blob) . '/' . $cod_blob . '.' . $ext))
            {
                $filetype = $ext;
                break;
            }
        }
        

        if (is_null($filetype)) $_page->headerHtml(404, "Arquivo não encontrado");
        $cod_objeto = $this->CodigoObjeto($_page, $cod_blob);
        $cod_objeto = $cod_objeto["cod"];
        
        
        if (!$_page->_adminobjeto->estaSobAreaProtegida($_page, $cod_objeto)) $_page->headerHtml(403, "Acesso não permitido");

        $arquivo = $this->VerificaCache($_page, $cod_blob, $filetype, $largura, $altura);
//        xd($arquivo);
        
        if (!file_exists($arquivo)) $_page->headerHtml(404, "Arquivo não encontrado");

//        $conteudo = file_get_contents($arquivo);
        $tamanho = filesize($arquivo);
        $crc = crc32($arquivo);
        $etag = "pbl-blob-".$crc.".".$tamanho;
        
        $etagEncontrada = isset($_SERVER["HTTP_IF_NONE_MATCH"]) ? stripslashes($_SERVER["HTTP_IF_NONE_MATCH"]) : "";
        if (strstr($etagEncontrada, $etag)) 
        {
            http_response_code(304);
            header('Cache-Control: public');
            header("ETag: ".$etag);
//            header('Cache-Control: max-age='.(60*60*24*7));
        }
        else
        {
            header("ETag: ".$etag);
            header('Cache-Control: public');
            header('Cache-Control: max-age='.(60*60*24*7));
            header('Content-Type: '.$this->tipos_ver[$filetype][0]);
            print file_get_contents($arquivo);
        }

        exit(0);
    }
    
    /**
     * Realiza download de arquivos
     * @param object $_page - Referência de objeto da classe Pagina
     * @param integer $cod_blob - Codigo do blob
     */
    function BaixarBlob(&$_page, $cod_blob)
    {
        $subpasta = Blob::identificaPasta($cod_blob);
        
        $dados_objeto = $this->CodigoObjeto($_page, $cod_blob);
	$filename = $dados_objeto["nome"];
	$cod_objeto = $dados_objeto["cod"];
        
        $file_ext = Blob::PegaExtensaoArquivo($filename);
        
        if ($_page->_adminobjeto->estaSobAreaProtegida($_page, $cod_objeto))
	{
            if (!defined("_BLOBDIR"))
            {
                $nao_compactar = array ('zip', 'jpg');
                if (in_array ($file_ext, $nao_compactar)) $data = $rs->fields['valor'];
                else $data = gzuncompress($rs->fields['valor']);
            }
            else
            {
                if (isset($this->tipos_baixar[$file_ext]))
                {
                    if (file_exists(_BLOBDIR.$subpasta."/".$cod_blob.'.'.$file_ext))
                    {
                        $size = filesize(_BLOBDIR.$subpasta."/".$cod_blob.'.'.$file_ext);
                        $nome = limpaString($filename);
                        $nome = substr($nome, 0, strlen($nome)-strlen($file_ext)).".".$file_ext;
                        
                        header("Content-length: $size");
                        header("Content-Disposition: attachment; filename=".$nome."");
                        header("Content-type: $content_type[$file_ext]");
                        
                        $this->readfile_chunked(_BLOBDIR.$subpasta."/".$cod_blob.'.'.$file_ext);
                        
                        exit(0);
                    }
                    else
                    {
                        $_page->headerHtml(404, "Arquivo não encontrado"); //die("Arquivo não encontrado.");
                    }
                }
                else
                {
                    $_page->headerHtml(403, "Tipo de arquivo com download não permitido"); //die("Tipo de arquivo com download não permitido.");
                }
            }
        }
        else
        {
            $_page->headerHtml(403, "Acesso não permitido"); //die("Acesso ao arquivo não permitido para o perfil.");
        }
    }
    
    /**
     * Retorna código do objeto dono do blob
     * @param object $_page - Referência de objeto da classe Pagina
     * @param integer $cod_blob - Codigo do blob
     * @return integer - Codigo do objeto
     */
    private function CodigoObjeto(&$_page, $cod_blob)
    {
        $sql = 'select cod_objeto, arquivo from tbl_blob where cod_blob='.$cod_blob;
	$rs = $_page->_db->ExecSQL($sql);
	return array("cod"=>$rs->fields['cod_objeto'], "nome"=>$rs->fields['arquivo']);
    }
    
    /**
     * Verifica existência de pasta para gravação de cache de imagens, 
     * cria a pasta caso não exista.
     * Verifica existência do arquivo de cache já no tamanho especificado. 
     * Caso exista retorna arquivo cache, caso nao exista cria arquivo e retorna.
     * @param integer $cod_blob - Codigo do blob
     * @param string $ext - Extensao do arquivo
     * @param integer $largura - Largura da imagem
     * @param integer $altura - Altura da imagem
     * @return string - PATH do arquivo
     */
    function VerificaCache(&$_page, $cod_blob, $ext, $largura, $altura)
    {
        $endereco = _BLOBDIR . "/" . Blob::identificaPasta($cod_blob) . "/" . $cod_blob . "." . $ext;
        
        if ($largura != "0" || $altura != "0")
        {
            if ($this->tipos_ver[$ext][1])
            {
                $endereco_original = $endereco;
                $pasta = _BLOBDIR . '/cache/' . Blob::identificaPasta($cod_blob) . '/';
                $pasta = preg_replace("[\/\/]", "/", $pasta);
                $endereco = $pasta . $cod_blob . "_" . $largura . "_" . $altura . "." . $ext;
                if (!file_exists($pasta)) mkdir($pasta, 0770, true);
                
                if (!file_exists($endereco))
                {
                    $im = null;
                    $newim = null;
                    switch ($ext)
                    {
                        case "jpg":
                        case "jpeg":
                            $im = imagecreatefromjpeg($endereco_original);
                            break;
                        case "png":
                            $im = imagecreatefrompng($endereco_original);
                            break;
                        case "gif":
                            $im = imagecreatefromgif($endereco_original);
                            break;
                    }
                    if (!is_null($im))
                    {
                        $x = ImageSX($im);
                        $y = ImageSY($im);
                        $width = $x;
                        $height = $y;
                        if ($largura != "0" && $altura != "0")
                        {
                            $width = $largura;
                            $height = $altura;
                        }
                        elseif ($largura != "0")
                        {
                            $width = $largura;
                            $height = ceil($largura * $y / $x);
                        }
                        else
                        {
                            $height = $altura;
                            $width = ceil($altura * $x / $y);
                        }
                        
                        
                        if ($ext == "jpg" || $ext == "jpeg") $newim = ImageCreateTrueColor($width, $height);
                        else $newim = ImageCreate($width, $height);
                        ImageCopyResized($newim, $im, 0, 0, 0, 0, $width, $height, $x, $y);
                        
                        imagedestroy($im);
                        
                        switch ($ext)
                        {
                            case "jpg":
                            case "jpeg":
                                imagejpeg($newim, $endereco);
                                break;
                            case "png":
                                imagepng($newim, $endereco);
                                break;
                            case "gif":
                                imagegif($newim, $endereco);
                                break;
                        }
                        
                        imagedestroy($newim);
                    }
                }
            }
        }
        
        $endereco = preg_replace("[\/\/]", "/", $endereco);
        return $endereco;
    }
    
    /**
     * Identifica subpasta onde ficam armazenados os arquivos, conforme código do blob
     * @param integer $codigo_blob
     * @return string - Nome da pasta
     */
    static function identificaPasta($codigo_blob)
    {
        $ret = false;
        $tamanho = strlen($codigo_blob);
        if ($codigo_blob+0)
        {
            if ($tamanho<=3)
            {
                $ret="0000";
            }
            else
            {
                $ret = (int)($codigo_blob/1000);
            }
            for ($i=strlen($ret); $i<4; $i++) $ret="0".$ret;
        }
        // cria a pasta caso não exista
//        xd(_BLOBDIR.$ret);
        if (!is_dir(_BLOBDIR.$ret))
        {
            mkdir(_BLOBDIR.$ret, 0755);
        }
        return $ret;
    }
    
    /**
     * Grava os arquivos em disco
     * @param object $_page - Referncia do objeto $_page
     * @param array $file - Array com os dados do arquivo
     * @param integer $cod_blob - Código do blob
     * @return boolean
     */
    function gravarBlob(&$_page, $file, $cod_blob)
    {
        $pasta = _BLOBDIR.Blob::identificaPasta($cod_blob)."/";
        $nome_original = $file["name"];
        $nome_temp = $file["tmp_name"];
        $extensao = Blob::PegaExtensaoArquivo($nome_original);
        $nome_final = $cod_blob.".".$extensao;
        
        $this->verificaExistenciaPasta($pasta);
        
        return copy($nome_temp, $pasta.$nome_final);
    }
    
    /**
     * Grava ícone da classe
     * @param object $_page - Referncia do objeto $_page
     * @param array $file - Referencia do $_FILE
     * @param string $prefixo - Prefixo da classe
     */
    function gravarIconeClasse(&$_page, $file, $prefixo)
    {
        $pasta = $this->pasta_classes;
        $nome_original = $_FILES["ic_classe"]["name"];
        $nome_temp = $_FILES["ic_classe"]["tmp_name"];
        $extensao = Blob::PegaExtensaoArquivo($nome_original);
        $nome_final = "ic_".$prefixo.".".$extensao;
        
        $this->verificaExistenciaPasta($pasta);
        
        foreach ($this->tipos_classe as $ext=>$mime)
        {
            if ($ext == $extensao)
            {
                copy($nome_temp, $pasta.$nome_final);
                break;
            }
        }
    }
    
    function apagaBlob(&$_page, $cod_blob, $arquivo)
    {
        $file_ext = Blob::PegaExtensaoArquivo($arquivo);
        if (file_exists(_BLOBDIR.Blob::identificaPasta($cod_blob)."/".$cod_blob.'.'.$file_ext))
        {
            $checkDelete = unlink(_BLOBDIR.Blob::identificaPasta($cod_blob)."/".$cod_blob.'.'.$file_ext);
        }

//                            if (defined ("_THUMBDIR"))
//                            {
//                                if (file_exists(_THUMBDIR.$row['cod_blob'].'.'.$file_ext)) unlink(_THUMBDIR.$row['cod_blob'].'.'.$file_ext);
//                            }
    }
    
    /**
     * Apaga arquivo cache do ícone da clase
     * @param object $_page - Referncia do objeto $_page
     * @param string $prefixo - Prefixo da classe
     */
    function apagaIconeClasse(&$_page, $prefixo)
    {
        $caminho = $this->pasta_classes;
        $nome = "ic_" . $prefixo;
        
        $retorno = $this->verificaExistenciaArquivoPasta($caminho, $nome, $this->tipos_classe);
        if (is_array($retorno) && isset($retorno["arquivo"]) && $retorno["arquivo"] != "")
        {
            unlink($retorno["caminho"].$retorno["arquivo"]);
        }
    }
    
    private function verificaExistenciaPasta($pasta)
    {
        if (!is_dir($pasta)) mkdir($pasta, 0755, true);
    }
    
    function verificaExistenciaArquivoPasta($pasta, $nome, $tipos, $destino="", $default=false)
    {
        $retorno = array("arquivo" => "",
            "caminho" => "", 
            "extensao" => "",
            "default" => "");
        
        foreach ($tipos as $ext=>$mime)
        {
            $nome_temp = $nome . "." . $ext;
            if (file_exists($pasta . $nome_temp))
            {
                $retorno["arquivo"] = $nome_temp;
                $retorno["extensao"] = $ext;
                $retorno["default"] = $default;
                $retorno["caminho"] = $pasta;
                if ($destino != "")
                {
                    copy($pasta.$nome_temp, $destino.$nome_temp);
                    $retorno["caminho"] = $destino;
                }
                break;
            }
        }
        
        return $retorno;
        
    }
    
    /**
     * Verifica existencia de arquivo imagem para icone de classe
     * @param string $prefixo - prefixo da classe
     * @return string
     */
    function verificaExistenciaIconeClasse(&$_page, $prefixo)
    {
        $retorno = array();
        
        // pasta onde o arquivo deve ficar, no site
        $pasta_destino = $this->pasta_classes;
        
        $this->verificaExistenciaPasta($pasta_destino);
        
        // nome do arquivo de icone da classe
        $nome = "ic_" . $prefixo;
        $pasta = "";
        
        // verifica se existe o arquivo na pasta do site
        $retorno = $this->verificaExistenciaArquivoPasta($pasta_destino, $nome, $this->tipos_classe);
        
        // se o arquivo nao existir na pasta do site, procura na pasta de icones do publicare
        if (!isset($retorno["arquivo"]) || $retorno["arquivo"]=="")
        {
            $pasta = _PUBLICAREPATH . "/blobs/classes/";
            $retorno = $this->verificaExistenciaArquivoPasta($pasta, $nome, $this->tipos_classe, $pasta_destino);
        }
        
        // caso continue sem encontrar, procura pelo ícone genérico na pasta do site
        if (!isset($retorno["arquivo"]) || $retorno["arquivo"]=="")
        {
            $nome = "ic_default";
            $retorno = $this->verificaExistenciaArquivoPasta($pasta_destino, $nome, $this->tipos_classe, "", true);
        }
        
        // caso continue sem encontrar, como ultima tentativa, vai buscar o icone genérico na pasta do publicare
        if (!isset($retorno["arquivo"]) || $retorno["arquivo"]=="")
        {
            $nome = "ic_default";
            $pasta = _PUBLICAREPATH . "/blobs/classes/";
            $retorno = $this->verificaExistenciaArquivoPasta($pasta, $nome, $this->tipos_classe, $pasta_destino, true);
        }
        
        return $retorno;
    }
    
    /**
     * Verifica existencia de arquivo imagem para icone de blobs
     * @param string $extensao - extensao do blob
     * @return string
     */
    function verificaExistenciaIconeBlob(&$_page, $extensao)
    {
        $retorno = array();
        $extensoes = array("png", "gif", "svg");
        $caminho = "";
        $nome = "";
        
//        xd($_SERVER['DOCUMENT_ROOT']);
        
        if (!is_dir(_BLOBDIR . "blobs/"))
        {
            mkdir(_BLOBDIR . "blobs/", 0755, true);
        }
        
        // procura na pasta /html/imagens do portal
        foreach ($extensoes as $ext)
        {
            $nome = "icnx_" . $extensao . "." . $ext;
            // caso encontre na pasta do portal
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/html/imagens/" . $nome))
            {
                $caminho = $_SERVER['DOCUMENT_ROOT'] . "/html/imagens/" . $nome;
                $retorno["arquivo"] = $nome;
                $retorno["caminho"] = $caminho;
                $retorno["default"] = false;
                break;
            }
        }
        
        // caso não encontre na pasta /html/imagens do portal
        // procura na pasta upd_blob/blobs
        if ($caminho == "")
        {
            foreach ($extensoes as $ext)
            {
                $nome = "icnx_" . $extensao . "." . $ext;
                // caso encontre na pasta do portal
                if (file_exists(_BLOBDIR . "/blobs/" . $nome))
                {
                    $caminho = _BLOBDIR . "/blobs/" . $nome;
                    $retorno["arquivo"] = $nome;
                    $retorno["caminho"] = $caminho;
                    $retorno["default"] = false;
                    break;
                }
            }
        }
        
        // caso não encontre na pasta do portal
        if ($caminho == "")
        {
            // procura na pasta do publicare
            foreach ($extensoes as $ext)
            {
                $nome = "icnx_" . $extensao . "." . $ext;
                // caso encontre na pasta do publicare
                if (file_exists(_PUBLICAREPATH . "/imagens/blobs/" . $nome))
                {
                    // copia da pasta do publicare para a pasta do portal
                    copy(_PUBLICAREPATH."/imagens/blobs/".$nome, _BLOBDIR."/blobs/".$nome);
                    $caminho = _BLOBDIR . "/blobs/" . $nome;
                    $retorno["arquivo"] = $nome;
                    $retorno["caminho"] = $caminho;
                    $retorno["default"] = false;
                    break;
                }
            }
        }
        
        // caso continue sem localizar procura pelo icone generico na pasta do portal
        if ($caminho == "")
        {
            foreach ($extensoes as $ext)
            {
                $nome = "icnx_generic." . $ext;
                // caso encontre na pasta do portal
                if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/html/imagens/" . $nome))
                {
                    $caminho = $_SERVER['DOCUMENT_ROOT'] . "/html/imagens/" . $nome;
                    $retorno["arquivo"] = $nome;
                    $retorno["caminho"] = $caminho;
                    $retorno["default"] = true;
                    break;
                }
            }
        }
        
        // caso continue sem localizar procura pelo icone generico na pasta do portal
        if ($caminho == "")
        {
            foreach ($extensoes as $ext)
            {
                $nome = "icnx_generic." . $ext;
                // caso encontre na pasta do portal
                if (file_exists(_BLOBDIR . "/blobs/" . $nome))
                {
                    $caminho = _BLOBDIR . "/blobs/" . $nome;
                    $retorno["arquivo"] = $nome;
                    $retorno["caminho"] = $caminho;
                    $retorno["default"] = true;
                    break;
                }
            }
        }
        
        // caso continue sem localizar, como ultima tentativa 
        // procura icone generico no publicare
        if ($caminho == "")
        {
            foreach ($extensoes as $ext)
            {
                $nome = "icnx_generic." . $ext;
                // caso encontre na pasta do portal
                if (file_exists(_PUBLICAREPATH . "/imagens/blobs/" . $nome))
                {
                    copy(_PUBLICAREPATH."/imagens/blobs/".$nome, _BLOBDIR."/blobs/".$nome);
                    $caminho = _BLOBDIR . "/blobs/" . $nome;
                    $retorno["arquivo"] = $nome;
                    $retorno["caminho"] = $caminho;
                    $retorno["default"] = true;
                    break;
                }
            }
        }
        
        return $retorno;
    }
    
    /**
     * Exibe o ícone da classe. Primeiro busca no portal, caso não encontre busca
     * na pasta publicare.
     * @param string $prefixo - Prefixo da classe
     */
    function IconeClasse(&$_page, $prefixo)
    {
        $icone = $this->verificaExistenciaIconeClasse($_page, $prefixo);
        
        // Caso tenha encontrado a imagem em algum lugar, exibe a mesma
        if (is_array($icone) && count($icone)>0 && $icone["caminho"]!="")
        {
            header('Content-Type: '.$this->tipos_ver[$icone["extensao"]][0]);
            print file_get_contents($icone["caminho"].$icone["arquivo"]);
        }
        exit(0);
    }
    
    function IconeBlob(&$_page, $prefixo)
    {
        $icone = $this->verificaExistenciaIconeBlob($_page, $prefixo);
        
        // Caso tenha encontrado a imagem em algum lugar, exibe a mesma
        if (is_array($icone) && count($icone)>0 && $icone["caminho"]!="")
        {
            $extensao = Blob::PegaExtensaoArquivo($icone["caminho"]);
//            header('Cache-Control: max-age=86400');
            header('Content-Type: '.$this->tipos_ver[$extensao][0]);
            print file_get_contents($icone["caminho"]);
        }
        exit(0);
    }
    
    /**
    * Retorna extensão do arquivo
    * @param string $nome - nome completo do arquivo
    * @return string - extensão
    */
   static function PegaExtensaoArquivo($nome)
   {
       $filetype = "";
       if ($nome && !empty($nome) && $nome!="")
       {
           $arrNome = preg_split("[\.]", $nome);
           $filetype = strtolower($arrNome[count($arrNome)-1]);
       }
       return $filetype;
   }
   
   /**
    * Lê e coloca no buffer de saído arquivo informado, por partes byte streamming
    * @param string $filename - Nome com path do arquivo
    * @param boolean $retbytes - Retorna os bytes lidos ou não
    * @return boolean|bytes
    */
    private function readfile_chunked($filename, $retbytes=true)
    {
        $chunksize = 1*(1024*1024); // tamanho dos pedaços
        $buffer = '';
        $cnt = 0;
        $handle = fopen($filename, 'rb');
        if ($handle === false) return false;
        while (!feof($handle))
        {
            $buffer = fread($handle, $chunksize);
            echo $buffer;
            ob_flush();
            flush();
            if ($retbytes)
            {
                $cnt += strlen($buffer);
            }
        }
        $status = fclose($handle);
        if ($retbytes && $status) return $cnt;
        return $status;
    } 
    
    /**
    * Gera imagem para capa de PDF, para utilização com page flipper
    * @param type $_page
    * @param type $cod_objeto
    * @param type $prop_pdf
    * @param type $prop_capa
    * @return boolean
    */
   static function geraImagemCapaPDF(&$_page, $cod_objeto, $prop_pdf, $prop_capa)
   {
   //    error_log("Gerando imagem - Objeto: " . $cod_objeto . " - PDF: " . $prop_pdf . " - IMG: " . $prop_capa);
       // carregando objeto
       $objeto = new Objeto($_page, $cod_objeto);
       // carregando propriedades
       $objeto->Valor($_page, $prop_pdf);
       // definindo variaveis como path e nomes dos arquivos
       $arquivo = $objeto->propriedades[$prop_pdf]["cod_blob"] . "." . $objeto->propriedades[$prop_pdf]["tipo_blob"];
       $nome_original = $objeto->propriedades[$prop_pdf]["valor"];
       $vnome = preg_split("[\.]", strtolower($nome_original));
       $nome_original_capa = "capa_".$vnome[0].".jpg";
       $arquivo_temp = "temp_" . $objeto->propriedades[$prop_pdf]["cod_blob"] . ".jpg";
   //    Blob::identificaPasta($codigo_blob)
       $path_arquivo = _BLOBDIR . Blob::identificaPasta($objeto->propriedades[$prop_pdf]["cod_blob"]) . "/";
       $cod_classe = $objeto->Valor($_page, "cod_classe");
       // usando gosthScript para gerar JPG da parimeira página do PDF
       $comando = "gs -sDEVICE=jpeg -dJPEGQ=75 -quiet -dSAFER -dBATCH -dNOPAUSE -dFirstPage=1 -dLastPage=1 -sOutputFile=".$path_arquivo.$arquivo_temp." ".$path_arquivo.$arquivo;
       $msg = shell_exec($comando);
       // se tiver ocorrido algum erro joga no log do apache
       if (preg_match('/error/i', $msg)) {
           error_log('Ocorreu um erro gerando JPG! ' . $msg);
           return false;
       }
       // pega infos da propriedade
       $info = $_page->_adminobjeto->PegaInfoSobrePropriedade($_page, $cod_classe, $prop_capa);

       if ($info && is_array($info))
       {

           if (!is_null($objeto->propriedades[$prop_capa]["cod_blob"]))
           {
               @unlink(_BLOBDIR . Blob::identificaPasta($objeto->propriedades[$prop_capa]["cod_blob"]) . "/" . $objeto->propriedades[$prop_capa]["cod_blob"] . "." . $objeto->propriedades[$prop_capa]["tipo_blob"]);
               $sql = "delete from tbl_blob where cod_blob=".$objeto->propriedades[$prop_capa]["cod_blob"];
               $_page->_db->ExecSQL($sql);
   //            xd($objeto->propriedades[$prop_capa]["tipo_blob"]);
           }

           $campos = array();
           $campos['cod_propriedade'] = (int)$info['cod_propriedade'];
           $campos['cod_objeto'] = (int)$objeto->Valor($_page, "cod_objeto");
           $campos['arquivo'] = $nome_original_capa;
           $campos['tamanho'] = filesize($path_arquivo . $arquivo_temp);
           $name = $_page->_db->Insert($info['tabela'], $campos);
           $filetype = Blob::PegaExtensaoArquivo($arquivo_temp);

           $subpasta = Blob::identificaPasta($name);  //Pega o nome da subpasta
           if (!$resultado = is_dir(_BLOBDIR . "/" . $subpasta . "/"))
           {
               mkdir(_BLOBDIR . "/" . $subpasta, 0755); //cria a pasta
           }

           copy($path_arquivo . $arquivo_temp, _BLOBDIR . "/" . $subpasta . "/" . $name . "." . $filetype);

           $im = imagecreatefromjpeg(_BLOBDIR . "/" . $subpasta . "/" . $name . "." . $filetype);
           $x = ImageSX($im);
           $y = ImageSY($im);
           $width = _THUMBWIDTH;
           $height = ceil(_THUMBWIDTH * $y / $x);
           $newim = ImageCreateTrueColor($width, $height);
           ImageCopyResized($newim, $im, 0, 0, 0, 0, $width, $height, $x, $y);
           $im = $newim;
           ImageJpeg($im, _THUMBDIR.$name.'.'.$filetype, 100);
       }

       unlink($path_arquivo.$arquivo_temp);

       $_page->_administracao->cacheFlush($_page);
   }

}

