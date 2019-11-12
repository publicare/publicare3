<?php
/**
* Publicare - O CMS Público Brasileiro
* @description Classe AdminObjeto é responsável pela manipulação dos objetos por parte dos internautas
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

class Rss
{
	
	private $_cod_pai;
	private $_total;
	private $_encoding="UTF-8";
	private $_title="";
	private $_language="pt-br";
	private $_description="";
	private $_link="";
	private $_generator="rss";
	private $_version="2.0";
	private $_items=array();
	private $_indice=0;
	private $_copyright;
	private $_categoria="Noticias";
		
	public function __construct($title="") {
		$this->_title=$title;
	}
		
	public function __get($name) {
		if ($name=='encoding') return $this->_encoding;
		if ($name=='title') return $this->_title;
		if ($name=='language') return $this->_language;
		if ($name=='description') return $this->_description;
		if ($name=='generator') return $this->_generator;
		if ($name=='link') return $this->_link;
		if ($name=='cod_pai') return $this->_cod_pai;
		if ($name=='total') return $this->_total;
		if ($name=='copyright') return $this->_copyright;
		if ($name=='categoria') return $this->_categoria;
	}
		
	public function __set($name,$value) {
		if ($name=='encoding') $this->_encoding = stripslashes($value);
		if ($name=='title') $this->_title = stripslashes($value);
		if ($name=='language') $this->_language = stripslashes($value);
		if ($name=='description') $this->_description = stripslashes($value);
		if ($name=='generator') $this->_generator = stripslashes($value);
		if ($name=='link') $this->_link = stripslashes($value);
		if ($name=='cod_pai') $this->_cod_pai = stripslashes($value);
		if ($name=='total') $this->_total = stripslashes($value);
		if ($name=='copyright') $this->_copyright = stripslashes($value);
		if ($name=='categoria') $this->_categoria = stripslashes($value);
	}
		
	public function addItem($title, $description, $link, $pubDate)
	{
		$this->_items[$this->_indice]['title'] = $title;
		$this->_items[$this->_indice]['description'] = $description;
		$this->_items[$this->_indice]['link'] = $link;
		$this->_items[$this->_indice]['pubDate'] = $pubDate;
		$this->_indice++;
	}
		
	public function showRSS() 
	{
		// header
		$res="<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n";
		$res.="\t<channel>\n";
		$res.="\t\t<atom:link href=\"http://agenciact.mct.gov.br/rss\" rel=\"self\" type=\"application/rss+xml\" />\n";
		$res.="\t\t<title><![CDATA[".$this->_title."]]></title>\n";
		$res.="\t\t<description><![CDATA[".$this->_description."]]></description>\n";
		$res.="\t\t<link>".$this->_link."</link>\n";
		$res.="\t\t<language><![CDATA[".$this->_language."]]></language>\n";
		$res.="\t\t<copyright><![CDATA[Copyright ".date("Y")." ".$this->_copyright."]]></copyright>\n";
		$res.="\t\t<category><![CDATA[".$this->_categoria."]]></category>\n";
		
		//items
		foreach($this->_items as $item) {
			//$date = date("r", stripslashes($item["pubDate"]));
			$res.="\t\t<item>\n";
			$res.="\t\t\t<title><![CDATA[".stripslashes($item["title"])."]]></title>\n";
			$res.="\t\t\t<description><![CDATA[".stripslashes($item["description"])."]]></description>\n";
			if (!empty($item["pubDate"])) $res.="\t\t\t<pubDate>".stripslashes($item["pubDate"])."</pubDate>\n";
			if (!empty($item["link"])) $res.="\t\t\t<link>".stripslashes($item["link"])."</link>\n";
			$res.="\t\t</item>\n";
		}
			
		//footer
		$res.="\t</channel>\n";
		$res.="</rss>\n";
		return $res;
	}
	
}

?>
