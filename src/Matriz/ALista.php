<?php
namespace Pbl\Matriz;

use IteratorAggregate;

use Pbl\Matriz\Cursor;

// incluir_classe(CAMINHO_PHP_LIBRARY . 'generico/GenObjeto.php');
// incluir_classe(CAMINHO_PHP_LIBRARY . 'utilidades/matriz/Cursor.php');

abstract class ALista implements IteratorAggregate {

	private $dados = array();
	private $cont = 0;

	public function __destruct() {
		unset($this->dados);
	}
	
	public function getIterator() {
		return(new Cursor($this->dados));
	}
	
	public function getItems() {
		return($this->dados);
	}
	
	public function getItem($id) {
		return($this->dados[$id]);
	}
	
	protected function add($intId, $mxdValor) {
		//   TODO: Implementar assim no futuro
		//   $this->mxdArrDados[$this->intCont][0] = $intId;
		//   $this->mxdArrDados[$this->intCont][1] = $mxdValor;
	
		$this->mxdArrDados[$this->intCont] = $mxdValor;
		$this->intCont++;
	}
	
	public function __toString() {
		$strRet = '';
		$intI   = 0;
		
		if(count($this->mxdArrDados) > 0) {
			for($intI = 0; $intI < count($this->mxdArrDados); $intI++) {
				if(is_object($this->mxdArrDados[$intI])) {
					$strRet .= $this->mxdArrDados[$intI]->__toString() . '!';
				} else {
					$strRet .= (string) $this->mxdArrDados[$intI] . '!';
				}
			}
			
			$strRet = substr($strRet, 0, strlen($strRet) - 1);
		}
				
		return $strRet;
	}
	//Declaracao de metodos ----------------------------------------------------
}
?>