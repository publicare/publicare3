<?php
namespace Pbl\Matriz;

// incluir_classe(CAMINHO_PHP_LIBRARY . 'generico/GenObjeto.php');

class Cursor implements Iterator {

    private $mxdArrDados = array();

    public function __construct($mxdArrDados) {
        if (is_array($mxdArrDados) ) {
            $this->mxdArrDados = $mxdArrDados;
        }
    }

    public function __destruct() {
        unset($this->mxdArrDados);
    }
  
    public function rewind() {
        reset($this->mxdArrDados);
    }

    public function current() {
        $mxdArrDados = current($this->mxdArrDados);
        return $mxdArrDados;
    }

    public function key() {
        $mxdArrDados = key($this->mxdArrDados);
        return $mxdArrDados;
    }

    public function next() {
        $mxdArrDados = next($this->mxdArrDados);
        return $mxdArrDados;
    }

    public function valid() {
        $mxdArrDados = $this->current() !== false;

   return $mxdArrDados;
  }
  
  //TODO: Implementar hasNext()
}
?>